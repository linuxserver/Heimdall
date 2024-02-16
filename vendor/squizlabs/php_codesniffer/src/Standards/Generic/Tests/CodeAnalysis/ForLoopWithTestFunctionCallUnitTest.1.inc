<?php

$a = array(1, 2, 3, 4);
for ($i = 0; $i < count($a); $i++) {
    $a[$i] *= $i;
}

for ($i = 0, $c = sizeof($a); $i < $c; ++$i) {
    $a[$i] *= $i;
}

$it = new ArrayIterator($a);
for ($it->rewind(); $it->valid(); $it->next()) {
    echo $it->current();
}

for ($i = 0; MyClass::staticMethod($value); $i++) {
    echo $i;
}

for ($i = 0; $countFunction($value); $i++) {
    echo $i;
}

$a = array(1, 2, 3, 4);
for ($i = 0; $i < count($a); $i++):
    $a[$i] *= $i;
endfor;

for ($i = 0, $c = sizeof($a); $i < $c; ++$i):
    $a[$i] *= $i;
endfor;

$it = new ArrayIterator($a);
for ($it->rewind(); $it->valid(); $it->next()):
    echo $it->current();
endfor;

for ($i = 0; MyClass::staticMethod($value); $i++) :
    echo $i;
endfor;

for ($i = 0; $countFunction($value); $i++):
    echo $i;
endfor;

for ($i = 0; (new MyClass)->method(); $i++) {
}

for (; $i < 10; ++$i) {}

for (; count($a); ++$i) {}

for ($i = 0;; ++$i) {}

for ($i = 0; $i < 10;) {}

for ($i = 0; count($a);) {}

for (;; $i++) {}

for ($i = 0;;) {}

for (;;) {}

for ($i = 0; (new MyClass)->method(); $i++):
endfor;

for (; $i < 10; ++$i) :
endfor;

for (; count($a); ++$i) :
endfor;

for ($i = 0;; ++$i) :
endfor;

for ($i = 0; $i < 10;) :
endfor;

for ($i = 0; count($a);) :
endfor;

for (;; $i++) :
endfor;

for ($i = 0;;) :
endfor;

for (;;) :
endfor;

for ($i = 0; $i < 10; $i = increment($i)) {}

for ($i = initialValue(); $i < 10; $i = increment($i)) {}
