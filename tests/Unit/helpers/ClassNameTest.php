<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

/**
 * Regression coverage for className() in app/Helper.php.
 *
 * className() turns a supported-app display name into the PHP class-name
 * fragment used to resolve enhanced-app classes (see Application::single()).
 * It relies on a unicode-aware preg_replace, which is exactly the kind of
 * PCRE behaviour that can change across PHP versions, so the stripping rules
 * and unicode-safety are pinned here.
 */
class ClassNameTest extends TestCase
{
    public function test_strips_spaces_and_punctuation(): void
    {
        $this->assertSame('HomeAssistant', className('Home Assistant'));
        $this->assertSame('Pihole', className('Pi-hole'));
        $this->assertSame('NodeRED', className('Node-RED!'));
    }

    public function test_keeps_digits(): void
    {
        $this->assertSame('App2Go', className('App 2 Go'));
    }

    public function test_is_unicode_safe(): void
    {
        // Letters in other scripts / accented letters must be preserved,
        // only the separators are removed.
        $this->assertSame('CaféServer', className('Café Server'));
        $this->assertSame('中文测试', className('中文 测试'));
    }
}
