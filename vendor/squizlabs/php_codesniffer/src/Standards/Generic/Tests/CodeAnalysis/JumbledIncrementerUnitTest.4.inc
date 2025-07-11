<?php

// Intentional parse error (inner for loop missing opening parenthesis).
// This should be the only test in this file.
// Testing that the sniff is *not* triggered.
for ($i = 0; $i < 20; $i++) {
    for
}
