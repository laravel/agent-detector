# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased](https://github.com/laravel/agent-detector/compare/v2.0.3...main)

## [v2.0.3](https://github.com/laravel/agent-detector/compare/v2.0.2...v2.0.3) - 2026-09-12

### What's Changed

* Pin GitHub Actions to commit SHAs and add Dependabot config by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/agent-detector/pull/18
* Bump the github-actions group with 2 updates by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/19
* Bump shivammathur/setup-php from 2.37.0 to 2.37.1 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/20
* Add Dependabot cooldown of 5 days by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/agent-detector/pull/21
* Enable Dependabot auto-merge by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/agent-detector/pull/22
* Bump actions/checkout from 6.0.2 to 6.0.3 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/23
* Bump shivammathur/setup-php from 2.37.1 to 2.37.2 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/25
* Bump actions/checkout from 6.0.3 to 7.0.0 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/27
* Bump actions/cache from 5.0.5 to 6.1.0 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/28
* Bump actions/checkout from 7.0.0 to 7.0.1 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/agent-detector/pull/29
* Add detection for JetBrains Junie by [@kamazee](https://github.com/kamazee) in https://github.com/laravel/agent-detector/pull/30

### New Contributors

* [@dependabot](https://github.com/dependabot)[bot] made their first contribution in https://github.com/laravel/agent-detector/pull/19
* [@nunomaduro](https://github.com/nunomaduro) made their first contribution in https://github.com/laravel/agent-detector/pull/21
* [@kamazee](https://github.com/kamazee) made their first contribution in https://github.com/laravel/agent-detector/pull/30

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v2.0.2...v2.0.3

## [v2.0.2](https://github.com/laravel/agent-detector/compare/v2.0.1...v2.0.2) - 2026-04-29

### What's Changed

* [2.x] Detect Claude via AI_AGENT prefix by [@jackbayliss](https://github.com/jackbayliss) in https://github.com/laravel/agent-detector/pull/17

### New Contributors

* [@jackbayliss](https://github.com/jackbayliss) made their first contribution in https://github.com/laravel/agent-detector/pull/17

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v2.0.1...v2.0.2

## [v2.0.1](https://github.com/laravel/agent-detector/compare/v2.0.0...v2.0.1) - 2026-04-28

### What's Changed

* Add label() method to KnownAgent enum by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/agent-detector/pull/16
* Make `AgentDetector::AGENT_ENV_VARS` visible by [@cosmastech](https://github.com/cosmastech) in https://github.com/laravel/agent-detector/pull/15

### New Contributors

* [@cosmastech](https://github.com/cosmastech) made their first contribution in https://github.com/laravel/agent-detector/pull/15

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v2.0.0...v2.0.1

## [v2.0.0](https://github.com/laravel/agent-detector/compare/v1.1.4...v2.0.0) - 2026-04-27

### What's Changed

* Repo cleanup after transfer by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/agent-detector/pull/13
* Cleanup pass on AgentDetector internals by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/agent-detector/pull/14

### Breaking Changes

* Namespace is now `Laravel\AgentDetector` (formerly `AgentDetector`)
* `AgentResult` constructor now only accepts the `$name` (dropped `isAgent` from constructor, kept it as a public readonly property)

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.1.4...v2.0.0

## [v1.1.4](https://github.com/laravel/agent-detector/compare/v1.1.3...v1.1.4) - 2026-04-26

### What's Changed

- feat: add Kiro IDE and Kiro CLI agent detection by [@FlorianHyver](https://github.com/FlorianHyver) in https://github.com/laravel/agent-detector/pull/10
- Improve GitHub Copilot detection by [@pushpak1300](https://github.com/pushpak1300) in https://github.com/laravel/agent-detector/pull/11
- Add v0, Cowork, and CODEX_CI agent detection by [@pushpak1300](https://github.com/pushpak1300) in https://github.com/laravel/agent-detector/pull/12

### New Contributors

- [@FlorianHyver](https://github.com/FlorianHyver) made their first contribution in https://github.com/laravel/agent-detector/pull/10

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.1.3...v1.1.4

## [v1.1.3](https://github.com/laravel/agent-detector/compare/v1.1.2...v1.1.3) - 2026-04-14

### What's Changed

- Add pi detection by [@ssnepenthe](https://github.com/ssnepenthe) in https://github.com/laravel/agent-detector/pull/7

### New Contributors

- [@ssnepenthe](https://github.com/ssnepenthe) made their first contribution in https://github.com/laravel/agent-detector/pull/7

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.1.2...v1.1.3

## [v1.1.2](https://github.com/laravel/agent-detector/compare/v1.1.1...v1.1.2) - 2026-04-09

### What's Changed

- Add Antigravity detection by [@clesecq](https://github.com/clesecq) in https://github.com/laravel/agent-detector/pull/6

### New Contributors

- [@clesecq](https://github.com/clesecq) made their first contribution in https://github.com/laravel/agent-detector/pull/6

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.1.1...v1.1.2

## [v1.1.1](https://github.com/laravel/agent-detector/compare/v1.1.0...v1.1.1) - 2026-04-09

### What's Changed

- Add Copilot CLI detection by [@chris-ware](https://github.com/chris-ware) in https://github.com/laravel/agent-detector/pull/5

### New Contributors

- [@chris-ware](https://github.com/chris-ware) made their first contribution in https://github.com/laravel/agent-detector/pull/5

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.1.0...v1.1.1

## [v1.1.0](https://github.com/laravel/agent-detector/compare/v1.0.2...v1.1.0) - 2026-03-11

### What's Changed

- Concise agent with env vars detection by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/agent-detector/pull/3
- Consoldate cursor cli by [@pushpak1300](https://github.com/pushpak1300) in https://github.com/laravel/agent-detector/pull/4

### New Contributors

- [@joetannenbaum](https://github.com/joetannenbaum) made their first contribution in https://github.com/laravel/agent-detector/pull/3
- [@pushpak1300](https://github.com/pushpak1300) made their first contribution in https://github.com/laravel/agent-detector/pull/4

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.0.2...v1.1.0

## [v1.0.2](https://github.com/laravel/agent-detector/compare/v1.0.1...v1.0.2) - 2026-03-05

### What's Changed

- Add Amp + alias for Codex by [@mateusjatenee](https://github.com/mateusjatenee) in https://github.com/laravel/agent-detector/pull/2

### New Contributors

- [@mateusjatenee](https://github.com/mateusjatenee) made their first contribution in https://github.com/laravel/agent-detector/pull/2

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.0.1...v1.0.2

## [v1.0.1](https://github.com/laravel/agent-detector/compare/v1.0.0...v1.0.1) - 2026-02-12

- Improve Opecode Detection

**Full Changelog**: https://github.com/laravel/agent-detector/compare/v1.0.0...v1.0.1

## v1.0.0 - 2026-02-08

**Initial commit**

**Full Changelog**: https://github.com/laravel/agent-detector/commits/v1.0.0
