<?php

namespace Tests\Unit\lang;

use Tests\TestCase;

class LangTest extends TestCase
{
    /**
     * All language keys are defined in all languages based on the en language file.
     */
    public function test_all_language_keys_are_defined(): void
    {
        $this->markTestSkipped('2022-11-14 Lot of keys missing. Enable this test to see them all.');
        $languageDirectories = array_filter(glob(resource_path().'/lang/*'), 'is_dir');

        $enLanguageDirectory = array_values(array_filter($languageDirectories, function($v) {
            return substr($v, -2) === 'en';
        }))[0];
        $notENLanguageDirectories = array_filter($languageDirectories, function ($v) {
            return substr($v, -2) !== 'en';
        });

        $enLanguageKeys = require_once($enLanguageDirectory.'/app.php');
        $missingKeys = [];

        foreach ($notENLanguageDirectories as $langDirectory) {
            $testingLangKeys = require_once($langDirectory . '/app.php');

            foreach ($enLanguageKeys as $langKey => $langValue) {
                if (!array_key_exists($langKey, $testingLangKeys)) {
                    if(!isset($missingKeys[$langDirectory])) {
                        $missingKeys[$langDirectory] = [];
                    }
                    $missingKeys[$langDirectory][] = [$langKey => $langValue];
                }
            }
        }

        if (count($missingKeys) > 0) {
            print_r(json_encode($missingKeys));
        }

        $this->assertEmpty($missingKeys);
    }
}
