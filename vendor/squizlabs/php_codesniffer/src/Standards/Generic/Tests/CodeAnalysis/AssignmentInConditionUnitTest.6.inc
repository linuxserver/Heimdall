<?php

// Intentional parse error. Testing that the sniff is *not* triggered in this case.
switch ( true ) {
    case $sample == 'something'
