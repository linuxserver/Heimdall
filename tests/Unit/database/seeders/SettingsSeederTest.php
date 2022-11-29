<?php

namespace Tests\Unit\database\seeders;

use Database\Seeders\SettingsSeeder;
use Tests\TestCase;

class SettingsSeederTest extends TestCase
{
    /**
     * All language keys are defined in all languages based on the en language file.
     *
     * @return void
     */
    public function test_returns_a_jsonmap_with_same_amount_of_items_as_language_directories_present()
    {
        $languageDirectories = array_filter(glob(resource_path().'/lang/*'), 'is_dir');

        $languageMap = json_decode(SettingsSeeder::getSupportedLanguageMap(), true);

        $this->assertTrue(count($languageMap) === count($languageDirectories));
    }
}
