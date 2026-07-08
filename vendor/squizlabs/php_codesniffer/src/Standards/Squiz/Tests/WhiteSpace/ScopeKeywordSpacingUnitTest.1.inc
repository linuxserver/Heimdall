<?php
class MyClass
{
    public static $var = null;
    protected $var = null;

    public   static  $var = null;
    protected  $var = null;

    private function myFunction() {}
    public static function myFunction() {}

    private   function myFunction() {}
    public  static function myFunction() {}
    private static   function myFunction() {}

    private  static
        function myFunction() {}

    public static function output()
    {
        // New in PHP 5.3
        static::bar();
    }

    public static$var = null;

    public
    static
    $var = null;
}

abstract class Foo
{
    public static function getInstance()
    {
        return new static();
    }
}

if ($geometry instanceof static      || $geometry instanceof static) {
    echo 'foo';
}

class MyClass1 {
    use HelloWorld { sayHello as private; }
}

abstract class Foo
{
    public static function getInstance()
    {
        return new /* comment */   static();
    }

    public static function output()
    {
        static   /* comment */ :: bar();
    }
}

class MyOtherClass
{
    public
        $varK = array( 'a', 'b' );

    protected    $varK,
        $varL,
        $varM;

    protected static
        $varK, $varL, $varM;

    private
        $varO = true,
        $varP = array( 'a' => 'a', 'b' => 'b' ),
        $varQ = 'string',
        $varR = 123;

    public
        $varS,
        $varT,
        $varU;

    // Issue #3188 - static as return type.
    public static function staticAsReturnType($attributes = []): static
    {
        return static::factory()->create($attributes);
    }

    public static function nullableStaticReturnType($attributes = []): ?static
    {
        return static::factory()->create($attributes);
    }

    // Also account for static used within union types.
    public function staticLast($attributes = []): object|static {}
    public function staticMiddle(): string|static|object {}
    public function staticFirst(): static|object {}
}

// Ensure that static as a scope keyword when preceeded by a colon which is not for a type declaration is still handled.
$callback = $cond ? get_fn_name() : static  function ($a) { return $a * 10; };

class TypedProperties {
    public
        int $var;

    protected string $stringA, $stringB;

    private   bool
        $boolA,
        $boolB;
}

// PHP 8.0 constructor property promotion.
class ConstructorPropertyPromotionTest {
    public function __construct(
        public    $x = 0.0,
        protected $y = '',
        private   $z = null,
        $normalParam,
    ) {}
}

class ConstructorPropertyPromotionWithTypesTest {
    public function __construct(protected   float|int $x, public?string &$y = 'test', private mixed $z) {}
}

// PHP 8.1 readonly keywords.
class ReadonlyTest {
    public readonly int $publicReadonlyProperty;

    protected    readonly    int $protectedReadonlyProperty;

    readonly protected int $protectedReadonlyProperty;

    readonly   private   int $privateReadonlyProperty;

    public function __construct(readonly  protected  float|int $x, public readonly?string &$y = 'test') {}
}

// PHP 8.2 readonly classes.
readonly class ReadonlyClassTest {}
readonly    class ReadonlyClassTest {}

// PHP 8.3 readonly anonymous classes.
$anon = new readonly class {};
$anon = new readonly    class {};

class FinalTest {
    final      public static function create(ContainerInterface $container) {}
}

final 	  class FinalTest2 {
}

final
    readonly class FinalTest3 {}

class FinalTest4 {
    final     const X = "foo";
    final   public const Y = "bar";
}

abstract    class AbstractTest {
    abstract    public function foo();
}

final class FinalSpacingCorrect {
    public final const SPACING_CORRECT = true;
}

abstract class AbstractSpacingCorrect {
    public abstract function spacingCorrect() {}
}

$closure = static function() { return 'spacing correct'; };
$closure =   static     function() { return 'spacing incorrect'; };

class ConstantVisibility {
    public const PUBLIC_SPACING_CORRECT = true;
    protected const PROTECTED_SPACING_CORRECT = true;
    private const PRIVATE_SPACING_CORRECT = true;

    public     const PUBLIC_SPACING_INCORRECT = true;
    protected  const PROTECTED_SPACING_INCORRECT = true;
    private    const PRIVATE_SPACING_INCORRECT = true;
}

class FinalProperties {
    final readonly public ?MyType $spacing_correct;
    protected   final   $spacing_incorrect = 'foo';
}

class AsymVisibility {
    public(set)      string $asymPublic  = 'hello';
    public  protected(set)   final  $asymProtected  = 'hello';
    private(set)     public   string|false $asymPrivate  = 'hello';

    public public(set) $asymPublicPublic  = 'hello';
    protected(set) public $asymPublicProtected  = 'hello';
    protected private(set) $asymProtectedPrivate  = 'hello';
}

abstract class AbstractProperties {
    abstract public ?MyType $spacing_correct {get;}
    protected   abstract   $spacing_incorrect { set; }
}
