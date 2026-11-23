<?php

declare(strict_types=1);

namespace Laravel\AgentDetector;

use Laravel\AgentDetector\Http\TrafficClassifier;
use Laravel\AgentDetector\Http\TrafficResult;

function detectAgent(): AgentResult
{
    return AgentDetector::detect();
}

/**
 * Classify an inbound HTTP request by its headers, or the current request when none are given.
 *
 * @param  array<string, string|list<string>|null>|null  $headers
 */
function classifyTraffic(?array $headers = null): TrafficResult
{
    return $headers === null
        ? TrafficClassifier::fromGlobals()
        : (new TrafficClassifier())->classify($headers);
}
