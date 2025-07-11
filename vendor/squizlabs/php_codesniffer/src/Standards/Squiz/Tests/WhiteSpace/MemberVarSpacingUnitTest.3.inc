<?php

// Intentional parse error. Property in nested anonymous class missing modifier.
// This should be the only test in this file.

class NestedAnonClass
{
    public function method($var1, $var2)
    {
        $anon = new class {
            $p1 = null;
        };
    }
}
