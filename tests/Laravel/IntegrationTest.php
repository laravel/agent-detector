<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Laravel\AgentDetector\Http\TrafficCategory;
use Laravel\AgentDetector\Http\TrafficClassifier;
use Laravel\AgentDetector\Laravel\Traffic;

const GPTBOT = 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)';
const CHATGPT_USER = 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot';
const SAFARI = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';

it('registers the classifier as a singleton', function (): void {
    expect(app(TrafficClassifier::class))->toBe(app(TrafficClassifier::class));
});

it('adds request macros', function (): void {
    $request = Request::create('/', server: ['HTTP_USER_AGENT' => CHATGPT_USER, 'HTTP_ACCEPT' => 'text/markdown']);

    expect($request->traffic()->id)->toBe('chatgpt-user')
        ->and($request->isAgent())->toBeTrue()
        ->and($request->isAiCrawler())->toBeFalse()
        ->and($request->isAiTraffic())->toBeTrue()
        ->and($request->isBot())->toBeTrue()
        ->and($request->prefersMarkdown())->toBeTrue()
        ->and($request->attributes->get(Traffic::ATTRIBUTE))->toBe($request->traffic());
});

it('classifies browsers as human', function (): void {
    $request = Request::create('/', server: ['HTTP_USER_AGENT' => SAFARI, 'HTTP_ACCEPT' => 'text/html']);

    expect($request->traffic()->category)->toBe(TrafficCategory::Human)
        ->and($request->isBot())->toBeFalse()
        ->and($request->prefersMarkdown())->toBeFalse();
});

it('adds a markdown response macro', function (): void {
    Route::get('/docs', fn () => response()->markdown('# Hello', 200, ['X-Test' => 'yes']));

    $this->get('/docs')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
        ->assertHeader('Vary', 'Accept')
        ->assertHeader('X-Test', 'yes')
        ->assertSee('# Hello', false);
});

it('adds blade conditionals', function (string $userAgent, string $expected): void {
    $this->app->instance('request', Request::create('/', server: ['HTTP_USER_AGENT' => $userAgent]));

    $output = Blade::render('@agent agent @endagent @aicrawler crawler @endaicrawler @aitraffic ai @endaitraffic @human human @endhuman');

    expect(preg_replace('/\s+/', ' ', trim($output)))->toBe($expected);
})->with([
    [CHATGPT_USER, 'agent ai'],
    [GPTBOT, 'crawler ai'],
    [SAFARI, 'human'],
]);

it('blocks ai training crawlers by default', function (): void {
    Route::get('/', fn (): string => 'ok')->middleware('block-ai');

    $this->get('/', ['User-Agent' => GPTBOT])->assertForbidden();
    $this->get('/', ['User-Agent' => CHATGPT_USER])->assertOk();
    $this->get('/', ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])->assertOk();
    $this->get('/', ['User-Agent' => SAFARI])->assertOk();
});

it('blocks configured categories, kinds and ids', function (string $targets, string $userAgent, int $status): void {
    Route::get('/', fn (): string => 'ok')->middleware("block-ai:{$targets}");

    $this->get('/', ['User-Agent' => $userAgent])->assertStatus($status);
})->with([
    ['agent', CHATGPT_USER, 403],
    ['agent', GPTBOT, 200],
    ['ai-crawler,agent', GPTBOT, 403],
    ['chatgpt-user', CHATGPT_USER, 403],
    ['ai-search', GPTBOT, 200],
    ['search', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 200],
]);

it('shares the classification with context', function (): void {
    Route::get('/', fn (): array => Context::get('traffic'))->middleware('traffic-context');

    $this->get('/', ['User-Agent' => GPTBOT])->assertExactJson([
        'category' => 'ai-crawler',
        'kind' => 'training',
        'agent' => 'gptbot',
        'confidence' => 'high',
    ]);
});

it('adds agent detector info to the about command', function (): void {
    $this->artisan('about', ['--only' => 'agent_detector', '--json' => true])->assertSuccessful();
});
