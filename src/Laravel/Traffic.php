<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Laravel;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Laravel\AgentDetector\Http\TrafficClassifier;
use Laravel\AgentDetector\Http\TrafficResult;

class Traffic
{
    /**
     * The request attribute the classification is memoized under.
     */
    public const ATTRIBUTE = 'agent-detector.traffic';

    /**
     * Classify the given request, memoizing the result on the request.
     */
    public static function for(Request $request): TrafficResult
    {
        $cached = $request->attributes->get(self::ATTRIBUTE);

        if ($cached instanceof TrafficResult) {
            return $cached;
        }

        /** @var TrafficClassifier $classifier */
        $classifier = Container::getInstance()->make(TrafficClassifier::class);

        /** @var array<string, list<string|null>> $headers */
        $headers = $request->headers->all();

        $result = $classifier->classify(array_map(
            fn (array $values): string => implode(', ', array_filter($values, is_string(...))),
            $headers,
        ));

        $request->attributes->set(self::ATTRIBUTE, $result);

        return $result;
    }

    /**
     * Classify the current request.
     */
    public static function current(): TrafficResult
    {
        /** @var Request $request */
        $request = Container::getInstance()->make('request');

        return static::for($request);
    }
}
