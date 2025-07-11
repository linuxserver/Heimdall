<?php

class Foo {
    public final function fooBar() {}
    protected final function fool() {}
    private final function Bar() {}
}

final class Foo_Bar {
    public $foobar;
    public final $FOOBAR = 23; // Parse error, but that's not the concern of this sniff, so report it.
    public final function fooBar() {}

    protected function foo() {}
    protected final function fool() {}

    private function Bar() {}
    private final function Bard() {}
}

final class Bar_Foo {
    public $foobar;
    protected $foo;
    private $bar;

    public function fooBar() {}
    protected function foo() {}
    private function Bar() {}
}

final readonly class Foo_Bar {
    public final function fooBar() {}
    final protected function fool() {}
}

final class Final_Class_Final_Constants {
    final public const FINAL_PUBLIC_CONST = 23;
    protected final const FINAL_PROTECTED_CONST = 'foo';
}

final class Final_Class_Regular_Constants {
    public const PUBLIC_CONST = 23;
    protected const PROTECTED_CONST = 'foo';
    private const PRIVATE_CONST = true;
}

class Regular_Class_Final_Constants {
    public final const FINAL_PUBLIC_CONST = 23;
    final protected const FINAL_PROTECTED_CONST = 'foo';
}

class Regular_Class_Regular_Constants {
    public const PUBLIC_CONST = 23;
    protected const PROTECTED_CONST = 'foo';
    private const PRIVATE_CONST = true;
}

final class Final_Class_Final_Properties {
    final readonly public ?MyType $final_public;
    protected final $final_protected = 'foo';
}

final class Final_Class_Regular_Properties {
    public $public = 23;
    protected string $protected = 'foo';
    private $private = true;
}

class Regular_Class_Final_Properties {
    public static final $final_public = 23;
    final readonly $final_protected = 'foo';
}

class Regular_Class_Regular_Properties {
    public $public = 23;
    protected $protected = 'foo';
    private static bool $private = true;
}
