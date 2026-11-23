<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Laravel\AgentDetector\Laravel\Traffic;

/**
 * Share the traffic classification with Laravel's Context so it is attached
 * to every log entry and dispatched job for the remainder of the request.
 */
class AddTrafficContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        $traffic = Traffic::for($request);

        Context::add('traffic', array_filter([
            'category' => $traffic->category->value,
            'kind' => $traffic->kind,
            'agent' => $traffic->id,
            'confidence' => $traffic->confidence->value,
        ]));

        return $next($request);
    }
}
