<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

/**
 * Regression coverage for the colour brightness globals in app/Helper.php.
 *
 * get_brightness() and title_color() drive the automatic black/white tile
 * text colour on the dashboard. They rely on hexdec(), substr() and integer
 * maths, all of which are sensitive to PHP behavioural changes, so the
 * expected luminance values and the black/white threshold are pinned here.
 */
class ColorHelpersTest extends TestCase
{
    public function test_get_brightness_returns_full_luminance_for_white(): void
    {
        $this->assertEqualsWithDelta(255, get_brightness('#ffffff'), 0.0001);
    }

    public function test_get_brightness_returns_zero_for_black(): void
    {
        $this->assertEqualsWithDelta(0, get_brightness('#000000'), 0.0001);
    }

    public function test_get_brightness_expands_three_char_hex(): void
    {
        // #fff / #000 must expand to the six-char form before decoding.
        $this->assertEqualsWithDelta(255, get_brightness('#fff'), 0.0001);
        $this->assertEqualsWithDelta(0, get_brightness('#000'), 0.0001);
    }

    public function test_get_brightness_strips_leading_hash_and_other_non_hex(): void
    {
        // A value without the leading # must decode identically.
        $this->assertEqualsWithDelta(255, get_brightness('ffffff'), 0.0001);
    }

    public function test_get_brightness_weights_channels_per_luma_formula(): void
    {
        // (R*299 + G*587 + B*114) / 1000
        $this->assertEqualsWithDelta(76.245, get_brightness('#ff0000'), 0.0001);
        $this->assertEqualsWithDelta(149.685, get_brightness('#00ff00'), 0.0001);
        $this->assertEqualsWithDelta(29.07, get_brightness('#0000ff'), 0.0001);
    }

    public function test_title_color_returns_black_for_bright_colours(): void
    {
        // Brightness > 130 => dark text.
        $this->assertSame(' black', title_color('#ffffff'));
        $this->assertSame(' black', title_color('#00ff00'));
    }

    public function test_title_color_returns_white_for_dark_colours(): void
    {
        // Brightness <= 130 => light text.
        $this->assertSame(' white', title_color('#000000'));
        $this->assertSame(' white', title_color('#0000ff'));
        $this->assertSame(' white', title_color('#ff0000'));
    }
}
