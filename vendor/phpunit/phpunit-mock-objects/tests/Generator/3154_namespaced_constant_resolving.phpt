--TEST--
https://github.com/sebastianbergmann/phpunit-mock-objects/issues/420
https://github.com/sebastianbergmann/phpunit/issues/3154
--FILE--
<?php
namespace Is\Namespaced;
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const A_CONSTANT = 17;
const PHP_VERSION = "0.0.0";

class Issue3154
{
    public function a(int $i = PHP_INT_MAX, int $j = A_CONSTANT, string $v = \PHP_VERSION, string $z = '#'): string
    {
        return $z."sum: ".($i+$j).$v;
    }
}
require __DIR__ . '/../../vendor/autoload.php';

$generator = new \PHPUnit\Framework\MockObject\Generator;

$mock = $generator->generate(
    Issue3154::class,
    [],
    'Issue3154Mock',
    true,
    true
);

print $mock['code'];
--EXPECT--
class Issue3154Mock extends Is\Namespaced\Issue3154 implements PHPUnit\Framework\MockObject\MockObject
{
    private $__phpunit_invocationMocker;
    private $__phpunit_originalObject;
    private $__phpunit_configurable = ['a'];

    public function __clone()
    {
        $this->__phpunit_invocationMocker = clone $this->__phpunit_getInvocationMocker();
    }

    public function a(int $i = PHP_INT_MAX, int $j = Is\Namespaced\A_CONSTANT, string $v = PHP_VERSION, string $z = '#'): string
    {
        $arguments = array($i, $j, $v, $z);
        $count     = func_num_args();

        if ($count > 4) {
            $_arguments = func_get_args();

            for ($i = 4; $i < $count; $i++) {
                $arguments[] = $_arguments[$i];
            }
        }

        $result = $this->__phpunit_getInvocationMocker()->invoke(
            new \PHPUnit\Framework\MockObject\Invocation\ObjectInvocation(
                'Is\Namespaced\Issue3154', 'a', $arguments, 'string', $this, true
            )
        );

        return $result;
    }

    public function expects(\PHPUnit\Framework\MockObject\Matcher\Invocation $matcher)
    {
        return $this->__phpunit_getInvocationMocker()->expects($matcher);
    }

    public function method()
    {
        $any = new \PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
        $expects = $this->expects($any);
        return call_user_func_array(array($expects, 'method'), func_get_args());
    }

    public function __phpunit_setOriginalObject($originalObject)
    {
        $this->__phpunit_originalObject = $originalObject;
    }

    public function __phpunit_getInvocationMocker()
    {
        if ($this->__phpunit_invocationMocker === null) {
            $this->__phpunit_invocationMocker = new \PHPUnit\Framework\MockObject\InvocationMocker($this->__phpunit_configurable);
        }

        return $this->__phpunit_invocationMocker;
    }

    public function __phpunit_hasMatchers()
    {
        return $this->__phpunit_getInvocationMocker()->hasMatchers();
    }

    public function __phpunit_verify($unsetInvocationMocker = true)
    {
        $this->__phpunit_getInvocationMocker()->verify();

        if ($unsetInvocationMocker) {
            $this->__phpunit_invocationMocker = null;
        }
    }
}
