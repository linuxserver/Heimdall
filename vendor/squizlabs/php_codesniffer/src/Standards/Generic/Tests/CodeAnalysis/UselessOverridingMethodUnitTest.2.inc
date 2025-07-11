<?php

// Intentional parse error (missing opening bracket). Testing that the sniff is *not* triggered
// in this case.

class FooBar {
    public function __construct()
