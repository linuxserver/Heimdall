<?php

for ($same = 0; $same < 20; $same++) {
    for ($j = 0; $j < 5; $same += 2) {
        for ($k = 0; $k > 3; $same++) {
            
        }
    }
}

for ($i = 0; $i < 20; $i++) {
    for ($j = 0; $j < 5; $j += 2) {
        for ($k = 0; $k > 3; $k++) {
            
        }
    }
}

for ($i = 0; $i < 20; $i++) {
    for ($same = 0; $same < 5; $same += 2) {
        for ($k = 0; $k > 3; $same++) {
            
        }
    }
}

for (; $i < 10; $i++) {
    for ($j = 0;; $j++) {
        if ($j > 5) {
            break;
        }
        for (;; $k++) {
            if ($k > 5) {
                break;
            }
        }
    }
}

for (; $same < 10; $same++) {
    for ($j = 0;; $same++) {
        if ($j > 5) {
            break;
        }
        for (;; $same++) {
            if ($k > 5) {
                break;
            }
        }
    }
}

for ($i = 0; $i < 20; $i++) :
    for ($j = 0; $j < 5; $j += 2) :
    endfor;
endfor;

for ($same = 0; $same < 20; $same++) :
    for ($j = 0; $j < 5; $same += 2) :
    endfor;
endfor;

// Sniff bails early when there is no incrementor in the third expression of the outer for loop.
for ($same = 0; $same < 10;) {
    ++$same;
    for ($j = 0; $j < 5; $same++) {}
}

for ($i = 1, $same = 0; $i <= 10; $i++, $same++) {
    for ($same = 0, $k = 0; $k < 5; $same++, $k++) {}
}

for ($i = 20; $i > 0; $i--) {
    for ($j = 5; $j > 0; $j -= 2) {
        for ($k = 3; $k > 0; $k--) {}
    }
}

for ($same = 20; $same > 0; $same--) {
    for ($j = 5; $j > 0; $same -= 2) {
        for ($k = 3; $k > 0; $same--) {}
    }
}

for ($i = 0; $i < 20; $i++);

for ($same = 0; $same < 20; $same++) {
    for ($j = 0; $j < 20; $same++);
}
