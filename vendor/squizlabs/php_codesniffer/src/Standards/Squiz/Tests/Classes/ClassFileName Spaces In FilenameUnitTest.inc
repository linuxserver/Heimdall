<?php

// Test to demonstrate that file names should follow class names, not the other way around.
// Making the class name comply with the file name would result in a parse error for this file,
// as the file name doesn't comply with the naming conventions for PHP symbol names.

class ClassFileNameSpacesInFilenameUnitTest {}
