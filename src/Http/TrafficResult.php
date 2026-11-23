<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Http;

use JsonSerializable;

class TrafficResult implements JsonSerializable
{
    public function __construct(
        public readonly TrafficCategory $category,
        public readonly ?string $kind = null,
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $operator = null,
        public readonly ?string $version = null,
        public readonly ?string $tenant = null,
        public readonly Confidence $confidence = Confidence::Low,
    ) {
        //
    }

    /**
     * Determine if the request was made by an AI agent acting on behalf of a user.
     */
    public function isAgent(): bool
    {
        return $this->category === TrafficCategory::Agent;
    }

    /**
     * Determine if the request was made by an AI crawler (training, AI search, or ingestion).
     */
    public function isAiCrawler(): bool
    {
        return $this->category === TrafficCategory::AiCrawler;
    }

    /**
     * Determine if the request was made by an AI agent or AI crawler.
     */
    public function isAi(): bool
    {
        return $this->isAgent() || $this->isAiCrawler();
    }

    /**
     * Determine if the request was made by a search engine crawler.
     */
    public function isCrawler(): bool
    {
        return $this->category === TrafficCategory::Crawler;
    }

    /**
     * Determine if the request was made by a non-AI bot or generic HTTP client.
     */
    public function isBot(): bool
    {
        return $this->category === TrafficCategory::Bot;
    }

    /**
     * Determine if the request appears to have been made by a human.
     */
    public function isHuman(): bool
    {
        return $this->category === TrafficCategory::Human;
    }

    /**
     * Determine if the request was made by any automated client.
     */
    public function isAutomated(): bool
    {
        return ! $this->isHuman() && $this->category !== TrafficCategory::Unknown;
    }

    /**
     * @return array{category: string, kind: ?string, id: ?string, name: ?string, operator: ?string, version: ?string, tenant: ?string, confidence: string}
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category->value,
            'kind' => $this->kind,
            'id' => $this->id,
            'name' => $this->name,
            'operator' => $this->operator,
            'version' => $this->version,
            'tenant' => $this->tenant,
            'confidence' => $this->confidence->value,
        ];
    }

    /**
     * @return array{category: string, kind: ?string, id: ?string, name: ?string, operator: ?string, version: ?string, tenant: ?string, confidence: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
