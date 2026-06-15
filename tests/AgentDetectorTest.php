<?php

declare(strict_types=1);

use Laravel\AgentDetector\AgentDetector;
use Laravel\AgentDetector\KnownAgent;

use function Laravel\AgentDetector\detectAgent;

beforeEach(function (): void {
    foreach ([
        'AI_AGENT',
        'CURSOR_AGENT',
        'GEMINI_CLI',
        'CODEX_SANDBOX',
        'CODEX_CI',
        'CODEX_THREAD_ID',
        'AUGMENT_AGENT',
        'OPENCODE_CLIENT',
        'OPENCODE',
        'AMP_CURRENT_THREAD_ID',
        'CLAUDECODE',
        'CLAUDE_CODE',
        'CLAUDE_CODE_IS_COWORK',
        'COPILOT_MODEL',
        'COPILOT_ALLOW_ALL',
        'COPILOT_GITHUB_TOKEN',
        'COPILOT_CLI',
        'REPL_ID',
        'ANTIGRAVITY_AGENT',
        'PI_CODING_AGENT',
        'KIRO_AGENT_PATH',
        'MISTRAL_API_KEY',
    ] as $var) {
        putenv($var);
    }

    unset($GLOBALS['__mock_file_exists']);
});

afterEach(function (): void {
    foreach ([
        'AI_AGENT',
        'CURSOR_AGENT',
        'GEMINI_CLI',
        'CODEX_SANDBOX',
        'CODEX_CI',
        'CODEX_THREAD_ID',
        'AUGMENT_AGENT',
        'OPENCODE_CLIENT',
        'OPENCODE',
        'AMP_CURRENT_THREAD_ID',
        'CLAUDECODE',
        'CLAUDE_CODE',
        'CLAUDE_CODE_IS_COWORK',
        'COPILOT_MODEL',
        'COPILOT_ALLOW_ALL',
        'COPILOT_GITHUB_TOKEN',
        'COPILOT_CLI',
        'REPL_ID',
        'ANTIGRAVITY_AGENT',
        'PI_CODING_AGENT',
        'KIRO_AGENT_PATH',
        'MISTRAL_API_KEY',
    ] as $var) {
        putenv($var);
    }

    unset($GLOBALS['__mock_file_exists']);
});

// Custom agent detection
it('detects a custom agent via AI_AGENT', function (): void {
    putenv('AI_AGENT=my-custom-agent');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('my-custom-agent')
        ->and($result->knownAgent())->toBeNull();
});

it('detects copilot via AI_AGENT github-copilot', function (): void {
    putenv('AI_AGENT=github-copilot');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects copilot via AI_AGENT github-copilot-cli', function (): void {
    putenv('AI_AGENT=github-copilot-cli');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects claude via AI_AGENT claude-code prefix', function (): void {
    putenv('AI_AGENT=claude-code/2.1.123/agent');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('claude')
        ->and($result->knownAgent())->toBe(KnownAgent::Claude);
});

it('detects v0 via AI_AGENT', function (): void {
    putenv('AI_AGENT=v0');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('v0')
        ->and($result->knownAgent())->toBe(KnownAgent::V0);
});

it('does not detect an agent when AI_AGENT is not set', function (): void {
    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

// Known agent env var detection
it('detects cursor via CURSOR_AGENT', function (): void {
    putenv('CURSOR_AGENT=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('cursor')
        ->and($result->knownAgent())->toBe(KnownAgent::Cursor);
});

it('detects gemini via GEMINI_CLI', function (): void {
    putenv('GEMINI_CLI=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('gemini')
        ->and($result->knownAgent())->toBe(KnownAgent::Gemini);
});

it('detects codex via CODEX_SANDBOX', function (): void {
    putenv('CODEX_SANDBOX=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('codex')
        ->and($result->knownAgent())->toBe(KnownAgent::Codex);
});

it('detects codex via CODEX_CI', function (): void {
    putenv('CODEX_CI=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('codex')
        ->and($result->knownAgent())->toBe(KnownAgent::Codex);
});

it('detects codex via CODEX_THREAD_ID', function (): void {
    putenv('CODEX_THREAD_ID=some-thread-id');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('codex')
        ->and($result->knownAgent())->toBe(KnownAgent::Codex);
});

it('detects augment-cli via AUGMENT_AGENT', function (): void {
    putenv('AUGMENT_AGENT=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('augment-cli')
        ->and($result->knownAgent())->toBe(KnownAgent::AugmentCli);
});

it('detects opencode via OPENCODE_CLIENT', function (): void {
    putenv('OPENCODE_CLIENT=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('opencode')
        ->and($result->knownAgent())->toBe(KnownAgent::Opencode);
});

it('detects opencode via OPENCODE', function (): void {
    putenv('OPENCODE=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('opencode')
        ->and($result->knownAgent())->toBe(KnownAgent::Opencode);
});

it('detects amp via AMP_CURRENT_THREAD_ID', function (): void {
    putenv('AMP_CURRENT_THREAD_ID=some-thread-id');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('amp')
        ->and($result->knownAgent())->toBe(KnownAgent::Amp);
});

it('detects claude via CLAUDECODE', function (): void {
    putenv('CLAUDECODE=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('claude')
        ->and($result->knownAgent())->toBe(KnownAgent::Claude);
});

it('detects claude via CLAUDE_CODE', function (): void {
    putenv('CLAUDE_CODE=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('claude')
        ->and($result->knownAgent())->toBe(KnownAgent::Claude);
});

it('detects cowork via Claude Code cowork env vars', function (): void {
    putenv('CLAUDE_CODE=1');
    putenv('CLAUDE_CODE_IS_COWORK=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('cowork')
        ->and($result->knownAgent())->toBe(KnownAgent::Cowork);
});

it('does not detect cowork without Claude Code', function (): void {
    putenv('CLAUDE_CODE_IS_COWORK=1');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('detects copilot via COPILOT_MODEL', function (): void {
    putenv('COPILOT_MODEL=gpt-5.2');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects copilot via COPILOT_ALLOW_ALL', function (): void {
    putenv('COPILOT_ALLOW_ALL=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects copilot via COPILOT_GITHUB_TOKEN', function (): void {
    putenv('COPILOT_GITHUB_TOKEN=token');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects copilot via COPILOT_CLI', function (): void {
    putenv('COPILOT_CLI=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects replit via REPL_ID', function (): void {
    putenv('REPL_ID=some-repl-id');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('replit')
        ->and($result->knownAgent())->toBe(KnownAgent::Replit);
});

it('detects antigravity via ANTIGRAVITY_AGENT', function (): void {
    putenv('ANTIGRAVITY_AGENT=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('antigravity')
        ->and($result->knownAgent())->toBe(KnownAgent::Antigravity);
});

it('detects pi via PI_CODING_AGENT', function (): void {
    putenv('PI_CODING_AGENT=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('pi')
        ->and($result->knownAgent())->toBe(KnownAgent::Pi);
});

it('detects kiro-cli via KIRO_AGENT_PATH', function (): void {
    putenv('KIRO_AGENT_PATH=/usr/local/bin/kiro-cli');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('kiro-cli')
        ->and($result->knownAgent())->toBe(KnownAgent::KiroCli);
});

it('detects Mistral Vibe via MISTRAL_API_KEY', function (): void {
    putenv('MISTRAL_API_KEY=1234');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('vibe')
        ->and($result->knownAgent())->toBe(KnownAgent::Vibe);
});

// Devin detection via file_exists mock
it('detects devin via /opt/.devin file', function (): void {
    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => $path === '/opt/.devin';

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('devin')
        ->and($result->knownAgent())->toBe(KnownAgent::Devin);
});

it('does not detect devin when /opt/.devin does not exist', function (): void {
    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

// Priority order
it('prioritizes AI_AGENT over CURSOR_AGENT', function (): void {
    putenv('AI_AGENT=custom');
    putenv('CURSOR_AGENT=true');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('custom');
});

it('prioritizes CURSOR_AGENT over CLAUDECODE', function (): void {
    putenv('CURSOR_AGENT=true');
    putenv('CLAUDECODE=1');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('cursor');
});

it('prioritizes CLAUDECODE over REPL_ID', function (): void {
    putenv('CLAUDECODE=1');
    putenv('REPL_ID=some-id');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('claude');
});

// Edge cases
it('ignores empty AI_AGENT', function (): void {
    putenv('AI_AGENT=');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('ignores whitespace-only AI_AGENT', function (): void {
    putenv('AI_AGENT=   ');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('trims AI_AGENT value', function (): void {
    putenv('AI_AGENT=  my-agent  ');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('my-agent');
});

it('handles AI_AGENT with special characters', function (): void {
    putenv('AI_AGENT=my-agent/v2.0 (beta)');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('my-agent/v2.0 (beta)');
});

// knownAgent() return values
it('returns correct enum for known agents', function (string $envVar, string $envValue, KnownAgent $expected): void {
    putenv("{$envVar}={$envValue}");

    $result = AgentDetector::detect();

    expect($result->knownAgent())->toBe($expected);
})->with([
    'cursor' => ['CURSOR_AGENT', '1', KnownAgent::Cursor],
    'gemini' => ['GEMINI_CLI', 'true', KnownAgent::Gemini],
    'codex' => ['CODEX_SANDBOX', 'true', KnownAgent::Codex],
    'codex ci' => ['CODEX_CI', 'true', KnownAgent::Codex],
    'v0' => ['AI_AGENT', 'v0', KnownAgent::V0],
    'augment-cli' => ['AUGMENT_AGENT', 'true', KnownAgent::AugmentCli],
    'opencode' => ['OPENCODE_CLIENT', 'true', KnownAgent::Opencode],
    'amp' => ['AMP_CURRENT_THREAD_ID', 'thread-id', KnownAgent::Amp],
    'copilot' => ['COPILOT_CLI', '1', KnownAgent::Copilot],
    'claude' => ['CLAUDECODE', '1', KnownAgent::Claude],
    'replit' => ['REPL_ID', 'id', KnownAgent::Replit],
    'antigravity' => ['ANTIGRAVITY_AGENT', '1', KnownAgent::Antigravity],
    'pi' => ['PI_CODING_AGENT', 'true', KnownAgent::Pi],
    'kiro-cli' => ['KIRO_AGENT_PATH', '/usr/local/bin/kiro-cli', KnownAgent::KiroCli],
    'vibe' => ['MISTRAL_API_KEY', '12345', KnownAgent::Vibe],
]);

it('returns null knownAgent for custom agent', function (): void {
    putenv('AI_AGENT=unknown-agent');

    $result = AgentDetector::detect();

    expect($result->knownAgent())->toBeNull();
});

// Human-friendly labels
it('returns a human-friendly label for each known agent', function (KnownAgent $agent, string $expected): void {
    expect($agent->label())->toBe($expected);
})->with([
    'cursor' => [KnownAgent::Cursor, 'Cursor'],
    'claude' => [KnownAgent::Claude, 'Claude'],
    'cowork' => [KnownAgent::Cowork, 'Cowork'],
    'devin' => [KnownAgent::Devin, 'Devin'],
    'replit' => [KnownAgent::Replit, 'Replit'],
    'gemini' => [KnownAgent::Gemini, 'Gemini'],
    'codex' => [KnownAgent::Codex, 'Codex'],
    'v0' => [KnownAgent::V0, 'v0'],
    'augment-cli' => [KnownAgent::AugmentCli, 'Augment CLI'],
    'opencode' => [KnownAgent::Opencode, 'Opencode'],
    'amp' => [KnownAgent::Amp, 'Amp'],
    'copilot' => [KnownAgent::Copilot, 'Copilot'],
    'antigravity' => [KnownAgent::Antigravity, 'Antigravity'],
    'pi' => [KnownAgent::Pi, 'Pi'],
    'kiro-cli' => [KnownAgent::KiroCli, 'Kiro CLI'],
    'vibe' => [KnownAgent::Vibe, 'Vibe'],
]);

// Standalone function
it('works via standalone detectAgent function', function (): void {
    putenv('CURSOR_AGENT=1');

    $result = detectAgent();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('cursor');
});

// Convenience boolean
it('returns false isAgent when no agent detected', function (): void {
    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->knownAgent())->toBeNull();
});
