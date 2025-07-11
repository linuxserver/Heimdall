<?php

// Intentional parse error (missing opening curly bracket).
// This should be the only test in this file.
// Testing that the sniff is *not* triggered.

function sniffShouldBailMissingScopeOpener()
