# Keep It Out of Git — post-install setup (for coding agents)

**Status: bootstrap only — not fully configured until you finish this checklist.**

The installer may have skipped files because this project already had AGENTS.md,
CI, gitleaks, or scripts. Do **not** assume every guard is active.

Profile this run: **portable core**

## Written this run

- .agents/skills/sensitive-data-git/
- .agents/skills/safe-demo-data/
- .agents/skills/sensitive-path-guard/
- .agents/skills/gitleaks-guard/
- scripts/check-sensitive-paths.sh
- AGENTS.md (keep-it-out-of-git block)
- gitleaks.toml
- .github/workflows/sensitive-data.yml

## Skipped this run (already existed, or not in profile)

- (none skipped)

## Required agent tasks (adapt to THIS repo)

1. **Inventory** — Confirm which of these exist and are intended:
   - `.agents/skills/sensitive-data-git`
   - `.agents/skills/safe-demo-data`
   - `.agents/skills/sensitive-path-guard`
   - `.agents/skills/gitleaks-guard`
   - `scripts/check-sensitive-paths.sh`
   - `gitleaks.toml`
   - `.github/workflows/sensitive-data.yml` and/or steps inside existing CI
   - Keep It Out of Git block in `AGENTS.md`
2. **Private storage** — Set the real gitignored path for this stack in `AGENTS.md`
   (Laravel `storage/app/private/`, Nest `storage/private/`, etc.).
3. **Path script** — Adjust allow/block regexes to this tree; run
   `bash scripts/check-sensitive-paths.sh` (Git Bash/WSL on Windows).
4. **Gitleaks** — If `gitleaks.toml` is missing, add it from the skill templates.
   If the project already has secret scanning, integrate rather than duplicate blindly.
5. **CI** — Prefer wiring path check + gitleaks into the project's **existing**
   workflow if one exists; the starter `sensitive-data.yml` is optional/additive
   and must not silently replace `ci-cd.yml` / `tests.yml`.
6. **Safe demo data** — Review seeders/factories: fictional identities, refuse
   production, secrets from env. Init does **not** rewrite application seeders.
7. **Commit** — `git add` the bootstrap files the human wants to keep; open a PR
   if the default branch is protected.
8. **Ask before destructive merges** — Do not `--force` overwrite human-tuned
   AGENTS/CI/gitleaks without confirmation.

## Skills to read

- `.agents/skills/sensitive-data-git/SKILL.md`
- `.agents/skills/safe-demo-data/SKILL.md`
- `.agents/skills/sensitive-path-guard/SKILL.md`
- `.agents/skills/gitleaks-guard/SKILL.md`

Pack upstream: https://github.com/Sultonisky/keep-it-out-of-git
