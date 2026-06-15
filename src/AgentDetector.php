<?php

declare(strict_types=1);

namespace Laravel\AgentDetector;

class AgentDetector
{
    public const AGENT_ENV_VARS = [
        'CURSOR_AGENT' => KnownAgent::Cursor,
        'GEMINI_CLI' => KnownAgent::Gemini,
        'CODEX_SANDBOX' => KnownAgent::Codex,
        'CODEX_CI' => KnownAgent::Codex,
        'CODEX_THREAD_ID' => KnownAgent::Codex,
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
        'PI_CODING_AGENT' => KnownAgent::Pi,
        'KIRO_AGENT_PATH' => KnownAgent::KiroCli,
        'MISTRAL_API_KEY' => KnownAgent::Vibe,
    ];

    public static function detect(): AgentResult
    {
        return self::fromAiAgentEnvVar()
            ?? self::fromKnownEnvVars()
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

    protected static function fromFileSystem(): ?AgentResult
    {
        if (file_exists('/opt/.devin')) {
            return AgentResult::forAgent(KnownAgent::Devin);
        }

        return null;
    }
}
