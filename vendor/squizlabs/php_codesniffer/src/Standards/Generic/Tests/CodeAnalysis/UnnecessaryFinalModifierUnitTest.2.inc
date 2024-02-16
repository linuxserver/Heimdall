<?php

// Intentional parse error (class without body). Testing that the sniff is *not* triggered
// in this case.
final class Missing_Body
