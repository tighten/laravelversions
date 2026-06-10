---
name: frisk-agentic-audit
description: Run Frisk's agentic security audit — dispatch specialized scanner subagents in parallel and write the aggregated results to .frisk/agentic-findings.json so the Frisk CLI can ingest them.
---

# Frisk Agentic Audit (coordinator)

You are coordinating a security audit of this Laravel project. Your job is to dispatch the eleven specialized scanner skills in parallel as forked subagents, collect the JSON each one returns, and write the combined results to disk so the `frisk` CLI can render them alongside its static findings.

## Scanners to dispatch

Dispatch **all eleven** in parallel (single message with multiple Skill invocations). Each runs in a forked Explore subagent — independent context, read-only.

1. `frisk-scan-privilege-escalation` — routes/controllers reachable by users who shouldn't reach them.
2. `frisk-scan-idor` — models loaded by user-supplied ID without ownership scoping.
3. `frisk-scan-mass-assignment` — `$guarded`/`$fillable` misuse and `$request->all()` flowing into models.
4. `frisk-scan-auth-flow` — custom auth/password-reset/OAuth/remember-me/MFA flaws.
5. `frisk-scan-business-logic-injection` — user input into HTTP clients, processes, Blade raw output, file paths.
6. `frisk-scan-sensitive-data-exposure` — tokens/hashes/secrets leaking via responses, logs, error pages.
7. `frisk-scan-race-conditions` — read-then-write on money/credits/quotas without locking; non-idempotent webhook handlers.
8. `frisk-scan-webhook-integrity` — inbound webhook signature verification, replay protection, hand-rolled signed URLs.
9. `frisk-scan-tenancy` — multi-tenant boundary violations (skips itself if the app is single-tenant).
10. `frisk-scan-open-redirect` — controller-side `redirect()` to user-controlled URLs.
11. `frisk-scan-prompt-injection` — user input into LLM message payloads without sandboxing; trusted JSON response driving decisions or re-rendered into Slack/HTML/markdown.

Each scanner is briefed for its own domain. **Do not** instruct them on what to find — they already know.

## Output contract

Each scanner returns a single JSON block of shape:

```json
{
  "scanner": "frisk-scan-<topic>",
  "findings": [
    {
      "intensity": "high" | "medium" | "low",
      "message": "concrete description",
      "subject": "relative/path:line",
      "url": null
    }
  ]
}
```

If a scanner finds nothing, its `findings` array is empty. That is a valid result — do not pad it.

## What to do after all scanners return

1. Aggregate every scanner's result into one file at `.frisk/agentic-findings.json` (the `.frisk/` directory was created by the Frisk installer, so it already exists; if for any reason it does not, create it via the Bash tool with `mkdir -p .frisk` so the user can approve it) with this shape:

   ```json
   {
     "version": 1,
     "generated_at": "<ISO 8601 UTC, e.g. 2026-05-21T12:00:00Z>",
     "scanners": [
       { "scanner": "frisk-scan-privilege-escalation", "findings": [...] },
       { "scanner": "frisk-scan-idor", "findings": [...] },
       { "scanner": "frisk-scan-mass-assignment", "findings": [...] },
       { "scanner": "frisk-scan-auth-flow", "findings": [...] },
       { "scanner": "frisk-scan-business-logic-injection", "findings": [...] },
       { "scanner": "frisk-scan-sensitive-data-exposure", "findings": [...] },
       { "scanner": "frisk-scan-race-conditions", "findings": [...] },
       { "scanner": "frisk-scan-webhook-integrity", "findings": [...] },
       { "scanner": "frisk-scan-tenancy", "findings": [...] },
       { "scanner": "frisk-scan-open-redirect", "findings": [...] },
       { "scanner": "frisk-scan-prompt-injection", "findings": [...] }
     ]
   }
   ```

   Every scanner must appear in `scanners[]` even if its `findings` array is empty — Frisk uses the presence of an entry to confirm the scanner ran.

2. Print a one-line summary to the user in the form:
   `Agentic audit complete: N findings (X high · Y medium · Z low). Run \`php frisk agentic ingest\` to view, or \`php frisk check\` to merge with static findings.`

## Hard rules

- Do not modify any files in the project other than `.frisk/agentic-findings.json`.
- Do not run the scanners sequentially when you can run them in parallel.
- Do not editorialize on the findings or add commentary inside the JSON file — that's the user's job after they read the report.
- If a scanner returns malformed JSON, record `{"scanner": "...", "findings": [], "error": "<short reason>"}` for that scanner and continue.
