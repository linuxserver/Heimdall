<?php

namespace Tests\Unit\database\seeders;

use App\Item;
use App\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * All language keys are defined in all languages based on the en language file.
     */
    public function test_returns_a_jsonmap_with_same_amount_of_items_as_language_directories_present(): void
    {
        $languageDirectories = array_filter(glob(lang_path().'/*'), 'is_dir');

        $languageMap = json_decode(SettingsSeeder::getSupportedLanguageMap(), true);

        $this->assertTrue(count($languageMap) === count($languageDirectories));
    }

    public function test_seeds_the_default_tag_setting(): void
    {
        $this->seed();

        $setting = Setting::where('key', 'default_tag')->first();

        $this->assertNotNull($setting);
        $this->assertSame('select', $setting->type);
        $this->assertSame(4, (int) $setting->group_id);
    }

    public function test_default_tag_edit_value_lists_all_tags_and_a_none_option(): void
    {
        $this->seed();

        Item::factory()->create([
            'title' => 'Home',
            'url' => 'home-dashboard',
            'type' => 1,
            'user_id' => 0,
        ]);
        Item::factory()->create([
            'title' => 'Media',
            'url' => 'media',
            'type' => 1,
            'user_id' => 0,
        ]);

        $setting = Setting::where('key', 'default_tag')->first();
        $editValue = $setting->edit_value;

        // A "none" option with an empty value, using the shared translation key.
        $this->assertStringContainsString('<option value="" ', $editValue);
        $this->assertStringContainsString(__('app.options.none'), $editValue);

        // One option per tag: the slug as the value, the raw title as the label.
        $this->assertStringContainsString('value="home-dashboard"', $editValue);
        $this->assertStringContainsString('>Home</option>', $editValue);
        $this->assertStringContainsString('value="media"', $editValue);
        $this->assertStringContainsString('>Media</option>', $editValue);
    }
}
