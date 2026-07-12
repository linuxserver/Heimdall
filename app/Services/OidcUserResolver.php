<?php

namespace App\Services;

class OidcUserResolver
{
    public function __construct(
        private array $usernameMap,
        private bool $autoProvision,
        private string $adminBreakGlassUsername,
        private OidcUserRepoContract $userRepo,
    ) {}

    /** @return array{0: ?string, 1: ?string} [resolvedUsername, errorCode] */
    public function resolveUsername(string $oidcUsername): array
    {
        $mapped = $this->usernameMap[$oidcUsername] ?? $oidcUsername;
        if (strcasecmp($mapped, $this->adminBreakGlassUsername) === 0) {
            return [null, 'admin_breakglass_blocked'];
        }
        return [$mapped, null];
    }

    /** @return array{0: ?object, 1: ?string} [user, errorCode] */
    public function findOrProvision(string $username, string $email): array
    {
        $user = $this->userRepo->findByUsername($username);
        if ($user) {
            return [$user, null];
        }
        if (!$this->autoProvision) {
            return [null, 'user_not_found_autoprovision_disabled'];
        }
        $user = $this->userRepo->create($username, $email);
        return [$user, null];
    }
}
