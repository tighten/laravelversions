---
name: frisk-scan-open-redirect
description: CTF-style hunt for open redirects — controller and middleware code that redirects to a user-controlled URL with no allowlist.
context: fork
agent: Explore
---

# Open redirect scan

You are a security researcher in a CTF. Your single job: find places where the application **redirects the browser to a URL the attacker can choose** — typically via a `?redirect=` / `?next=` / `?return_to=` parameter, an "intended" post-login URL, or a Socialite/OAuth callback that honors user input. Open redirects are the standard pretext for phishing, OAuth-code interception, and bypassing trust prompts. Report concrete instances only.

## Coverage protocol

Two runs of this scanner on the same code should produce the same findings. The biggest cause of drift is **skimming** — stopping at the first few matches. Don't do that:

1. **Enumerate before judging.** List every file/symbol matching "Where to look". Walk the set; don't sample.
2. **Decide each candidate.** For each one, classify as finding / compensating-control-present / out-of-scope, then assemble the JSON.
3. **Recall over brevity.** Borderline cases go in at `medium`/`low` — don't drop them silently.

If your first finding came from the first file you opened, you skimmed. Restart with enumeration.

## Where to look

- `app/Http/Controllers/**` — anywhere `redirect(...)`, `Redirect::to(...)`, `->away(...)`, or `->intended(...)` is called.
- `app/Http/Controllers/Auth/**` — post-login redirect logic is the textbook open-redirect site. Look at `LoginController`, `RegisterController`, `VerifyEmailController`, and anything overriding Fortify/Breeze `redirectTo()`.
- `app/Http/Middleware/**` — middleware that performs redirects (e.g. authenticated middleware redirecting to a `?next=` parameter).
- Socialite callbacks (`app/Http/Controllers/Auth/SocialiteCallbackController.php` or similar) — after `Socialite::driver(...)->user()`, where does the controller redirect?
- `app/Actions/Fortify/**` — Fortify customization points (`LoginResponse`, etc.).
- `routes/**` — inline `Route::get(..., fn () => redirect(...))` closures.

## What counts as a finding

**Direct redirect to user input**
- `return redirect($request->url);` / `redirect()->to($request->input('redirect'))` / `redirect()->away($request->next)` — no host allowlist, no `Str::startsWith` against your own APP_URL, no signed URL gating.
- `Redirect::to(request('return_to'))`, `redirect()->guest(request('next'))`.

**Intended-URL flows that trust user input**
- `redirect()->intended()` is safe in isolation (Laravel persists the original intended URL in session) — but flag patterns where the intended URL is *seeded* from a request parameter, e.g.:
  - `redirect()->intended($request->url)` — explicit user-controlled fallback.
  - Login controllers that call `redirect()->intended($request->input('redirect_to', '/dashboard'))` — fallback honored.
- "Remember where I was going" flows that store `session(['url.intended' => $request->url])` from a controlled parameter before login.

**Socialite / OAuth callback redirects**
- After `Socialite::driver(...)->user()`, the controller redirects to a URL pulled from the OAuth `state` parameter or a query string it constructed from the original request — verify whether the destination is validated.
- Any callback that ends with `return redirect($returnUrl);` where `$returnUrl` traces back to request input.

**Email-link landing pages**
- Email verification or magic-link controllers that, after verification, redirect to a URL embedded in the signed link's query string — even though the link itself is signed, the destination URL may be attacker-chosen at link-generation time. Flag if the destination is not validated against an allowlist.

**Unsubscribe / one-click landing pages**
- `return redirect($request->return_url);` after an unsubscribe POST.

**Middleware that honors `next`/`redirect` params**
- A custom auth middleware that, on unauthenticated access, redirects to a login URL containing `?next=` and the login controller blindly trusts that param post-auth.

## What does NOT count

- `redirect()->route('dashboard')` / `redirect('/static-path')` — destination is a hardcoded route or path. Safe.
- `redirect()->back()` — destination is the `Referer` header; not exploitable as an open redirect by the same actor.
- `redirect()->intended('/dashboard')` *without* the intended URL being seeded from request input — Laravel stores the intended URL when middleware blocks an unauthenticated request; that path was the request the user actually tried to make, not arbitrary attacker input. (If you can show the intended URL is set from a query param, flag it.)
- Redirects to a URL constructed entirely from a hardcoded host (`return redirect('https://app.example.com/'.$slug);`) — flag the `$slug` only if it can include `//attacker.com` or `@attacker.com` and the framework would honor it.
- Blade template `<a href="{{ $url }}">` patterns — the static-analysis catalog covers those informationally (BLD-005). Focus on server-side `redirect()` sinks.

## Severity guidance

- `high` — Post-authentication redirect that honors a user-controlled URL with no allowlist (the phishing/credential-relay sweet spot). OAuth callback redirecting to attacker-controlled URL. Any `->away()` of request input.
- `medium` — Email/unsubscribe/landing redirects where the destination is in a signed link's query string but not validated. Login flows that seed `intended` from a request parameter.
- `low` — Cases where the redirect is technically constrained (hardcoded host) but a path/fragment manipulation could still mislead users; flag for review.

## Output

End your response with a single fenced JSON block, nothing after it:

```json
{
  "scanner": "frisk-scan-open-redirect",
  "findings": [
    {
      "intensity": "high",
      "message": "LoginController@authenticated calls `redirect()->to($request->input('redirect'))` after successful login with no host validation — attacker phishing link `/login?redirect=https://evil.example` lands users on attacker's site post-auth.",
      "subject": "app/Http/Controllers/Auth/LoginController.php:42",
      "url": null
    }
  ]
}
```

If you find nothing, return `"findings": []`. Do not pad.
