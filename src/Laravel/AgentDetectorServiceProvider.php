<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Laravel;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\AgentDetector\AgentDetector;
use Laravel\AgentDetector\Http\TrafficClassifier;
use Laravel\AgentDetector\Http\TrafficResult;
use Laravel\AgentDetector\Laravel\Middleware\AddTrafficContext;
use Laravel\AgentDetector\Laravel\Middleware\PreventAiTraffic;
use Symfony\Component\HttpFoundation\Response;

class AgentDetectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TrafficClassifier::class);
    }

    public function boot(): void
    {
        $this->registerRequestMacros();
        $this->registerResponseMacros();
        $this->registerBladeDirectives();
        $this->registerMiddlewareAliases();
        $this->registerAboutCommand();
    }

    protected function registerRequestMacros(): void
    {
        Request::macro('traffic', fn (): TrafficResult => Traffic::for($this));
        Request::macro('isAgent', fn (): bool => Traffic::for($this)->isAgent());
        Request::macro('isAiCrawler', fn (): bool => Traffic::for($this)->isAiCrawler());
        Request::macro('isAiTraffic', fn (): bool => Traffic::for($this)->isAi());
        Request::macro('isBot', fn (): bool => Traffic::for($this)->isAutomated());
        Request::macro('prefersMarkdown', fn (): bool => TrafficClassifier::prefersMarkdown($this->headers->get('Accept')));
    }

    protected function registerResponseMacros(): void
    {
        ResponseFactory::macro('markdown', fn (string $markdown, int $status = 200, array $headers = []): Response => new \Illuminate\Http\Response($markdown, $status, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Vary' => 'Accept',
            ...$headers,
        ]));
    }

    protected function registerBladeDirectives(): void
    {
        Blade::if('agent', fn (): bool => Traffic::current()->isAgent());
        Blade::if('aicrawler', fn (): bool => Traffic::current()->isAiCrawler());
        Blade::if('aitraffic', fn (): bool => Traffic::current()->isAi());
        Blade::if('human', fn (): bool => Traffic::current()->isHuman());
    }

    protected function registerMiddlewareAliases(): void
    {
        $this->callAfterResolving('router', function (Router $router): void {
            $router->aliasMiddleware('traffic.prevent', PreventAiTraffic::class);
            $router->aliasMiddleware('traffic.context', AddTrafficContext::class);
        });
    }

    protected function registerAboutCommand(): void
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Agent Detector', fn (): array => [
            'Running In Agent' => AgentDetector::detect()->name ?? '<fg=yellow;options=bold>NO</>',
        ]);
    }
}
