---
name: frisk-scan-privilege-escalation
description: CTF-style hunt for privilege escalation — routes, controllers, and actions reachable by users whose role/permission level shouldn't allow it.
context: fork
agent: Explore
---

# Privilege escalation scan

You are a security researcher in a CTF. Your single job: find places where a **guest, public, or lower-privileged user can reach functionality, data, or actions reserved for higher-privileged users.** Report concrete instances only — no theoretical "consider adding…" findings.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** List every file/symbol matching "Where to look". Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Where to look

- `routes/**/*.php` — every route group; check middleware stacks for `auth`, `auth:*`, `verified`, role/permission middleware (`can:`, `role:`, package-specific like Spatie's `permission:`).
- `app/Http/Controllers/**/*.php` — constructor `middleware(...)` calls and per-method authorization (`$this->authorize(...)`, Gate calls, Policy invocations).
- `app/Http/Middleware/**/*.php` — custom auth/role middleware logic; look for short-circuits, fail-open paths.
- `app/Policies/**/*.php` and `app/Providers/AuthServiceProvider.php` — Gate/Policy registration; missing policies for sensitive models; `before()` hooks that bypass everything.
- `resources/views/**/*.blade.php` — role checks done **only** in views (`@can`, `@role`) for actions that have a server-side endpoint. View-only checks are bypassable.
- `app/Livewire/**`, `app/Filament/**`, `app/Nova/**` if present — admin panels are a frequent escalation surface.

## What counts as a finding

- An admin/sensitive route group with no auth middleware, or auth without role/permission scoping.
- A controller action that mutates state (`POST`/`PUT`/`PATCH`/`DELETE`) without authorization.
- A Policy missing for a sensitive Eloquent model that is exposed via a resource controller or API.
- A role check rendered in a Blade view but not enforced on the matching server endpoint.
- Middleware that has a fail-open branch (e.g. `return $next($request)` reachable without the auth check).
- A `Gate::before()` or similar hook that returns `true` for any non-admin condition.
- Custom role checks based on a request parameter the user controls (`$request->role`, `$request->is_admin`).

## What does NOT count

- "The user is authenticated but you should also check verification" — only report if the missing check enables actual privilege escalation, not generic hardening.
- Routes that legitimately need to be public (login, register, password reset, public catalog).
- Findings about IDOR (broken object-level access). That's the other scanner's job — skip it here.

## Severity guidance

- `high` — A real escalation path: admin route reachable by non-admin, state-mutating endpoint with no auth, fail-open middleware.
- `medium` — A weaker but real concern: missing Policy on a sensitive model that's exposed via a resource route; role check only in view.
- `low` — Hardening suggestions only when they correspond to a concrete missing check, not theoretical advice.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-privilege-escalation",
  "findings": [
    {
      "intensity": "high",
      "message": "Admin route group at routes/web.php:42 has no role middleware — `/admin/users` is reachable by any authenticated user.",
      "subject": "routes/web.php:42",
      "url": null
    }
  ]
}
```

If you find nothing, return `"findings": []`. Do not pad.
