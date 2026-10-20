<?php

declare(strict_types=1);

namespace Laravel\AgentDetector;

class AgentDetector
{
    public const AGENT_ENV_VARS = [
        'CURSOR_AGENT' => KnownAgent::Cursor,
        'GROK_PLUGIN_ROOT' => KnownAgent::Grok,
        'GROK_PLUGIN_DATA' => KnownAgent::Grok,
        'QWEN_CODE' => KnownAgent::QwenCode,
        'GEMINI_CLI' => KnownAgent::Gemini,
        'CODEX_SANDBOX' => KnownAgent::Codex,
        'CODEX_CI' => KnownAgent::Codex,
        'CODEX_THREAD_ID' => KnownAgent::Codex,
        'CODEX_SANDBOX_NETWORK_DISABLED' => KnownAgent::Codex,
        'AUGMENT_AGENT' => KnownAgent::AugmentCli,
        'OPENCODE_CLIENT' => KnownAgent::Opencode,
        'OPENCODE' => KnownAgent::Opencode,
        'AMP_CURRENT_THREAD_ID' => KnownAgent::Amp,
        'CLAUDECODE' => KnownAgent::Claude,
        'CLAUDE_CODE' => KnownAgent::Claude,
        'REPL_ID' => KnownAgent::Replit,
        'COPILOT_MODEL' => KnownAgent::Copilot,
        'COPILOT_ALLOW_ALL' => KnownAgent::Copilot,
        'COPILOT_GITHUB_TOKEN' => KnownAgent::Copilot,
        'COPILOT_CLI' => KnownAgent::Copilot,
        'ANTIGRAVITY_AGENT' => KnownAgent::Antigravity,
        'ANTIGRAVITY_CLI_ALIAS' => KnownAgent::Antigravity,
        'PI_CODING_AGENT' => KnownAgent::Pi,
        'MATTERHORN_SESSION_ID' => KnownAgent::Junie,
        'JUNIE_DATA' => KnownAgent::Junie,
        'JUNIE_SHIM_PATH' => KnownAgent::Junie,
        'KIRO_AGENT_PATH' => KnownAgent::KiroCli,
        'KIMI_PLUGIN_ROOT' => KnownAgent::Kimi,
        'CLINE_ACTIVE' => KnownAgent::Cline,
        'GOOSE_TERMINAL' => KnownAgent::Goose,
        'OPENCLAW_SHELL' => KnownAgent::OpenClaw,
        'ZCODE_ENV' => KnownAgent::ZCode,
        'CRUSH' => KnownAgent::Crush,
        'WINDSURF_CASCADE_TERMINAL' => KnownAgent::Windsurf,
    ];

    public static function detect(): AgentResult
    {
        return static::fromAiAgentEnvVar()
            ?? static::fromKnownEnvVars()
            ?? static::fromCompoundEnvVars()
            ?? static::fromFileSystem()
            ?? AgentResult::noAgent();
    }

    protected static function fromAiAgentEnvVar(): ?AgentResult
    {
        $aiAgent = trim((string) getenv('AI_AGENT'));

        if ($aiAgent === '') {
            return null;
        }

        return AgentResult::forAgent(match (true) {
            in_array($aiAgent, ['github-copilot', 'github-copilot-cli'], true) => KnownAgent::Copilot,
            str_starts_with($aiAgent, 'claude-code') => KnownAgent::Claude,
            default => $aiAgent,
        });
    }

    protected static function fromKnownEnvVars(): ?AgentResult
    {
        foreach (self::AGENT_ENV_VARS as $envVar => $agent) {
            if (getenv($envVar) === false) {
                continue;
            }

            if ($agent === KnownAgent::Claude && getenv('CLAUDE_CODE_IS_COWORK') !== false) {
                $agent = KnownAgent::Cowork;
            }

            return AgentResult::forAgent($agent);
        }

        return null;
    }

    protected static function fromCompoundEnvVars(): ?AgentResult
    {
        $termProgram = getenv('TERM_PROGRAM');

        if ($termProgram === 'zed'
            && getenv('PAGER') === ''
            && getenv('GIT_PAGER') === 'cat'
            && static::isRunningInCli()) {
            return AgentResult::forAgent(KnownAgent::Zed);
        }

        if ($termProgram === 'kiro'
            && static::isRunningInCli()
            && ! static::isInteractiveTerminal()) {
            return AgentResult::forAgent(KnownAgent::Kiro);
        }

        if (getenv('COPILOT_AGENT_SESSION_ID') !== false && getenv('GITHUB_ACTIONS') !== false) {
            return AgentResult::forAgent(KnownAgent::Copilot);
        }

        if (getenv('HOME') === '/home/jules' && getenv('USER') === 'swebot') {
            return AgentResult::forAgent(KnownAgent::Jules);
        }

        return null;
    }

    protected static function isInteractiveTerminal(): bool
    {
        return defined('STDOUT') && is_resource(STDOUT) && stream_isatty(STDOUT);
    }

    protected static function isRunningInCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    protected static function fromFileSystem(): ?AgentResult
    {
        if (file_exists('/opt/.devin')) {
            return AgentResult::forAgent(KnownAgent::Devin);
        }

        return null;
    }
}
