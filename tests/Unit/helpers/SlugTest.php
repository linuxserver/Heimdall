<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

class SlugTest extends TestCase
{
    public function test_slug_returns_valid_tag_for_cn_characters_when_language_is_set_to_en_US(): void
    {
        $tag = str_slug('中文測試', '-', 'en_US');

        $this->assertEquals('zhong-wen-ce-shi', $tag);
    }
}
