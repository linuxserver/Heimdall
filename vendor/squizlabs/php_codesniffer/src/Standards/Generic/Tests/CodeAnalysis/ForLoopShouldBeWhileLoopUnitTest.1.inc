<?php
for ($i = 0; $i < 10; $i++) {
    // Everything is fine
}

for (; $it->valid();) {
    $it->next();
}

for (;(($it1->valid() && $foo) || (!$it2->value && ($bar || false)));/*Could be ignored*/) {
    $it1->next();
    $it2->next();
}

for ($i = 0, $j = 10; $i < $j; $i++, $j--) {
    echo "i: $i, j: $j\n";
}

for (;;) {
    if ($i >= 10) {
        break;
    }
    echo $i++;
}

for ($i = 0; $i < 10; $i++): ?>
    <p><?php echo $i; ?></p>
<?php endfor;

for ($i = 0, $len = count($array); $i < $len; $i++):
    echo $array[$i];
endfor;

for (; $i < 10;):
    echo $i;
    $i++;
endfor;
