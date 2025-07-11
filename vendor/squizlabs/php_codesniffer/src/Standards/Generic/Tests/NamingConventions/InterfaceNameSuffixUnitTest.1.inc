<?php

interface SomeNameInterface {}

interface MissingInterfaceSuffix {} // Error.

interface CaseOfSuffixIsNotEnforced_interFACE {}

interface
    /*comment*/
    InterfaceAnotherInvalidName
    extends
    AnotherInterface, \Countable {} // Error.
