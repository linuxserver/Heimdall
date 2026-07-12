# Authentik OIDC SSO for Heimdall

This branch adds native OpenID Connect (OIDC) support to Heimdall, letting each user sign in once via Authentik (or any OIDC-compliant provider) and land on their own dashboard — no second login required.

## How it works

- Visiting `/login` shows a prominent **"Sign in with Authentik"** button above the standard password form.
- Clicking the button initiates the OIDC authorization-code flow against your provider.
- On callback, the `preferred_username` claim is mapped (optionally via `OIDC_USERNAME_MAP`) to a Heimdall username. The user is looked up or auto-provisioned, then logged in.
- The Heimdall `admin` account is hard-blocked from OIDC (`OIDC_ADMIN_BREAKGLASS_USERNAME`). Admin logs in via the password form on the same page — no special URL required.
- The "Switch User" panel and `/userselect` route are hidden/bypassed when OIDC is active, so each person sees only their own dashboard.

## Authentik provider setup

1. In Authentik Admin → Applications → Providers → Create:
   - **Type:** OAuth2/OpenID Provider
   - **Client type:** Confidential
   - **Redirect URI:** `https://<your-heimdall-host>/auth/oidc/callback`
   - **Subject mode:** Based on the User's username
   - **Scopes:** `openid`, `email`, `profile`
2. Bind the provider to your Heimdall application.
3. Note the **Client ID**, **Client Secret**, and **Issuer URL** from the discovery document at `https://<authentik-host>/application/o/<app-slug>/.well-known/openid-configuration`.

## Docker image

```bash
docker build --build-arg UPSTREAM_TAG=v2.7.6-ls341 -t heimdall-sso .
docker run -d --name heimdall \
  -e OIDC_ENABLED=true \
  -e OIDC_ISSUER=https://<authentik-host>/application/o/<app-slug>/ \
  -e OIDC_CLIENT_ID=<client-id> \
  -e OIDC_CLIENT_SECRET=<client-secret> \
  -e OIDC_REDIRECT_URI=https://<your-heimdall-host>/auth/oidc/callback \
  -p 80:80 \
  -v /path/to/config:/config \
  heimdall-sso
```

## Environment variables

| Variable | Default | Description |
|---|---|---|
| `OIDC_ENABLED` | `false` | Master switch. `false` = stock Heimdall behaviour. |
| `OIDC_ISSUER` | — | OIDC issuer URL (from discovery doc `issuer` field). |
| `OIDC_CLIENT_ID` | — | OAuth2 client ID from your provider. |
| `OIDC_CLIENT_SECRET` | — | OAuth2 client secret from your provider. |
| `OIDC_REDIRECT_URI` | — | Must match the redirect URI registered in the provider. |
| `OIDC_AUTO_PROVISION` | `true` | Create a Heimdall user on first login if one doesn't exist. |
| `OIDC_ADMIN_BREAKGLASS_USERNAME` | `admin` | Heimdall username that is never authenticated via OIDC. |
| `OIDC_USERNAME_MAP` | `""` | Comma-separated `src:dst` pairs, e.g. `akadmin:aaronckj,other:alias`. Applied before the break-glass check. |
| `OIDC_SCOPES` | `openid,email,profile` | Comma-separated OIDC scopes to request. |

## Break-glass admin login

Visit `/login`. The password form below the Authentik button accepts the local `admin` credentials. No special URL needed.

## Notes

- Tested against Authentik 2026.2.2, PHP 8.4, Laravel 11.45.
- Uses [`jumbojett/openid-connect-php`](https://github.com/jumbojett/OpenID-Connect-PHP) for the OIDC client. Run `composer require jumbojett/openid-connect-php:^1.0` after installing Heimdall's dependencies, or use the provided Dockerfile which handles this automatically.
- jumbojett uses PHP's native `$_SESSION` for state/nonce storage. Heimdall's `file` session driver coexists with it correctly, but verify your session configuration if you switch drivers.
- The `admin` break-glass guard is case-insensitive and applies after username mapping, so mapping an external user to `admin` is also blocked.
