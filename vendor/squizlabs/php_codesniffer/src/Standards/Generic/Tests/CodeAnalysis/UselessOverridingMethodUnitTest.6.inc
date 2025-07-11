<?php

// Intentional parse error (missing closing parenthesis in parent method call).
// Testing that the sniff is *not* triggered in this case.

class FooBar {
    public function __construct() {
        parent::__construct(
    }
}
