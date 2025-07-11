<?php
echo $blah;
echo    $blah;
echo($blah);
echo$blah;

print $blah;
print     $blah;
print($blah);
print$blah;

include $blah;
include     $blah;
include($blah);
include$blah;

include_once $blah;
include_once     $blah;
include_once($blah);
include_once$blah;

require $blah;
require     $blah;
require($blah);
require$blah;

require_once $blah;
require_once     $blah;
require_once($blah);
require_once$blah;

$obj = new MyClass();
$obj = new    MyClass();

$obj = new $className();
$obj = new$className();

yield $blah;
yield   $blah;
yield$blah;

yield from $test();
yield FROM   $test();
YIELD   from   $test();
yield   from $test();
yield
from $test();
yield


From $test();
yield FROM$test();

throw new Exception();
throw  new Exception();
throw new   Exception();
throw   new   Exception();

throw $exception;
throw$exception;

namespace MyClass;
namespace    MyClass;
namespace MyNamespace\MyClass;
namespace    MyNamespace\MyClass;

// Namespace operator, not language construct.
namespace\MyNamespace\MyClass;
namespace \MyNamespace\MyClass;
namespace   /*comment*/  \MyNamespace\MyClass;

use MyClass;
use    MyClass;
use MyNamespace\MyClass;
use    MyNamespace\MyClass;
use \MyNamespace\MyClass;
use    \MyNamespace\MyClass;

return;
return $blah;
return   $blah;
return($blah);
return$blah;

return	$tab;
return
$newLine;

// The following line must have a single space at the end (after return)
return 
$spaceAndNewLine;

// Related to issue #529. These should not be auto-fixed as we don't know what to do with the comment.
yield    /*comment*/    from $test();
yield
    # comment
    from $test();
yield
    // phpcs:ignore Stnd.Category.SniffName
    from $test();

// Closure use should be ignored. These are subject to their own rules.
$cl = function() use ($b) {};
$cl = function() use($b) {};

goto MyName;
goto    MyName;
