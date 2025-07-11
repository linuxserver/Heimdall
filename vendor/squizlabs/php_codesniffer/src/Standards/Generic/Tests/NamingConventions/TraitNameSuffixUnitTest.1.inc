<?php

trait MissingTraitSuffix {} // Error.

trait GoodTrait {}

trait SuffixCaseIsNotEnforced_tRaIt {}

trait
    /*comment*/
    AnotherInvalidTraitName {} // Error.
