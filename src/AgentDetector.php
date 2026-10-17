<?php

declare(strict_types=1);

namespace Laravel\AgentDetector;

class AgentDetector
{
    public const AGENT_ENV_VARS = [
        'CURSOR_AGENT' => KnownAgent::Cursor,
        // Grok runs Claude Code plugins and may expose Claude Code's env vars, so it must come before them.
        'GROK_PLUGIN_ROOT' => KnownAgent::Grok,
        'GROK_PLUGIN_DATA' => KnownAgent::Grok,
        // Qwen Code is a Gemini CLI fork and may inherit Gemini CLI's env vars, so it must come first.
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
        // Cursor's integrated terminal also sets this for human sessions, so it
        // comes last to let the agent CLIs running inside Cursor win.
        'CURSOR_TRACE_ID' => KnownAgent::Cursor,
    ];

    public static function detect(): AgentResult
    {
        return self::fromAiAgentEnvVar()
            ?? self::fromKnownEnvVars()
            ?? self::fromCompoundEnvVars()
            ?? self::fromFileSystem()
            ?? AgentResult::noAgent();
    }

    protected static function fromAiAgentEnvVar(): ?AgentResult
    {
        $aiAgent = getenv('AI_AGENT');

        if ($aiAgent === false) {
            return null;
        }

        $aiAgent = trim($aiAgent);

        if ($aiAgent === '') {
            return null;
        }

        return AgentResult::forAgent(match (true) {
            in_array($aiAgent, ['github-copilot', 'github-copilot-cli']) => KnownAgent::Copilot,
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

            return AgentResult::forAgent(match ($agent) {
                KnownAgent::Claude => getenv('CLAUDE_CODE_IS_COWORK') !== false ? KnownAgent::Cowork : KnownAgent::Claude,
                default => $agent,
            });
        }

        return null;
    }

    protected static function fromCompoundEnvVars(): ?AgentResult
    {
        // Zed sets TERM_PROGRAM in both its interactive terminal and its agent's
        // commands. Agent commands are also marked by an emptied `PAGER` with the
        // git pager forced to `cat`, which a human's `PAGER=cat` export cannot
        // match. The CLI check keeps servers started from a terminal, such as
        // `php artisan serve`, from being detected on every request they serve.
        if (getenv('TERM_PROGRAM') === 'zed' && static::isRunningInCli()
            && getenv('PAGER') === '' && getenv('GIT_PAGER') === 'cat') {
            return AgentResult::forAgent(KnownAgent::Zed);
        }

        // Kiro also sets TERM_PROGRAM for its IDE terminal, so a non-interactive
        // terminal is required to avoid detecting humans working in the IDE. A
        // human piping or redirecting output is still detected, and the CLI
        // check keeps servers started from a terminal, such as `php artisan
        // serve`, from being detected on every request they serve. `static::`
        // keeps both checks overridable via a subclass in tests.
        if (getenv('TERM_PROGRAM') === 'kiro' && static::isRunningInCli()
            && ! static::isInteractiveTerminal()) {
            return AgentResult::forAgent(KnownAgent::Kiro);
        }

        // Copilot's cloud coding agent runs in an ephemeral GitHub Actions environment.
        if (getenv('COPILOT_AGENT_SESSION_ID') !== false && getenv('GITHUB_ACTIONS') !== false) {
            return AgentResult::forAgent(KnownAgent::Copilot);
        }

        // Jules runs commands as a dedicated sandbox user.
        if (getenv('HOME') === '/home/jules' && getenv('USER') === 'swebot') {
            return AgentResult::forAgent(KnownAgent::Jules);
        }

        return null;
    }

    protected static function isInteractiveTerminal(): bool
    {
        if (! defined('STDOUT') || ! is_resource(STDOUT)) {
            return false;
        }

        // Terminals report as TTYs, while agent subprocesses use pipes or files.
        return stream_isatty(STDOUT);
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
