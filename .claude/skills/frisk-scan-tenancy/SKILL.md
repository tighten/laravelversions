---
name: frisk-scan-tenancy
description: CTF-style hunt for cross-tenant data leakage in multi-tenant apps — queries that skip tenant scoping, jobs that drop tenant context, cache keys missing tenant prefix, shared storage paths.
context: fork
agent: Explore
---

# Multi-tenant boundary scan

You are a security researcher in a CTF. Your single job: find places in a multi-tenant app where **one tenant can read or mutate another tenant's data**. Report concrete instances only.

This scanner is conditional. **First, decide whether the app is multi-tenant**. If it isn't, return an empty findings array — do not invent findings.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** Once Step 1 confirms tenancy, list every file/symbol matching "Where to look" in Step 2. Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Step 1 — Detect tenancy

Look for any of these signals. If none of them are present, the app is single-tenant; return `"findings": []` immediately.

- `composer.json` includes `stancl/tenancy`, `archtechx/tenancy`, `spatie/laravel-multitenancy`, `filament/spatie-laravel-tenancy`, or `laravel/jetstream` (Teams) / `laravel/spark` with `team` features.
- Most user-owned models in `app/Models/` have a column like `team_id`, `tenant_id`, `account_id`, `organization_id`, `org_id`, `workspace_id`, or `company_id`.
- There's a `BelongsToTenant` global scope or a `BootHasTenant` trait registered.
- Migrations reference a `tenants`, `teams`, `organizations`, `workspaces`, or `accounts` table that owns most other tables.

Note in your reasoning which signal(s) you found, and which column name represents the tenant. The findings below should reference that specific column.

## Step 2 — Hunt for boundary violations

### Where to look

- `app/Http/Controllers/**` — every query that loads a tenant-owned model.
- `app/Models/**` — `boot()` methods that register (or fail to register) a global tenant scope; `$dispatchesEvents`.
- `app/Jobs/**` — `handle()` methods that load models; constructors that capture only an id (loses tenant context).
- `app/Console/Commands/**` — artisan commands that iterate models across tenants (often legitimate, but flag if a user can trigger them).
- `app/Filament/**`, `app/Nova/**` — resource definitions; check whether `getEloquentQuery()` is scoped.
- `routes/**` — route-model bindings that bypass scoping.
- `app/Listeners/**`, `app/Observers/**` — event handlers that re-enter the tenant boundary.
- `config/cache.php`, `config/session.php` — to understand whether keys are namespaced per tenant at the driver level.

### What counts as a finding

**Queries that start from the model class instead of the tenant relationship**
- `Project::find($id)` / `Project::findOrFail($id)` / `Project::where('id', $id)->first()` on a tenant-owned model when no global scope is registered for that model. The safe form is `$tenant->projects()->findOrFail($id)` or `Project::where('tenant_id', $tenant->id)->find($id)`.
- Filament/Nova `getEloquentQuery()` that returns `parent::getEloquentQuery()` on a tenant-owned model without a tenant `where`.

**Bypassing global scope**
- `Model::withoutGlobalScope(TenantScope::class)->...` or `withoutGlobalScopes()` on a tenant-owned model anywhere user input can reach. Flag every occurrence; the legitimate ones (admin tooling, console commands run by ops) should still be reviewed.

**Jobs that drop tenant context**
- A job's `__construct(int $projectId)` that captures only the id, then `handle()` does `Project::find($this->projectId)`. If the global scope relies on a runtime "current tenant" set by middleware (Stancl / spatie/laravel-multitenancy), the scope isn't active inside the queue worker — flag.
- A job using `SerializesModels` on a model whose tenant column is fillable, where the worker process doesn't re-establish tenant context before `handle()` runs.

**Cache keys without tenant prefix**
- `Cache::remember("dashboard:{$user->id}", …)` for content that depends on tenant data, when the cache store is shared across tenants and the key doesn't include `tenant_id`/`team_id`. (Stancl's `cache.prefix` rewriting only applies if it's actually configured — check.)
- `Cache::tags(['dashboard'])` shared between tenants.

**Shared storage paths**
- `Storage::disk('public')->put("uploads/{$filename}", …)` where `$filename` is per-tenant but the directory isn't tenant-scoped — collisions across tenants and cross-tenant guessable URLs.
- `storage_path('exports/'.$user->id.'.csv')` — fine if `user_id` is globally unique, but flag when paths are keyed by an id that's only unique within a tenant.

**Route-model binding bypass**
- `Route::get('/projects/{project}', ...)` on a tenant-owned model with no policy and no global scope — implicit binding loads any project by id regardless of tenant.

### What does NOT count

- Genuinely tenant-public data: subscription plans, marketing content, public docs, system templates.
- Admin / super-admin routes that are explicitly meant to cross tenants and have role-based authorization (that's `frisk-scan-privilege-escalation`'s territory).
- IDOR within a single tenant (one user accessing another user's record in the same tenant) — that's `frisk-scan-idor`'s territory.
- Code where tenant scoping is clearly in place (global scope visible in the model's `boot()` method and not bypassed).

## Severity guidance

- `high` — A state-mutating query (update/delete/transfer) on a tenant-owned model that starts from the class with no scope; a job that loads tenant-owned models without re-establishing tenant context; `withoutGlobalScope` reachable from user input.
- `medium` — A read query exposing another tenant's record; cache keys missing tenant prefix on shared cache stores; shared storage paths.
- `low` — Patterns that *could* leak across tenants if scoping were removed (e.g. relying on implicit binding without a defensive `where` clause) — flag for human review.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-tenancy",
  "findings": [
    {
      "intensity": "high",
      "message": "ProjectController@update calls `Project::findOrFail($id)` on a model with a `team_id` column but no global scope — any authenticated user can edit any team's project by guessing the id.",
      "subject": "app/Http/Controllers/ProjectController.php:73",
      "url": null
    }
  ]
}
```

If the app is single-tenant or you find nothing, return `"findings": []`. Do not pad.
