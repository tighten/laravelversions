---
name: frisk-scan-sensitive-data-exposure
description: CTF-style hunt for tokens, hashes, secrets, and PII leaking via API responses, logs, error pages, or cached views.
context: fork
agent: Explore
---

# Sensitive data exposure scan

You are a security researcher in a CTF. Your single job: find places where the application **leaks secret or sensitive data** — API tokens, password hashes, MFA secrets, full PII, internal IDs, environment values — via responses, logs, error pages, headers, or rendered views. Report concrete instances only.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** List every file/symbol matching "Where to look". Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Where to look

- `app/Http/Resources/**/*.php` — `toArray()` methods; check whether sensitive attributes are filtered out.
- `app/Http/Controllers/**/*.php` — controllers returning models directly (`return $user;`) or via `->toJson()` / `->toArray()`.
- `app/Models/**/*.php` — `$hidden`, `$visible`, `$appends`; mutators that expose hashed values.
- `app/Exceptions/Handler.php`, `bootstrap/app.php` — exception rendering; whether stack traces or context can reach prod.
- All source — calls to `Log::*`, `info()`, `error()`, `report()` that include `$request`, `->password`, `->token`, `->api_token`, `->secret`, full model dumps.
- `routes/**/*.php` — `Route::get('/debug', ...)`, `dd()`/`dump()` reachable in non-dev routes.
- `resources/views/**/*.blade.php` — `{{ config('...') }}` echoing secrets, `dd()`/`dump()` calls.
- `config/**/*.php` — `'debug' => env('APP_DEBUG', true)` (default true is the bug), Telescope/Horizon enabled without middleware gates.

## What counts as a finding

**API responses / resources**
- Resource class returns `parent::toArray($request)` on a model that includes `password`, `remember_token`, `api_token`, `two_factor_secret`, `stripe_secret_key`, etc.
- Controller returns `return $user;` for a model that has any of the above (Eloquent auto-serializes, but `$hidden` is the only defense — easy to miss).
- API endpoint returns internal-only fields like `team_id`, `account_id`, `created_by` to a user who shouldn't see them.

**Logging**
- `Log::info($request->all())`, `Log::*($request->password)`, dumping a User model into logs.
- Stripe/Paddle/Plaid webhook handlers logging the full payload (often contains card tokens, customer secrets).
- Calls to `report()` with sensitive context.

**Errors in prod**
- `APP_DEBUG=true` default in `config/app.php`.
- Custom exception handler that returns `$exception->getMessage()` or stack trace as JSON to API clients.
- Ignition / debug pages enabled in production via missing env gating.

**Debug surfaces in prod**
- `dd()`, `dump()`, `var_dump`, `print_r` in controllers, services, jobs, or views.
- `/debug`, `/_ignition`, `/telescope`, `/horizon` routes without `auth`+role gating.
- `phpinfo()` reachable through any route.

**Cache & view leaks**
- Cached views containing secrets (Blade `{{ config('services.x.secret') }}` rendered into a public page).
- `Cache::put` of full request payloads.
- View composers pulling secret env values into shared view data.

**Header / cookie leaks**
- Sensitive data placed in custom response headers visible to JS.
- `Session::put('api_token', $token)` for a token meant for backend use only.

## What does NOT count

- Returning user emails in API responses to that same user (not a leak — it's their own data).
- Hashed passwords stored in DB (the storage of a hash is fine; *exposing* it via API is the bug).
- Telescope/Horizon present in dev-only environments.
- Anything inside `.env.example` (committed defaults like `APP_DEBUG=true`, look-alike secrets, etc.) — that's owned by the static catalog (ENV-015, ENV-016, CFG-050). Skip even if it matches a pattern above; Frisk's regex checks already flag it.

## Severity guidance

- `high` — Resource/controller leaking `password`, `api_token`, `two_factor_secret`, or any third-party secret to clients. Debug page reachable in prod. Stack traces in API responses.
- `medium` — Logging of `$request->all()` or full User models. Missing `$hidden` on secret attributes even if no current endpoint exposes them (regression hazard).
- `low` — Internal IDs (foreign keys) leaked to clients that don't need them.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-sensitive-data-exposure",
  "findings": [
    {
      "intensity": "high",
      "message": "UserResource returns `parent::toArray($request)` on a model that includes `api_token` and `two_factor_secret` — both are exposed in API responses.",
      "subject": "app/Http/Resources/UserResource.php:18",
      "url": null
    }
  ]
}
```

If you find nothing, return `"findings": []`. Do not pad.
