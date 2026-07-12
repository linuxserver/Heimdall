<?php

namespace Tests\Unit;

use App\Services\OidcUserResolver;
use App\Services\OidcUserRepoContract;
use PHPUnit\Framework\TestCase;

class OidcUserResolverTest extends TestCase
{
    public function testMapsUsernameViaEnvMap(): void
    {
        $resolver = new OidcUserResolver(
            usernameMap: ['aaron.jarrett@jarrettequipment.com' => 'aaronckj'],
            autoProvision: false,
            adminBreakGlassUsername: 'admin',
            userRepo: new InMemoryUserRepo([])
        );

        [$resolved, $err] = $resolver->resolveUsername('aaron.jarrett@jarrettequipment.com');

        $this->assertSame('aaronckj', $resolved);
        $this->assertNull($err);
    }

    public function testReturnsIdentityWhenNoMapping(): void
    {
        $resolver = new OidcUserResolver([], false, 'admin', new InMemoryUserRepo([]));
        [$resolved, $err] = $resolver->resolveUsername('Nhung');
        $this->assertSame('Nhung', $resolved);
        $this->assertNull($err);
    }

    public function testRefusesAdminBreakGlass(): void
    {
        $resolver = new OidcUserResolver([], true, 'admin', new InMemoryUserRepo([]));
        [$resolved, $err] = $resolver->resolveUsername('admin');
        $this->assertNull($resolved);
        $this->assertSame('admin_breakglass_blocked', $err);
    }

    public function testRefusesAdminAfterMapping(): void
    {
        $resolver = new OidcUserResolver(
            ['some.user@example.com' => 'admin'],
            true, 'admin', new InMemoryUserRepo([])
        );
        [$resolved, $err] = $resolver->resolveUsername('some.user@example.com');
        $this->assertNull($resolved);
        $this->assertSame('admin_breakglass_blocked', $err);
    }

    public function testRefusesAdminCaseInsensitive(): void
    {
        $resolver = new OidcUserResolver([], true, 'admin', new InMemoryUserRepo([]));
        [$resolved, $err] = $resolver->resolveUsername('ADMIN');
        $this->assertNull($resolved);
        $this->assertSame('admin_breakglass_blocked', $err);
    }

    public function testFindsExistingUserAndDoesNotMutate(): void
    {
        $existing = ['aaronckj' => (object)['id' => 2, 'username' => 'aaronckj', 'email' => 'old@example.com']];
        $repo = new InMemoryUserRepo($existing);
        $resolver = new OidcUserResolver([], false, 'admin', $repo);

        [$user, $err] = $resolver->findOrProvision('aaronckj', 'new@example.com');

        $this->assertNull($err);
        $this->assertSame('aaronckj', $user->username);
        $this->assertSame('old@example.com', $user->email, 'must NOT mutate existing user');
        $this->assertSame(0, $repo->createCount, 'must NOT create when user exists');
    }

    public function testProvisionsNewUserWhenAutoProvisionEnabled(): void
    {
        $repo = new InMemoryUserRepo([]);
        $resolver = new OidcUserResolver([], true, 'admin', $repo);

        [$user, $err] = $resolver->findOrProvision('newuser', 'new@example.com');

        $this->assertNull($err);
        $this->assertSame('newuser', $user->username);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame(1, $repo->createCount);
    }

    public function testRefusesNewUserWhenAutoProvisionDisabled(): void
    {
        $repo = new InMemoryUserRepo([]);
        $resolver = new OidcUserResolver([], false, 'admin', $repo);

        [$user, $err] = $resolver->findOrProvision('newuser', 'new@example.com');

        $this->assertNull($user);
        $this->assertSame('user_not_found_autoprovision_disabled', $err);
        $this->assertSame(0, $repo->createCount);
    }
}

class InMemoryUserRepo implements OidcUserRepoContract
{
    public int $createCount = 0;
    public function __construct(private array $byUsername = []) {}

    public function findByUsername(string $u): ?object
    {
        return $this->byUsername[$u] ?? null;
    }

    public function create(string $u, string $email): object
    {
        $this->createCount++;
        $obj = (object)['id' => count($this->byUsername) + 100, 'username' => $u, 'email' => $email];
        $this->byUsername[$u] = $obj;
        return $obj;
    }
}
