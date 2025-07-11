<?php

// Intentional parse error (missing class constant value).
// This should be the only test in this file.
// Testing that the sniff is *not* triggered.

class TypedConstants {
    const MISSING_VALUE;
}
