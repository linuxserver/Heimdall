<?php

// Intentional parse error (missing closing curly bracket).
// This should be the only test in this file.
// Testing that the sniff is *not* triggered.

function sniffShouldBailMissingScopeCloser() {
