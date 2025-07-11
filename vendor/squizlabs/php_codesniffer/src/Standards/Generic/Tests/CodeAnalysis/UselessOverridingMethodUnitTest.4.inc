<?php

// Intentional parse error (missing parent method opening parenthesis).
// Testing that the sniff is *not* triggered in this case.

class FooBar {
    public function __construct() {
        parent::__construct
    }
}
