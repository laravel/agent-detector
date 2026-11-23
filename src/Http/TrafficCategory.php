<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Http;

enum TrafficCategory: string
{
    case Agent = 'agent';
    case AiCrawler = 'ai-crawler';
    case Crawler = 'crawler';
    case Bot = 'bot';
    case Human = 'human';
    case Unknown = 'unknown';
}
