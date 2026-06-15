# Laravel Agent Detector

<p align="center">
    <a href="https://github.com/laravel/agent-detector/actions"><img alt="GitHub Workflow Status (main)" src="https://github.com/laravel/agent-detector/actions/workflows/tests.yml/badge.svg"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/laravel/agent-detector"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="Latest Version" src="https://img.shields.io/packagist/v/laravel/agent-detector"></a>
    <a href="https://packagist.org/packages/laravel/agent-detector"><img alt="License" src="https://img.shields.io/packagist/l/laravel/agent-detector"></a>
</p>

## Introduction

Agent Detector is a lightweight PHP utility to detect if your code is running inside an AI agent or automated development environment.

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
|-------------| ------------------------------------------------------------------------------------------------------------------------------------------------ |
| Custom      | `AI_AGENT` env var                                                                                                                               |
| Cursor      | `CURSOR_AGENT` env var                                                                                                                           |
| Gemini      | `GEMINI_CLI` env var                                                                                                                             |
| Codex       | `CODEX_SANDBOX`, `CODEX_CI`, or `CODEX_THREAD_ID` env var                                                                                        |
| Augment CLI | `AUGMENT_AGENT` env var                                                                                                                          |
| AMP         | `AMP_CURRENT_THREAD_ID` env var                                                                                                                  |
| Opencode    | `OPENCODE_CLIENT` or `OPENCODE` env var                                                                                                          |
| Claude      | `CLAUDECODE` or `CLAUDE_CODE` env var                                                                                                            |
| Cowork      | `CLAUDE_CODE_IS_COWORK` with `CLAUDECODE` or `CLAUDE_CODE` env var                                                                               |
| Copilot     | `AI_AGENT=github-copilot`, `AI_AGENT=github-copilot-cli`, `COPILOT_MODEL`, `COPILOT_ALLOW_ALL`, `COPILOT_GITHUB_TOKEN`, or `COPILOT_CLI` env var |
| Replit      | `REPL_ID` env var                                                                                                                                |
| Devin       | `/opt/.devin` file exists                                                                                                                        |
| Antigravity | `ANTIGRAVITY_AGENT` env var                                                                                                                      |
| Pi          | `PI_CODING_AGENT` env var                                                                                                                        |
| Kiro CLI    | `KIRO_AGENT_PATH` env var                                                                                                                        |
| v0          | `AI_AGENT=v0` env var                                                                                                                            |
| Vibe        | `MISTRAL_API_KEY` env var                                                                                                                            |

### Custom Agent

Set the `AI_AGENT` environment variable to any value to identify your custom agent:

```bash
AI_AGENT=my-custom-agent php your-script.php
```

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
