<?php

// Intentional parse error (missing double colon after parent keyword). Testing that the sniff is *not* triggered
// in this case.

class FooBar {
    public function __construct() {
        parent
    }
}
