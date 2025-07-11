<?php

// Intentional parse error - unsupported asym visibility used on class constant.
// Testing that the sniff will flags this as the constant doesn't have a valid visibility.
class Foo {
    public(set) const BAR = 'bar';
}
