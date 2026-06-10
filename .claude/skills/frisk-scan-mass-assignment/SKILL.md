---
name: frisk-scan-mass-assignment
description: CTF-style hunt for mass-assignment vulnerabilities — `$guarded`/`$fillable` misuse and `$request->all()` flowing into Eloquent.
context: fork
agent: Explore
---

# Mass assignment & input-trust scan

You are a security researcher in a CTF. Your single job: find places where **a user can inject attribute values into an Eloquent model that they shouldn't control** (role changes, ownership transfers, status escalation, foreign key tampering, etc.). Report concrete instances only.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** List every file/symbol matching "Where to look". Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Where to look

- `app/Models/**/*.php` — every Eloquent model's `$fillable`, `$guarded`, `$hidden`, `$casts`. **Also check the Laravel 12+ attribute equivalents** from `Illuminate\Database\Eloquent\Attributes`: `#[Fillable([...])]`, `#[Guarded([...])]`, `#[Hidden([...])]`, and the marker `#[Unguarded]` (= `$guarded = []`). A model may use either form, or mix them — treat them as equivalent when reasoning about what's mass-assignable / what serializes.
- `app/Http/Controllers/**/*.php` — any call to `create(...)`, `update(...)`, `fill(...)`, `make(...)`, `firstOrCreate(...)`, `updateOrCreate(...)`, `forceFill(...)`, `forceCreate(...)`.
- `app/Http/Requests/**/*.php` — what fields `validated()` returns; whether `validated()` is used vs `$request->all()`.
- `app/Http/Resources/**/*.php` — API resources that expose hidden/internal fields.
- `app/Filament/**`, `app/Nova/**`, `app/Livewire/**` — form bindings that expose attributes the user shouldn't write.

## What counts as a finding

- `$guarded = []` (or `#[Unguarded]`, or `#[Guarded([])]`) on a model that has a sensitive attribute (`is_admin`, `role`, `role_id`, `user_id`, `account_id`, `status`, `verified_at`, `email_verified_at`, `team_id`, etc.).
- `$fillable` (or `#[Fillable(...)]`) containing a sensitive attribute on a model exposed via a form or API.
- `Model::create($request->all())`, `->update($request->all())`, `->fill($request->all())`, `->fill(request()->all())` — without intermediate validation that restricts keys.
- `Model::create($request->validated())` where the `FormRequest` validates `is_admin`, `role`, ownership ids, or other sensitive fields.
- `forceFill(...)` or `forceCreate(...)` called with user-controlled data.
- `$hidden` missing on a model that has `password`, `remember_token`, `api_token`, `two_factor_secret`, `two_factor_recovery_codes`, or other secret attributes.
- API resource (`JsonResource`) that returns the entire model via `parent::toArray($request)` for a model with sensitive attributes.
- Filament/Nova form fields bound to sensitive attributes without role-based hiding.

## What does NOT count

- `$guarded = []` on a purely internal model never exposed to user input (e.g. cached aggregates, job payloads).
- Use of `$request->all()` where the controller immediately picks specific keys with `Arr::only` or destructuring.
- Models with `$fillable = ['name']` exposing only safe attributes.

## Severity guidance

- `high` — User-controlled `$request->all()` (or `validated()` that includes sensitive keys) flowing into `create`/`update`/`fill` on a model with role/ownership/status attributes.
- `medium` — Open `$guarded = []` on a sensitive model even if all current callers happen to be safe (regression hazard); missing `$hidden` on secret attributes.
- `low` — Resource classes returning the full model via `parent::toArray()` where attribute-level filtering would be safer.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-mass-assignment",
  "findings": [
    {
      "intensity": "high",
      "message": "UserController@update calls `$user->update($request->all())` on a User model with `$guarded = []` — attacker can set `is_admin=1` by adding a form field.",
      "subject": "app/Http/Controllers/UserController.php:67",
      "url": null
    }
  ]
}
```

If you find nothing, return `"findings": []`. Do not pad.
