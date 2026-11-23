<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\AgentDetector\Laravel\Traffic;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevent AI traffic from accessing the route.
 *
 * With no arguments, AI training crawlers are blocked. Pass categories or kinds to
 * customize, e.g. "traffic.prevent:ai-crawler,agent" or "traffic.prevent:training,ingest".
 */
class PreventAiTraffic
{
    public function handle(Request $request, Closure $next, string ...$targets): mixed
    {
        $traffic = Traffic::for($request);

        $targets = $targets === [] ? ['training'] : $targets;

        $blocked = in_array($traffic->category->value, $targets, true)
            || ($traffic->kind !== null && $traffic->isAi() && in_array($traffic->kind, $targets, true))
            || ($traffic->id !== null && in_array($traffic->id, $targets, true));

        abort_if($blocked, Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
