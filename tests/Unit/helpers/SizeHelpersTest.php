<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

/**
 * Regression coverage for the byte / size formatting globals in app/Helper.php.
 *
 * These are plain global functions (no framework involved) so a PHP upgrade
 * that changes integer/float division, rounding, or numeric-string casting is
 * exactly the kind of thing that would silently break them. Every assertion
 * pins a concrete expected string/integer so the behaviour is locked down.
 */
class SizeHelpersTest extends TestCase
{
    public function test_format_bytes_uses_drive_size_base_1000_by_default(): void
    {
        // Default $is_drive_size = true => divide by 1000 (simulated HD size).
        $this->assertSame('500B', format_bytes(500));
        $this->assertSame('1KB', format_bytes(1000));
        $this->assertSame('2KB', format_bytes(2000));
        $this->assertSame('1MB', format_bytes(1000000));
        $this->assertSame('1.5MB', format_bytes(1500000));
        $this->assertSame('1GB', format_bytes(1000000000));
        $this->assertSame('2.5GB', format_bytes(2500000000));
        $this->assertSame('1TB', format_bytes(1000000000000));
    }

    public function test_format_bytes_base_1024_when_not_drive_size(): void
    {
        // $is_drive_size = false => divide by 1024 (real byte size).
        $this->assertSame('1KB', format_bytes(1024, false));
        $this->assertSame('1MB', format_bytes(1048576, false));
        $this->assertSame('1GB', format_bytes(1073741824, false));
        $this->assertSame('1.43MB', format_bytes(1500000, false));
    }

    public function test_format_bytes_drive_size_flag_changes_the_result(): void
    {
        // The same byte count must format differently depending on the base.
        $this->assertSame('1MB', format_bytes(1000000, true));
        $this->assertSame('977KB', format_bytes(1000000, false));
    }

    public function test_format_bytes_caps_at_terabytes(): void
    {
        // The unit loop stops at TB (index 4) even for very large inputs.
        $this->assertSame('5TB', format_bytes(5000000000000));
    }

    public function test_format_bytes_applies_before_and_after_unit_strings(): void
    {
        $this->assertSame('1 KBps', format_bytes(1000, true, ' ', 'ps'));
        $this->assertSame('1.43 MB/s', format_bytes(1500000, false, ' ', '/s'));
    }

    public function test_parse_size_resolves_gmk_suffixes_to_bytes(): void
    {
        $this->assertSame(1073741824, parse_size('1g'));
        $this->assertSame(2147483648, parse_size('2G'));
        $this->assertSame(536870912, parse_size('512m'));
        $this->assertSame(131072, parse_size('128k'));
    }

    public function test_parse_size_without_suffix_returns_the_integer_value(): void
    {
        $this->assertSame(1024, parse_size('1024'));
    }
}
