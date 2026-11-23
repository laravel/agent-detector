<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Http;

enum Confidence: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
}
