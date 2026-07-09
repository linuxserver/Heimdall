<?php

namespace Tests\Feature;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Guards the filesystem configuration the icon / avatar upload paths depend on.
 *
 * config/filesystems.php explicitly pins the local disk root to
 * storage_path('app'). Laravel 12 changed the default local root to
 * storage_path('app/private'); if that pin were lost (or the framework's
 * default disks stopped being merged in) every stored icon path would silently
 * point at the wrong directory. These tests fail loudly if that happens.
 */
class StorageDiskTest extends TestCase
{
    public function test_local_disk_resolves(): void
    {
        $this->assertInstanceOf(Filesystem::class, Storage::disk('local'));
    }

    public function test_public_disk_resolves(): void
    {
        // The public disk comes from the framework defaults merged over the
        // app's partial config/filesystems.php.
        $this->assertInstanceOf(Filesystem::class, Storage::disk('public'));
    }

    public function test_local_disk_root_is_pinned_to_storage_app(): void
    {
        $this->assertSame(storage_path('app'), config('filesystems.disks.local.root'));

        // The resolved absolute path must live directly under storage/app,
        // not the Laravel 12 storage/app/private default.
        $this->assertSame(storage_path('app/icon.png'), Storage::disk('local')->path('icon.png'));
    }

    public function test_public_disk_root_is_storage_app_public(): void
    {
        $this->assertSame(storage_path('app/public'), config('filesystems.disks.public.root'));
    }

    public function test_public_disk_supports_a_put_exists_get_round_trip(): void
    {
        Storage::fake('public');

        $contents = 'icon-bytes';
        Storage::disk('public')->put('icons/test.png', $contents);

        $this->assertTrue(Storage::disk('public')->exists('icons/test.png'));
        $this->assertSame($contents, Storage::disk('public')->get('icons/test.png'));
    }
}
