<?php

declare(strict_types=1);

use Laravel\AgentDetector\Http\Confidence;
use Laravel\AgentDetector\Http\TrafficCategory;
use Laravel\AgentDetector\Http\TrafficClassifier;

use function Laravel\AgentDetector\classifyTraffic;

it('classifies known agents, crawlers and clients by user agent', function (string $userAgent, TrafficCategory $category, string $kind, string $id): void {
    $result = (new TrafficClassifier())->classifyUserAgent($userAgent);

    expect($result->category)->toBe($category)
        ->and($result->kind)->toBe($kind)
        ->and($result->id)->toBe($id)
        ->and($result->confidence)->toBe(Confidence::High);
})->with([
    'claude code' => ['Claude-User (claude-code/2.1.308; +https://support.anthropic.com/)', TrafficCategory::Agent, 'coding', 'claude-code'],
    'chatgpt user' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot', TrafficCategory::Agent, 'assistant', 'chatgpt-user'],
    'claude user' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Claude-User/1.0; +Claude-User@anthropic.com)', TrafficCategory::Agent, 'assistant', 'claude-user'],
    'gptbot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)', TrafficCategory::AiCrawler, 'training', 'gptbot'],
    'perplexitybot' => ['Mozilla/5.0 (compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot)', TrafficCategory::AiCrawler, 'ai-search', 'perplexitybot'],
    'googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', TrafficCategory::Crawler, 'search', 'googlebot'],
    'slack preview' => ['Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)', TrafficCategory::Bot, 'preview', 'link-previews'],
    'axios' => ['axios/1.13.2', TrafficCategory::Bot, 'http-client', 'axios'],
    'headless chrome' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/120.0.0.0 Safari/537.36', TrafficCategory::Bot, 'headless', 'headless-chrome'],
]);

it('extracts version and tenant named groups', function (): void {
    $classifier = new TrafficClassifier();

    expect($classifier->classifyUserAgent('Claude-User (claude-code/2.1.308; +https://support.anthropic.com/)')->version)->toBe('2.1.308')
        ->and($classifier->classifyUserAgent('amazon-Quick-on-behalf-of-acme-corp')->tenant)->toBe('acme-corp')
        ->and($classifier->classifyUserAgent('GPTBot/1.2')->version)->toBeNull();
});

it('prefers known agents over generic http clients', function (): void {
    $result = (new TrafficClassifier())->classifyUserAgent('axios/1.13.2', 'text/markdown, text/html;q=0.5');

    expect($result->category)->toBe(TrafficCategory::Agent)
        ->and($result->kind)->toBe('unidentified')
        ->and($result->confidence)->toBe(Confidence::Medium);
});

it('detects opencode by its accept fingerprint', function (): void {
    $result = (new TrafficClassifier())->classify([
        'Accept' => 'text/markdown;q=1.0, text/x-markdown;q=0.9, text/plain;q=0.8, text/html;q=0.7, */*;q=0.1',
    ]);

    expect($result->id)->toBe('opencode')
        ->and($result->confidence)->toBe(Confidence::Medium);
});

it('detects web bot auth signature agents with low confidence', function (string $signatureAgent): void {
    $result = (new TrafficClassifier())->classify([
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36',
        'Signature' => 'sig1=:abc:',
        'Signature-Agent' => $signatureAgent,
    ]);

    expect($result->id)->toBe('chatgpt-agent')
        ->and($result->isAgent())->toBeTrue()
        ->and($result->confidence)->toBe(Confidence::Low);
})->with(['"https://chatgpt.com"', 'chatgpt.com']);

it('ignores signature agent without a signature', function (): void {
    $result = (new TrafficClassifier())->classify([
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36',
        'Signature-Agent' => 'chatgpt.com',
    ]);

    expect($result->isHuman())->toBeTrue();
});

it('classifies browsers as human and everything else as unidentified bots', function (): void {
    $classifier = new TrafficClassifier();

    $human = $classifier->classifyUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15', 'text/html');
    $bot = $classifier->classifyUserAgent('SomethingCustom/1.0');
    $unknown = $classifier->classify([]);

    expect($human->isHuman())->toBeTrue()
        ->and($human->isAutomated())->toBeFalse()
        ->and($bot->category)->toBe(TrafficCategory::Bot)
        ->and($bot->kind)->toBe('unidentified')
        ->and($bot->isAutomated())->toBeTrue()
        ->and($unknown->category)->toBe(TrafficCategory::Unknown)
        ->and($unknown->isAutomated())->toBeFalse();
});

it('determines markdown preference from the accept header', function (?string $accept, bool $expected): void {
    expect(TrafficClassifier::prefersMarkdown($accept))->toBe($expected);
})->with([
    [null, false],
    ['', false],
    ['text/html', false],
    ['text/markdown', true],
    ['text/x-markdown, text/html;q=0.9', true],
    ['text/html, text/markdown;q=0.9', false],
    ['text/markdown;q=0.5, text/html;q=0.5', false],
    ['text/markdown;q=0', false],
]);

it('exposes result helpers and serializes', function (): void {
    $result = (new TrafficClassifier())->classifyUserAgent('GPTBot/1.2');

    expect($result->isAi())->toBeTrue()
        ->and($result->isAiCrawler())->toBeTrue()
        ->and($result->isAgent())->toBeFalse()
        ->and($result->isCrawler())->toBeFalse()
        ->and($result->isBot())->toBeFalse()
        ->and(json_encode($result))->toBe(json_encode([
            'category' => 'ai-crawler',
            'kind' => 'training',
            'id' => 'gptbot',
            'name' => 'GPTBot',
            'operator' => 'OpenAI',
            'version' => null,
            'tenant' => null,
            'confidence' => 'high',
        ]));
});

it('normalizes header names and array values', function (): void {
    $result = (new TrafficClassifier())->classify([
        'USER_AGENT' => ['GPTBot/1.2'],
        'X-Empty' => null,
    ]);

    expect($result->id)->toBe('gptbot');
});

it('accepts custom datasets', function (): void {
    $classifier = new TrafficClassifier(
        agents: [['id' => 'acme', 'name' => 'Acme Agent', 'category' => 'agent', 'kind' => 'assistant', 'patterns' => ['^AcmeAgent/']]],
        clients: [],
    );

    expect($classifier->classifyUserAgent('AcmeAgent/1.0')->name)->toBe('Acme Agent')
        ->and($classifier->classifyUserAgent('axios/1.0')->kind)->toBe('unidentified');
});

it('classifies the current request from superglobals', function (): void {
    $original = $_SERVER;
    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)';

    try {
        expect(classifyTraffic()->id)->toBe('claudebot')
            ->and(classifyTraffic(['User-Agent' => 'curl/8.0'])->id)->toBe('curl');
    } finally {
        $_SERVER = $original;
    }
});

it('compiles every bundled pattern', function (): void {
    $entries = [...require __DIR__.'/../resources/agents.php', ...require __DIR__.'/../resources/http-clients.php'];

    foreach ($entries as $entry) {
        foreach ($entry['patterns'] as $pattern) {
            expect(@preg_match('~'.str_replace('~', '\~', $pattern).'~i', ''))->not->toBeFalse("Invalid pattern for {$entry['id']}: {$pattern}");
        }
    }

    expect(array_unique(array_column($entries, 'id')))->toHaveCount(count($entries));
});
