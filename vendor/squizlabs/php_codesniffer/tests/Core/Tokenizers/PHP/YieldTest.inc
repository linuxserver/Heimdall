<?php

function generator()
{
    /* testYield */
    yield 1;

    /* testYieldFollowedByComment */
    YIELD/*comment*/ 2;

    /* testYieldFrom */
    yield from gen2();

    /* testYieldFromWithExtraSpacesBetween */
    Yield           From gen2();

    /* testYieldFromWithTabBetween */
    yield	from gen2();

    /* testYieldFromSplitByNewLines */
    yield

    FROM
    gen2();

    /* testYieldFromSplitByComment */
    yield /* comment */ from gen2();

    /* testYieldFromWithTrailingComment */
    yield // comment
    from gen2();

    /* testYieldFromWithTrailingAnnotation */
    yield // phpcs:ignore Stnd.Cat.Sniff -- for reasons.
    from gen2();

    /* testYieldFromSplitByNewLineAndComments */
    yield
    /* comment line 1
       line 2 */
    // another comment
    from
    gen2();

    /* testYieldFromSplitByNewLineAndAnnotation */
    YIELD
    // @phpcs:disable Stnd.Cat.Sniff -- for reasons.
    From
    gen2();
}

/* testYieldUsedAsClassName */
class Yield {
    /* testYieldUsedAsClassConstantName */
    const Type YIELD = 'foo';

    /* testYieldUsedAsMethodName */
    public function yield() {
        /* testYieldUsedAsPropertyName1 */
        echo $obj->yield;

        /* testYieldUsedAsPropertyName2 */
        echo $obj?->yield();

        /* testYieldUsedForClassConstantAccess1 */
        echo MyClass::YIELD;
        /* testFromUsedForClassConstantAccess1 */
        echo MyClass::FROM;
    }

    /* testYieldUsedAsMethodNameReturnByRef */
    public function &yield() {}
}

function myGen() {
    /* testYieldLiveCoding */
    yield
