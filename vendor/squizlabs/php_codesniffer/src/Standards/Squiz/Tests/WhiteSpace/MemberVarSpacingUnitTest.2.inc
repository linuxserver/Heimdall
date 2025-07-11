<?php

// Intentional parse error. Property missing modifier.
// This should be the only test in this file.

class PropertyWithoutModifier
{
    public static function output() {}
    $var = null;
}
