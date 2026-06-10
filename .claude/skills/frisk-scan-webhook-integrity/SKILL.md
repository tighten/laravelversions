---
name: frisk-scan-webhook-integrity
description: CTF-style hunt for unverified or weakly-verified inbound webhooks — missing HMAC checks, timing-unsafe comparisons, missing replay protection, hand-rolled signed-URL verification.
context: fork
agent: Explore
---

# Webhook & signed-payload integrity scan

You are a security researcher in a CTF. Your single job: find inbound webhook endpoints and signed-payload verifiers that **let an attacker forge or replay a request the application treats as trusted** — fake a Stripe payment-success, replay a GitHub push, replay a yesterday's signed download link, etc. Report concrete instances only.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** List every file/symbol matching "Where to look". Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Where to look

- `routes/api.php`, `routes/web.php` — look for routes containing `webhook`, `hook`, `callback`, `notify`, `ipn`, `events`, `incoming`, or paths matching `/stripe`, `/paddle`, `/github`, `/slack`, `/twilio`, `/mailgun`, `/postmark`, `/lemonsqueezy`, `/shopify`, `/pusher`, etc.
- `app/Http/Controllers/Webhooks/**`, `app/Http/Controllers/**Webhook*.php`, `app/Listeners/**` (some apps register webhook handling via Cashier event listeners).
- `app/Http/Middleware/**` — custom middleware that purports to verify signatures.
- `config/cashier.php`, `config/services.php` — to confirm whether a webhook secret env var is wired up at all.
- `app/Support/**`, `app/Services/**` — custom signed-URL helpers (look for `hash_hmac`, `hash_equals`, `URL::hasValidSignature` usage).
- `composer.json` / `composer.lock` — note whether `spatie/laravel-webhook-client`, `laravel/cashier`, `laravel/cashier-paddle`, or a provider SDK is present (those usually verify for you).

## What counts as a finding

**Missing signature verification**
- A controller action that receives a webhook payload from a third party (Stripe, Paddle, GitHub, Slack, Twilio, Mailgun, Shopify, etc.) and dispatches jobs or mutates state **without** verifying the provider's signature header. Provider headers to look for: `Stripe-Signature`, `Paddle-Signature`, `X-Hub-Signature-256` (GitHub), `X-Slack-Signature`, `X-Twilio-Signature`, `X-Mailgun-Signature-256`, `X-Shopify-Hmac-Sha256`.
- A custom webhook endpoint (`/webhooks/internal`, partner integrations) with no shared-secret / HMAC check at all.

**Timing-unsafe signature comparison**
- `if ($expected === $provided)` or `==` on an HMAC value. The fix is `hash_equals($expected, $provided)`. Flag any hand-rolled verification that uses `==`/`===` on a hash, signature, or token.
- `strcmp`/`substr` partial matches on signatures.

**Missing replay protection**
- Signature verified but no timestamp/freshness window check (no use of the provider's timestamp header — Stripe's `t=` inside `Stripe-Signature`, Slack's `X-Slack-Request-Timestamp`, etc.). Old payloads can be replayed indefinitely.
- Webhook handler mutates state without an idempotency check on the event id (no `processed_at`, no unique constraint on `(provider, event_id)`, no `firstOrCreate`). Note: this overlaps with the race-conditions scanner — if you flag it here, frame it as the *replay* attack, not the concurrency attack.

**Hand-rolled signed URLs**
- A controller verifying its own HMAC on a query-string `signature` parameter instead of using `URL::hasValidSignature($request)` / `signed` middleware.
- A custom verifier using `hash_hmac` and then comparing with `==` / `===`.
- A "magic link" / "one-click unsubscribe" / "download token" route whose signature isn't time-bound (no expiry baked into the signed payload).

**Webhook secret misconfiguration in source**
- Webhook secret hardcoded as a literal in a controller rather than read from config/env.
- `if ($secret === '' || $secret === null) { return response('ok'); }` — fail-open when the secret isn't configured.

## What does NOT count

- Webhook handlers that delegate to a package that verifies for you (`Cashier::webhook(...)`, `Spatie\WebhookClient` with a configured `signature_validator`, official provider SDKs). If the verification is happening one layer up, the controller doesn't need to repeat it.
- Outbound HTTP calls — that's SSRF territory (`frisk-scan-business-logic-injection`).
- Webhook handlers that only log/queue the raw payload without acting on its contents. (Still flag if they later get re-read by trusted code, but only if you can trace that flow.)
- Laravel's own `URL::signedRoute` paired with `signed` middleware — that pairing is what RTE-009 in the static catalog checks.

## Severity guidance

- `high` — No signature verification at all on a state-mutating webhook; `==`/`===` on an HMAC; missing replay protection on a payment/subscription/billing webhook; fail-open behavior when the secret env var is empty.
- `medium` — Signature verified but no event-id idempotency check (replay grants entitlements twice); hand-rolled signed URLs with no expiry; missing freshness check on non-billing webhooks.
- `low` — Recommendations to migrate hand-rolled HMAC code to `URL::hasValidSignature` or the relevant provider package.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-webhook-integrity",
  "findings": [
    {
      "intensity": "high",
      "message": "StripeWebhookController@handle reads the JSON body and grants subscription credits without verifying the `Stripe-Signature` header — any attacker who knows the URL can forge a payment success.",
      "subject": "app/Http/Controllers/Webhooks/StripeWebhookController.php:22",
      "url": null
    }
  ]
}
```

If you find nothing, return `"findings": []`. Do not pad.
