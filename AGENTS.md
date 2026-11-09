# Agent instructions

<!-- keep-it-out-of-git:start -->
## Keep It Out of Git

People data and secrets do not belong in git. Demo seeders/fixtures may stay in
git only when fictional and environment-guarded.

### Agent: post-install setup required

`npx … init` is only a **bootstrap**. It may skip files that already existed.
Before treating this pack as fully active, the coding agent for **this repo**
must complete project-specific setup:

1. Read `.agents/keep-it-out-of-git.POST_INSTALL.md` (written by the installer)
2. Audit what was installed vs skipped (AGENTS, gitleaks, CI, path script, skills)
3. Fill **This repo uses** private-storage path below for this stack
4. Wire guards into this project's existing CI workflow.
5. Adapt `scripts/check-sensitive-paths.sh` allow/block regexes to this tree
6. Apply `safe-demo-data` to seeders/factories (fictional rows, production refuse,
   passwords from env) — do not assume demo data was fixed by init
7. Ask the human before overwriting existing AGENTS/CI/gitleaks they already tuned

### Private storage (not git)

- **This repo uses:** `storage/app/private/`
- Never force-add gitignored private storage
- Real dumps/imports stay local; use anonymized `Sample…` / `Test…` / Faker in tests

### Guards

- Path check: `bash scripts/check-sensitive-paths.sh` (Git Bash/WSL on Windows)
- Secret scan: gitleaks (see `gitleaks.toml` / CI) when present
- Skills: `.agents/skills/` (optional `.cursor/skills/`)

### Local checks

- Run `bash scripts/check-sensitive-paths.sh` before committing.
- Run `gitleaks detect --source . --config gitleaks.toml --verbose --redact --exit-code 1`.
- Set `SEED_BOOTSTRAP_PASSWORD` to a private password before running local demo seeds.
- Store real imports, exports, and backups outside Git or under `storage/app/private/`.

### Safe demo data

- Seeders/factories/fixtures: fictional only
- Demo seeders must refuse production
- Bootstrap passwords from env/config — never hardcode live secrets

Skill details: `.agents/skills/*/SKILL.md` and `templates.md`.
<!-- keep-it-out-of-git:end -->
