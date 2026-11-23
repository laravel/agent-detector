# Laravel Agent Detector

<p align="center">
    <a href="https://github.com/laravel/agent-detector/actions"><img alt="GitHub Workflow Status (main)" src="https://github.com/laravel/agent-detector/actions/workflows/tests.yml/badge.svg"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/laravel/agent-detector"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="Latest Version" src="https://img.shields.io/packagist/v/laravel/agent-detector"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="License" src="https://img.shields.io/packagist/l/laravel/agent-detector"></a>
</p>

## Introduction

Agent Detector is a lightweight PHP utility to detect if your code is running inside an AI agent or automated development environment, and to classify inbound HTTP traffic from AI agents and crawlers — with first-class Laravel integration.

> **Requires [PHP 8.2+](https://php.net/releases/)**

## Installation

To get started, install Agent Detector via the Composer package manager:

```bash
composer require laravel/agent-detector
```

## Usage

```php
use Laravel\AgentDetector\AgentDetector;
use Laravel\AgentDetector\KnownAgent;

$result = AgentDetector::detect();

if ($result->isAgent) {
    echo "Running inside: {$result->name}";
}

// Check for a specific known agent
if ($result->knownAgent() === KnownAgent::Claude) {
    echo "Hello from Claude!";
}
```

Or use the standalone function:

```php
use function Laravel\AgentDetector\detectAgent;

$result = detectAgent();
```

## Supported Agents

| Agent       | Detection Method                                                                                                                                 |
| ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| Custom      | `AI_AGENT` env var                                                                                                                               |
| Cursor      | `CURSOR_AGENT` env var                                                                                                                           |
| Gemini      | `GEMINI_CLI` env var                                                                                                                             |
| Codex       | `CODEX_SANDBOX`, `CODEX_CI`, `CODEX_THREAD_ID`, or `CODEX_SANDBOX_NETWORK_DISABLED` env var                                                      |
| Augment CLI | `AUGMENT_AGENT` env var                                                                                                                          |
| AMP         | `AMP_CURRENT_THREAD_ID` env var                                                                                                                  |
| Opencode    | `OPENCODE_CLIENT` or `OPENCODE` env var                                                                                                          |
| Claude      | `CLAUDECODE` or `CLAUDE_CODE` env var                                                                                                            |
| Cowork      | `CLAUDE_CODE_IS_COWORK` with `CLAUDECODE` or `CLAUDE_CODE` env var                                                                               |
| Copilot     | `AI_AGENT=github-copilot`, `AI_AGENT=github-copilot-cli`, `COPILOT_MODEL`, `COPILOT_ALLOW_ALL`, `COPILOT_GITHUB_TOKEN`, `COPILOT_CLI`, or `COPILOT_AGENT_SESSION_ID` with `GITHUB_ACTIONS` env var |
| Replit      | `REPL_ID` env var                                                                                                                                |
| Devin       | `/opt/.devin` file exists                                                                                                                        |
| Antigravity | `ANTIGRAVITY_AGENT` or `ANTIGRAVITY_CLI_ALIAS` env var                                                                                           |
| Pi          | `PI_CODING_AGENT` env var                                                                                                                        |
| Junie       | `JUNIE_DATA`, `JUNIE_SHIM_PATH`, or `MATTERHORN_SESSION_ID` env var                                                                              |
| Kiro CLI    | `KIRO_AGENT_PATH` env var                                                                                                                        |
| v0          | `AI_AGENT=v0` env var                                                                                                                            |
| Kimi        | `KIMI_PLUGIN_ROOT` env var                                                                                                                       |
| Grok        | `GROK_PLUGIN_ROOT` or `GROK_PLUGIN_DATA` env var                                                                                                 |
| Cline       | `CLINE_ACTIVE` env var                                                                                                                           |
| Goose       | `GOOSE_TERMINAL` env var                                                                                                                         |
| OpenClaw    | `OPENCLAW_SHELL` env var                                                                                                                         |
| ZCode       | `ZCODE_ENV` env var                                                                                                                              |
| Qwen Code   | `QWEN_CODE` env var                                                                                                                              |
| Crush       | `CRUSH` or `AI_AGENT=crush` env var                                                                                                              |
| Windsurf    | `WINDSURF_CASCADE_TERMINAL` env var                                                                                                              |
| Zed Agent   | `TERM_PROGRAM=zed` with an empty `PAGER` and `GIT_PAGER=cat` env vars                                                                            |
| Kiro IDE    | `TERM_PROGRAM=kiro` env var in a non-interactive CLI process                                                                                     |
| Jules       | `HOME=/home/jules` with `USER=swebot` env vars                                                                                                   |

### Custom Agent

Set the `AI_AGENT` environment variable to any value to identify your custom agent:

```bash
AI_AGENT=my-custom-agent php your-script.php
```

## Detecting Agent HTTP Traffic

Agent Detector can also classify inbound HTTP requests — telling you whether a request came from an AI agent acting for a user (ChatGPT-User, Claude Code, Cursor...), an AI crawler (GPTBot, ClaudeBot, PerplexityBot...), a search engine, another bot, or a human.

```php
use Laravel\AgentDetector\Http\TrafficClassifier;

use function Laravel\AgentDetector\classifyTraffic;

$traffic = classifyTraffic(); // The current request, from $_SERVER...

$traffic = (new TrafficClassifier)->classify([
    'User-Agent' => 'Claude-User (claude-code/2.1.308; +https://support.anthropic.com/)',
]);

$traffic->category;   // TrafficCategory::Agent
$traffic->kind;       // "coding"
$traffic->name;       // "Claude Code"
$traffic->operator;   // "Anthropic"
$traffic->version;    // "2.1.308"
$traffic->confidence; // Confidence::High

$traffic->isAgent();     // AI agent acting on behalf of a user
$traffic->isAiCrawler(); // AI training, AI search, or ingestion crawler
$traffic->isAi();        // Either of the above
$traffic->isHuman();

TrafficClassifier::prefersMarkdown('text/markdown, text/html;q=0.9'); // true
```

Detection uses, in order: known User-Agent patterns, Accept header fingerprints, the [Web Bot Auth](https://datatracker.ietf.org/wg/webbotauth/about/) `Signature-Agent` header (low confidence, the signature is not verified), a preference for `text/markdown` over HTML, generic HTTP client patterns, and finally browser User-Agents. IP ranges are not verified, so a User-Agent can be spoofed.

| Category     | Kinds                                                                                                        |
| ------------ | ------------------------------------------------------------------------------------------------------------ |
| `agent`      | `assistant`, `coding`, `browser`, `unidentified`                                                             |
| `ai-crawler` | `training`, `ai-search`, `ingest`                                                                            |
| `crawler`    | `search`                                                                                                     |
| `bot`        | `seo`, `preview`, `ads`, `monitor`, `scanner`, `archiver`, `feed`, `http-client`, `headless`, `unidentified` |
| `human`      |                                                                                                              |
| `unknown`    |                                                                                                              |

The bundled dataset lives in [`resources/agents.php`](resources/agents.php) and [`resources/http-clients.php`](resources/http-clients.php) and is ported from [mintlify/agent-traffic](https://github.com/mintlify/agent-traffic) (MIT).

## Laravel Integration

When installed in a Laravel application, the service provider is auto-discovered.

### Request Macros

```php
$request->traffic();         // TrafficResult (memoized per request)
$request->isAgent();         // AI agent acting on behalf of a user
$request->isAiCrawler();     // AI crawler
$request->isAiTraffic();     // Either of the above
$request->isBot();           // Any automated client
$request->prefersMarkdown(); // Accept ranks text/markdown above text/html
```

### Serving Markdown to Agents

```php
Route::get('/docs/{page}', function (Request $request, string $page) {
    if ($request->prefersMarkdown() || $request->isAgent()) {
        return response()->markdown(Docs::markdown($page));
    }

    return view('docs', ['page' => $page]);
});
```

`response()->markdown()` sets `Content-Type: text/markdown; charset=UTF-8` and `Vary: Accept`.

### Middleware

```php
// Block AI training crawlers (GPTBot, ClaudeBot, CCBot...)
Route::middleware('traffic.prevent')->group(...);

// Block by category, kind, or agent id
Route::middleware('traffic.prevent:ai-crawler,agent')->group(...);
Route::middleware('traffic.prevent:training,ingest')->group(...);
Route::middleware('traffic.prevent:gptbot,bytespider')->group(...);

// Attach the classification to Laravel's Context (logs, jobs)
Route::middleware('traffic.context')->group(...);
```

### Blade Directives

```blade
@agent
    <link rel="alternate" type="text/markdown" href="{{ url()->current() }}.md">
@endagent

@aicrawler ... @endaicrawler
@aitraffic ... @endaitraffic
@human ... @endhuman
```

### About Command

`php artisan about` includes an "Agent Detector" section showing the agent the command is running inside, if any.

## Testing

```bash
composer test
```

## Contributing

Thank you for considering contributing to Agent Detector! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Agent Detector community is welcoming to all, please review and abide by the [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## License

Agent Detector is open-sourced software licensed under the [MIT license](LICENSE.md).
