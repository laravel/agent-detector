<?php

declare(strict_types=1);

namespace Laravel\AgentDetector;

enum KnownAgent: string
{
    case Cursor = 'cursor';
    case Claude = 'claude';
    case Cowork = 'cowork';
    case Devin = 'devin';
    case Replit = 'replit';
    case Gemini = 'gemini';
    case Codex = 'codex';
    case V0 = 'v0';
    case AugmentCli = 'augment-cli';
    case Opencode = 'opencode';
    case Amp = 'amp';
    case Copilot = 'copilot';
    case Antigravity = 'antigravity';
    case Pi = 'pi';
    case KiroCli = 'kiro-cli';
    case Vibe = 'vibe';

    public function label(): string
    {
        return match ($this) {
            self::AugmentCli => 'Augment CLI',
            self::KiroCli => 'Kiro CLI',
            self::V0 => 'v0',
            default => ucfirst($this->value),
        };
    }
}
