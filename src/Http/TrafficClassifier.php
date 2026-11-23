<?php

declare(strict_types=1);

namespace Laravel\AgentDetector\Http;

/**
 * Classify inbound HTTP traffic as AI agents, AI crawlers, search crawlers, bots, or humans.
 *
 * @phpstan-type Entry array{id: string, name: string, operator?: string, category: string, kind: string, patterns: list<string>, accept?: string, signatureAgent?: list<string>}
 */
class TrafficClassifier
{
    /**
     * @var list<Entry>
     */
    protected array $agents;

    /**
     * @var list<Entry>
     */
    protected array $clients;

    /**
     * @param  list<Entry>|null  $agents
     * @param  list<Entry>|null  $clients
     */
    public function __construct(?array $agents = null, ?array $clients = null)
    {
        /** @var list<Entry> $defaultAgents */
        $defaultAgents = require __DIR__.'/../../resources/agents.php';

        /** @var list<Entry> $defaultClients */
        $defaultClients = require __DIR__.'/../../resources/http-clients.php';

        $this->agents = $agents ?? $defaultAgents;
        $this->clients = $clients ?? $defaultClients;
    }

    /**
     * Classify the current request using PHP's superglobals.
     */
    public static function fromGlobals(): TrafficResult
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (is_string($key) && is_string($value) && str_starts_with($key, 'HTTP_')) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            }
        }

        return (new self())->classify($headers);
    }

    /**
     * Classify a request from its headers.
     *
     * @param  array<string, string|list<string>|null>  $headers
     */
    public function classify(array $headers): TrafficResult
    {
        $headers = $this->normalizeHeaders($headers);

        $userAgent = $headers['user-agent'] ?? '';
        $accept = $headers['accept'] ?? '';

        return $this->fromUserAgent($userAgent, $this->agents)
            ?? $this->fromAcceptFingerprint($accept)
            ?? $this->fromSignatureAgent($headers)
            ?? $this->fromMarkdownPreference($accept)
            ?? $this->fromUserAgent($userAgent, $this->clients)
            ?? $this->fromBrowser($userAgent)
            ?? $this->fallback($userAgent, $headers);
    }

    /**
     * Classify a request from just its User-Agent (and optionally Accept) header.
     */
    public function classifyUserAgent(?string $userAgent, ?string $accept = null): TrafficResult
    {
        return $this->classify(array_filter([
            'user-agent' => $userAgent,
            'accept' => $accept,
        ], fn (?string $value): bool => $value !== null));
    }

    /**
     * Determine if the given Accept header ranks markdown above HTML.
     */
    public static function prefersMarkdown(?string $accept): bool
    {
        if ($accept === null || trim($accept) === '') {
            return false;
        }

        $markdown = 0.0;
        $html = 0.0;

        foreach (explode(',', $accept) as $range) {
            $parts = array_map(trim(...), explode(';', $range));
            $type = strtolower(array_shift($parts));
            $quality = 1.0;

            foreach ($parts as $parameter) {
                if (preg_match('/^q\s*=\s*([\d.]+)$/i', $parameter, $matches) === 1) {
                    $quality = (float) $matches[1];
                }
            }

            if (in_array($type, ['text/markdown', 'text/x-markdown'], true)) {
                $markdown = max($markdown, $quality);
            } elseif ($type === 'text/html') {
                $html = max($html, $quality);
            }
        }

        return $markdown > 0 && $markdown > $html;
    }

    /**
     * @param  list<Entry>  $entries
     */
    protected function fromUserAgent(string $userAgent, array $entries): ?TrafficResult
    {
        if ($userAgent === '') {
            return null;
        }

        foreach ($entries as $entry) {
            foreach ($entry['patterns'] as $pattern) {
                if (preg_match('~'.str_replace('~', '\~', $pattern).'~i', $userAgent, $matches) === 1) {
                    return $this->fromEntry(
                        $entry,
                        Confidence::High,
                        isset($matches['version']) && $matches['version'] !== '' ? $matches['version'] : null,
                        isset($matches['tenant']) && $matches['tenant'] !== '' ? $matches['tenant'] : null,
                    );
                }
            }
        }

        return null;
    }

    protected function fromAcceptFingerprint(string $accept): ?TrafficResult
    {
        if ($accept === '') {
            return null;
        }

        foreach ($this->agents as $entry) {
            if (isset($entry['accept']) && $entry['accept'] === $accept) {
                return $this->fromEntry($entry, Confidence::Medium);
            }
        }

        return null;
    }

    /**
     * Match the host advertised by the Web Bot Auth Signature-Agent header.
     *
     * The signature itself is not verified, so this is only ever a low confidence match.
     *
     * @param  array<string, string>  $headers
     */
    protected function fromSignatureAgent(array $headers): ?TrafficResult
    {
        if (! isset($headers['signature'], $headers['signature-agent'])) {
            return null;
        }

        $host = strtolower(trim($headers['signature-agent'], " \t\""));
        $host = (string) (parse_url(str_contains($host, '://') ? $host : 'https://'.$host, PHP_URL_HOST) ?? '');

        foreach ($this->agents as $entry) {
            if (in_array($host, $entry['signatureAgent'] ?? [], true)) {
                return $this->fromEntry($entry, Confidence::Low);
            }
        }

        return null;
    }

    protected function fromMarkdownPreference(string $accept): ?TrafficResult
    {
        return static::prefersMarkdown($accept)
            ? new TrafficResult(TrafficCategory::Agent, 'unidentified', confidence: Confidence::Medium)
            : null;
    }

    protected function fromBrowser(string $userAgent): ?TrafficResult
    {
        $looksLikeBrowser = preg_match('~^(Mozilla/[4-6]\.0 \(|Opera/\d)~', $userAgent) === 1
            && preg_match('~(AppleWebKit|Gecko|Trident|Presto)/~', $userAgent) === 1
            && preg_match('~bot\b|crawl|spider|slurp|headless|automation|selenium|puppeteer~i', $userAgent) === 0;

        return $looksLikeBrowser
            ? new TrafficResult(TrafficCategory::Human, confidence: Confidence::Low)
            : null;
    }

    /**
     * @param  array<string, string>  $headers
     */
    protected function fallback(string $userAgent, array $headers): TrafficResult
    {
        if ($userAgent === '' && $headers === []) {
            return new TrafficResult(TrafficCategory::Unknown);
        }

        return new TrafficResult(TrafficCategory::Bot, 'unidentified');
    }

    /**
     * @param  Entry  $entry
     */
    protected function fromEntry(array $entry, Confidence $confidence, ?string $version = null, ?string $tenant = null): TrafficResult
    {
        return new TrafficResult(
            category: TrafficCategory::tryFrom($entry['category']) ?? TrafficCategory::Bot,
            kind: $entry['kind'],
            id: $entry['id'],
            name: $entry['name'],
            operator: $entry['operator'] ?? null,
            version: $version,
            tenant: $tenant,
            confidence: $confidence,
        );
    }

    /**
     * @param  array<string, string|list<string>|null>  $headers
     * @return array<string, string>
     */
    protected function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $name => $value) {
            $value = is_array($value) ? implode(', ', $value) : $value;

            if ($value !== null) {
                $normalized[strtolower(str_replace('_', '-', $name))] = trim($value);
            }
        }

        return $normalized;
    }
}
