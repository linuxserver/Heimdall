<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EloquentOidcUserRepo;
use App\Services\OidcUserResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jumbojett\OpenIDConnectClient;

// NOTE: jumbojett uses raw $_SESSION for OIDC state/nonce. Verify Phase 5 that
// state validation actually works under Laravel's session middleware — if not,
// add a session_start() shim or inject a custom OIDC session handler.
class OidcController extends Controller
{
    public function login(Request $request)
    {
        if (!config('services.oidc.enabled')) {
            return redirect('/login?password=1');
        }
        try {
            $oidc = $this->client();
            $oidc->setRedirectURL(config('services.oidc.redirect_uri'));
            $oidc->addScope(config('services.oidc.scopes'));
            $oidc->authenticate(); // 302 to Authentik
        } catch (\Throwable $e) {
            Log::error('OIDC login redirect failed', ['err' => $e->getMessage()]);
            return redirect('/login?password=1&oidc_error=login_redirect_failed');
        }
        Log::error('OIDC authenticate() returned unexpectedly without redirecting');
        return redirect('/login?password=1&oidc_error=login_redirect_unexpected_return');
    }

    public function callback(Request $request)
    {
        if (!config('services.oidc.enabled')) {
            return redirect('/login?password=1');
        }
        try {
            $oidc = $this->client();
            $oidc->setRedirectURL(config('services.oidc.redirect_uri'));
            $oidc->addScope(config('services.oidc.scopes'));
            $oidc->authenticate();
        } catch (\Throwable $e) {
            Log::error('OIDC callback authenticate() failed', [
                'err' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect('/login?password=1&oidc_error=callback_failed');
        }

        $sub      = $oidc->getVerifiedClaims('sub');
        $userInfo = $oidc->requestUserInfo();
        $email    = $userInfo->email ?? '';
        $username = $userInfo->preferred_username ?? $email;
        $name     = $userInfo->name ?? $username;

        Log::info('OIDC userinfo', compact('sub', 'username', 'email', 'name'));

        $map = $this->parseUsernameMap(config('services.oidc.username_map') ?? '');
        $resolver = new OidcUserResolver(
            usernameMap: $map,
            autoProvision: (bool)config('services.oidc.auto_provision'),
            adminBreakGlassUsername: config('services.oidc.admin_breakglass_username'),
            userRepo: new EloquentOidcUserRepo(),
        );

        [$heimdallUsername, $err] = $resolver->resolveUsername($username);
        if ($err) {
            Log::warning('OIDC username resolve refused', ['err' => $err, 'username' => $username]);
            return redirect('/login?password=1&oidc_error=' . $err);
        }

        [$user, $err] = $resolver->findOrProvision($heimdallUsername, $email);
        if ($err) {
            Log::warning('OIDC find-or-provision refused', ['err' => $err, 'username' => $heimdallUsername]);
            return redirect('/login?password=1&oidc_error=' . $err);
        }

        Auth::login($user, true);
        session(['current_user' => $user]);
        Log::info('OIDC login success', ['username' => $heimdallUsername, 'sub' => $sub]);
        return redirect('/');
    }

    private function parseUsernameMap(string $raw): array
    {
        $out = [];
        if ($raw === '') return $out;
        foreach (explode(',', $raw) as $pair) {
            $parts = explode(':', $pair, 2);
            if (count($parts) !== 2) continue;
            $out[trim($parts[0])] = trim($parts[1]);
        }
        return $out;
    }

    private function client(): OpenIDConnectClient
    {
        $oidc = new OpenIDConnectClient(
            config('services.oidc.issuer'),
            config('services.oidc.client_id'),
            config('services.oidc.client_secret'),
        );
        $oidc->setVerifyHost(true);
        $oidc->setVerifyPeer(true);
        return $oidc;
    }
}
