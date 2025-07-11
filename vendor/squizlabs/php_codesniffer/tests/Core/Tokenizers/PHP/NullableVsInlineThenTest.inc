<?php

class Nullable
{
    /* testNullableReadonlyOnly */
    readonly ?int $prop;

    /* testNullablePrivateSet */
    private(set) ?int $prop2;

    /* testNullablePublicProtectedSet */
    public protected(set) ?int $prop3;
}

class InlineThen
{
    /* testInlineThenInPropertyDefaultValue */
    public int $prop = self::SOMECONT ? PHP_CONST ? OTHER_CONST;
}
