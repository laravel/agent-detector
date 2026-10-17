<?php

declare(strict_types=1);

use Laravel\AgentDetector\AgentDetector;
use Laravel\AgentDetector\KnownAgent;

use function Laravel\AgentDetector\detectAgent;

beforeEach(function (): void {
    $GLOBALS['__original_home'] = getenv('HOME');
    $GLOBALS['__original_user'] = getenv('USER');

    putenv('HOME=/home/agent-detector');
    putenv('USER=agent-detector');

    foreach (['AI_AGENT', 'CLAUDE_CODE_IS_COWORK', 'TERM_PROGRAM', 'PAGER', 'GIT_PAGER', 'GITHUB_ACTIONS', 'COPILOT_AGENT_SESSION_ID', ...array_keys(AgentDetector::AGENT_ENV_VARS)] as $var) {
        putenv($var);
    }

    unset($GLOBALS['__mock_file_exists']);
});

afterEach(function (): void {
    foreach (['AI_AGENT', 'CLAUDE_CODE_IS_COWORK', 'TERM_PROGRAM', 'PAGER', 'GIT_PAGER', 'GITHUB_ACTIONS', 'COPILOT_AGENT_SESSION_ID', ...array_keys(AgentDetector::AGENT_ENV_VARS)] as $var) {
        putenv($var);
    }

    foreach (['HOME' => '__original_home', 'USER' => '__original_user'] as $var => $key) {
        $value = $GLOBALS[$key] ?? false;

        if ($value === false) {
            putenv($var);
        } else {
            putenv("{$var}={$value}");
        }

        unset($GLOBALS[$key]);
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

it('detects crush via AI_AGENT crush', function (): void {
    putenv('AI_AGENT=crush');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('crush')
        ->and($result->knownAgent())->toBe(KnownAgent::Crush);
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

it('detects junie via MATTERHORN_SESSION_ID', function (): void {
    putenv('MATTERHORN_SESSION_ID=session-id');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('junie')
        ->and($result->knownAgent())->toBe(KnownAgent::Junie);
});

it('detects kiro-cli via KIRO_AGENT_PATH', function (): void {
    putenv('KIRO_AGENT_PATH=/usr/local/bin/kiro-cli');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('kiro-cli')
        ->and($result->knownAgent())->toBe(KnownAgent::KiroCli);
});

it('detects cursor via CURSOR_TRACE_ID', function (): void {
    putenv('CURSOR_TRACE_ID=some-trace-id');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('cursor')
        ->and($result->knownAgent())->toBe(KnownAgent::Cursor);
});

it('detects qwen-code via QWEN_CODE', function (): void {
    putenv('QWEN_CODE=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('qwen-code')
        ->and($result->knownAgent())->toBe(KnownAgent::QwenCode);
});

it('detects crush via CRUSH', function (): void {
    putenv('CRUSH=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('crush')
        ->and($result->knownAgent())->toBe(KnownAgent::Crush);
});

it('detects windsurf via WINDSURF_CASCADE_TERMINAL', function (): void {
    putenv('WINDSURF_CASCADE_TERMINAL=1');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('windsurf')
        ->and($result->knownAgent())->toBe(KnownAgent::Windsurf);
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

// Compound environment signal detection
class NonInteractiveTerminalDetector extends AgentDetector
{
    protected static function isInteractiveTerminal(): bool
    {
        return false;
    }
}

class InteractiveTerminalDetector extends AgentDetector
{
    protected static function isInteractiveTerminal(): bool
    {
        return true;
    }
}

class NonCliDetector extends AgentDetector
{
    protected static function isRunningInCli(): bool
    {
        return false;
    }
}

// Windows cannot represent an environment variable that is set to an empty value.
it('detects zed agent via the emptied pager and git pager', function (): void {
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=');
    putenv('GIT_PAGER=cat');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('zed')
        ->and($result->knownAgent())->toBe(KnownAgent::Zed);
})->skipOnWindows();

it('does not detect an agent in a zed terminal via PAGER cat', function (): void {
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=cat');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect an agent in a zed terminal without the git pager', function (): void {
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect an agent in a zed terminal without the pager signals', function (): void {
    putenv('TERM_PROGRAM=zed');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect zed agent via PAGER cat alone', function (): void {
    putenv('PAGER=cat');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect zed outside of the CLI', function (): void {
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=');
    putenv('GIT_PAGER=cat');

    $result = NonCliDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('detects kiro via TERM_PROGRAM kiro in a non-interactive terminal', function (): void {
    putenv('TERM_PROGRAM=kiro');

    $result = NonInteractiveTerminalDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('kiro')
        ->and($result->knownAgent())->toBe(KnownAgent::Kiro);
});

it('does not detect kiro in an interactive terminal', function (): void {
    putenv('TERM_PROGRAM=kiro');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = InteractiveTerminalDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect kiro outside of the CLI', function (): void {
    putenv('TERM_PROGRAM=kiro');

    $result = NonCliDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

/**
 * Runs the detector in a fresh PHP process with stdout bound to the given
 * descriptor, so the real terminal probe is exercised deterministically.
 */
function detectInSubprocess(array $stdoutDescriptor, string $setup = ''): string
{
    $autoload = var_export(realpath(__DIR__.'/../vendor/autoload.php'), true);

    $code = 'require '.$autoload.'; '.$setup.' $name = Laravel\AgentDetector\AgentDetector::detect()->name; fwrite(STDERR, $name ?? "none");';

    $process = proc_open(
        [PHP_BINARY, '-r', $code],
        [1 => $stdoutDescriptor, 2 => ['pipe', 'w']],
        $pipes
    );

    expect($process)->toBeResource();

    $name = stream_get_contents($pipes[2]);

    fclose($pipes[2]);

    if (isset($pipes[1]) && is_resource($pipes[1])) {
        fclose($pipes[1]);
    }

    proc_close($process);

    return trim($name);
}

it('detects kiro with the real terminal probe when stdout is a pipe', function (): void {
    putenv('TERM_PROGRAM=kiro');

    expect(detectInSubprocess(['pipe', 'w']))->toBe('kiro');
});

it('detects kiro with the real terminal probe when stdout is discarded', function (): void {
    putenv('TERM_PROGRAM=kiro');

    $devNull = DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';

    expect(detectInSubprocess(['file', $devNull, 'w']))->toBe('kiro');
});

it('detects kiro with the real terminal probe when stdout is closed', function (): void {
    putenv('TERM_PROGRAM=kiro');

    expect(detectInSubprocess(['pipe', 'w'], 'fclose(STDOUT);'))->toBe('kiro');
});

it('detects copilot via COPILOT_AGENT_SESSION_ID with GITHUB_ACTIONS', function (): void {
    putenv('COPILOT_AGENT_SESSION_ID=session-id');
    putenv('GITHUB_ACTIONS=true');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('does not detect copilot via COPILOT_AGENT_SESSION_ID alone', function (): void {
    putenv('COPILOT_AGENT_SESSION_ID=session-id');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect copilot via GITHUB_ACTIONS alone', function (): void {
    putenv('GITHUB_ACTIONS=true');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('detects copilot via COPILOT_AGENT_SESSION_ID outside of the CLI', function (): void {
    putenv('COPILOT_AGENT_SESSION_ID=session-id');
    putenv('GITHUB_ACTIONS=true');

    $result = NonCliDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('copilot')
        ->and($result->knownAgent())->toBe(KnownAgent::Copilot);
});

it('detects jules via HOME and USER', function (): void {
    putenv('HOME=/home/jules');
    putenv('USER=swebot');

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeTrue()
        ->and($result->name)->toBe('jules')
        ->and($result->knownAgent())->toBe(KnownAgent::Jules);
});

it('does not detect jules via USER alone', function (): void {
    putenv('USER=swebot');
    putenv('HOME=/home/someone');

    $GLOBALS['__mock_file_exists'] = fn (string $path): bool => false;

    $result = AgentDetector::detect();

    expect($result->isAgent)->toBeFalse()
        ->and($result->name)->toBeNull();
});

it('does not detect jules via HOME alone', function (): void {
    putenv('HOME=/home/jules');
    putenv('USER=someone');

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

it('prioritizes CLAUDECODE over CURSOR_TRACE_ID', function (): void {
    putenv('CURSOR_TRACE_ID=some-trace-id');
    putenv('CLAUDECODE=1');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('claude');
});

it('prioritizes QWEN_CODE over GEMINI_CLI', function (): void {
    putenv('QWEN_CODE=1');
    putenv('GEMINI_CLI=true');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('qwen-code');
});

it('prioritizes KIRO_AGENT_PATH over the Kiro IDE signal', function (): void {
    putenv('KIRO_AGENT_PATH=/usr/local/bin/kiro-cli');
    putenv('TERM_PROGRAM=kiro');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('kiro-cli');
});

it('prioritizes CLAUDECODE over compound signals', function (): void {
    putenv('CLAUDECODE=1');
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=cat');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('claude');
});

it('prioritizes AI_AGENT over compound signals', function (): void {
    putenv('AI_AGENT=custom');
    putenv('TERM_PROGRAM=zed');
    putenv('PAGER=cat');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('custom');
});

it('prioritizes grok over CLAUDECODE', function (string $envVar): void {
    putenv("{$envVar}=/tmp/grok");
    putenv('CLAUDECODE=1');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('grok');
})->with(['GROK_PLUGIN_ROOT', 'GROK_PLUGIN_DATA']);

it('prioritizes CLAUDECODE over newer agent env vars', function (string $envVar): void {
    putenv("{$envVar}=1");
    putenv('CLAUDECODE=1');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('claude');
})->with(['KIMI_PLUGIN_ROOT', 'CLINE_ACTIVE', 'GOOSE_TERMINAL', 'JUNIE_DATA', 'JUNIE_SHIM_PATH', 'OPENCLAW_SHELL', 'ZCODE_ENV', 'CRUSH', 'WINDSURF_CASCADE_TERMINAL', 'CURSOR_TRACE_ID']);

it('prioritizes CODEX_SANDBOX over CLINE_ACTIVE', function (): void {
    putenv('CODEX_SANDBOX=1');
    putenv('CLINE_ACTIVE=true');

    $result = AgentDetector::detect();

    expect($result->name)->toBe('codex');
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
    'junie' => ['MATTERHORN_SESSION_ID', 'session-id', KnownAgent::Junie],
    'kiro-cli' => ['KIRO_AGENT_PATH', '/usr/local/bin/kiro-cli', KnownAgent::KiroCli],
    'kimi' => ['KIMI_PLUGIN_ROOT', '/tmp/kimi', KnownAgent::Kimi],
    'grok plugin root' => ['GROK_PLUGIN_ROOT', '/tmp/grok', KnownAgent::Grok],
    'grok plugin data' => ['GROK_PLUGIN_DATA', '/tmp/grok', KnownAgent::Grok],
    'cline' => ['CLINE_ACTIVE', 'true', KnownAgent::Cline],
    'codex sandbox network disabled' => ['CODEX_SANDBOX_NETWORK_DISABLED', '1', KnownAgent::Codex],
    'goose' => ['GOOSE_TERMINAL', '1', KnownAgent::Goose],
    'junie data' => ['JUNIE_DATA', '/tmp/junie', KnownAgent::Junie],
    'junie shim path' => ['JUNIE_SHIM_PATH', '/tmp/junie', KnownAgent::Junie],
    'antigravity cli alias' => ['ANTIGRAVITY_CLI_ALIAS', 'agy', KnownAgent::Antigravity],
    'openclaw' => ['OPENCLAW_SHELL', '1', KnownAgent::OpenClaw],
    'zcode' => ['ZCODE_ENV', '1', KnownAgent::ZCode],
    'cursor trace id' => ['CURSOR_TRACE_ID', 'trace-id', KnownAgent::Cursor],
    'qwen-code' => ['QWEN_CODE', '1', KnownAgent::QwenCode],
    'crush' => ['CRUSH', '1', KnownAgent::Crush],
    'windsurf' => ['WINDSURF_CASCADE_TERMINAL', '1', KnownAgent::Windsurf],
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
    'kimi' => [KnownAgent::Kimi, 'Kimi'],
    'grok' => [KnownAgent::Grok, 'Grok'],
    'cline' => [KnownAgent::Cline, 'Cline'],
    'goose' => [KnownAgent::Goose, 'Goose'],
    'junie' => [KnownAgent::Junie, 'Junie'],
    'openclaw' => [KnownAgent::OpenClaw, 'OpenClaw'],
    'zcode' => [KnownAgent::ZCode, 'ZCode'],
    'qwen-code' => [KnownAgent::QwenCode, 'Qwen Code'],
    'crush' => [KnownAgent::Crush, 'Crush'],
    'windsurf' => [KnownAgent::Windsurf, 'Windsurf'],
    'zed' => [KnownAgent::Zed, 'Zed Agent'],
    'kiro' => [KnownAgent::Kiro, 'Kiro'],
    'jules' => [KnownAgent::Jules, 'Jules'],
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
