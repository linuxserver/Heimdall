<?php

class FooBar {
    public function __construct($a, $b) {
        parent::__construct($a, $b);
    }
}

class BarFoo {
    public function __construct($a, $b) {
        parent::__construct($a, 'XML', $b);
    }
}

class Foo {
    public function export($a, $b = null) {
        return parent::export($a, $b);
    }
}

class Bar {
    public function export($a, $b = null) {
        return parent::export($a);
    }

    public function ignoreNoParent($a, $b) {
        return $a + $b;
    }

    public function differentParentMethod($a, $b) {
        return parent::anotherMethod($a, $b);
    }

    public function methodCallWithExpression($a, $b) {
        return parent::methodCallWithExpression(($a + $b), ($b));
    }

    public function uselessMethodCallWithExpression($a, $b) {
        return parent::uselessMethodCallWithExpression(($a), ($b));
    }

    public function contentAfterCallingParent() {
        parent::contentAfterCallingParent();

        return 1;
    }

    public function ignoreNoParentVoidMethod($a, $b) {
        $c = $a + $b;
    }

    public function modifiesParentReturnValue($a, $b) {
        return parent::modifiesParentReturnValue($a, $b) + $b;
    }

    public function uselessMethodCallTrailingComma($a) {
        return parent::uselessMethodCallTrailingComma($a,);
    }

    public function differentParameterOrder($a, $b) {
        return parent::differentParameterOrder($b, $a);
    }

    public function sameNumberDifferentParameters($a, $b) {
        return parent::sameNumberDifferentParameters($this->prop[$a], $this->{$b});
    }

    public function differentCase() {
        return parent::DIFFERENTcase();
    }

    public function differentCaseSameNonAnsiiCharáctêrs() {
        // This should be flagged, only ASCII chars have changed case.
        return parent::DIFFERENTcaseSameNonAnsiiCharáctêrs();
    }

    public function differentCaseDifferentNonAnsiiCharáctêrs() {
        // This should not be flagged as non-ASCII chars have changed case, making this a different method name.
        return parent::DIFFERENTcaseDifferentNonAnsiiCharÁctÊrs();
    }

    public function nestedFunctionShouldBailEarly() {
        function nestedFunctionShouldBailEarly() {
            // Invalid code needed to ensure an error is NOT triggered and the sniff bails early when handling nested function.
            parent::nestedFunctionShouldBailEarly();
        }
    }
}

abstract class AbstractFoo {
    abstract public function sniffShouldBailEarly();

    public function uselessMethodInAbstractClass() {
        parent::uselessMethodInAbstractClass();
    }

    public function usefulMethodInAbstractClass() {
        $a = 1;
        parent::usefulMethodInAbstractClass($a);
    }
}

interface InterfaceFoo {
    public function sniffShouldBailEarly();
}

trait TraitFoo {
    abstract public function sniffShouldBailEarly();

    public function usefulMethodInTrait() {
        parent::usefulMethodInTrait();

        return 1;
    }

    public function uselessMethodInTrait() {
        return parent::uselessMethodInTrait();
    }
}

enum EnumFoo {
    public function sniffShouldBailEarly() {
        // Invalid code needed to ensure an error is NOT triggered and the sniff bails early when handling an enum method.
        parent::sniffShouldBailEarly();
    }
}

function shouldBailEarly() {
    // Invalid code needed to ensure an error is NOT triggered and the sniff bails early when handling a regular function.
    parent::shouldBailEarly();
}

$anon = new class extends ParentClass {
    public function uselessOverridingMethod() {
        parent::uselessOverridingMethod();
    }

    public function usefulOverridingMethod() {
        $a = 10;
        parent::usefulOverridingMethod($a);
    }
};

function foo() {
    $anon = new class extends ParentClass {
        public function uselessOverridingMethod() {
            parent::uselessOverridingMethod();
        }
    };
}

class SniffShouldHandlePHPOpenCloseTagsCorrectly {
    public function thisIsStillAUselessOverride($a, $b) {
        return parent::thisIsStillAUselessOverride($a, $b) ?><?php
        // Even with a comment here.
    }

    public function butNotWithANewLineBetweenThePHPTagsAsThenWeEchoOutTheNewLine($a, $b) {
        parent::butNotWithANewLineBetweenThePHPTagsAsThenWeEchoOutTheNewLine($a, $b) ?>
        <?php
    }

    public function embeddedHTMLAfterCallingParent() {
        parent::embeddedHTMLAfterCallingParent() ?>
        <div>HTML</div>
        <?php
    }

    public function contentAfterUselessEmbedBlock() {
        parent::contentAfterUselessEmbedBlock() ?><?php
        return 1;
    }
}
