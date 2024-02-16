<?php

// Intentional parse error.
// The following function has a simulated git conflict for testing.
// This is not a merge conflict - it is a valid test case.
// Please do not remove.
function test()
    {
        $arr = array(
            'a' => 'a'
<<<<<<< HEAD
            'b' => 'b'
=======
            'c' => 'c'
>>>>>>> master
        );
    }
