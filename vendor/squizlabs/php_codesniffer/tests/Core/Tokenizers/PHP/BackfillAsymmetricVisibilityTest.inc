<?php

class PropertyDemo {
    /* testPublicSetProperty */ public(set) mixed $pub1;
    /* testPublicSetPropertyUC */ PUBLIC(SET) mixed $pub2;
    public /* testPublicPublicSetProperty */ public(set) mixed $pub3;
    public /* testPublicPublicSetPropertyUC */ PUBLIC(SET) mixed $pub4;

    /* testProtectedSetProperty */ protected(set) mixed $prot1;
    /* testProtectedSetPropertyUC */ PROTECTED(SET) mixed $prot2;
    public /* testPublicProtectedSetProperty */ protected(set) mixed $prot3;
    public /* testPublicProtectedSetPropertyUC */ PROTECTED(SET) mixed $prot4;

    /* testPrivateSetProperty */ private(set) mixed $priv1;
    /* testPrivateSetPropertyUC */ PRIVATE(SET) mixed $priv2;
    public /* testPublicPrivateSetProperty */ private(set) mixed $priv3;
    public /* testPublicPrivateSetPropertyUC */ PRIVATE(SET) mixed $priv4;

    /* testInvalidUnsetProperty */ public(unset) mixed $invalid1;
    /* testInvalidSpaceProperty */ public (set) mixed $invalid2;
    /* testInvalidCommentProperty */ protected/* foo */(set) mixed $invalid3;
    /* testInvalidGetProperty */ private(get) mixed $invalid4;
    /* testInvalidNoParenProperty */ private set mixed $invalid5;
}

class ConstructorPromotionDemo {
    public function __construct(
        /* testPublicSetCPP */ public(set) mixed $pub1,
        /* testPublicSetCPPUC */ PUBLIC(SET) mixed $pub2,
        public /* testPublicPublicSetCPP */ public(set) mixed $pub3,
        public /* testPublicPublicSetCPPUC */ PUBLIC(SET) mixed $pub4,

        /* testProtectedSetCPP */ protected(set) mixed $prot1,
        /* testProtectedSetCPPUC */ PROTECTED(SET) mixed $prot2,
        public /* testPublicProtectedSetCPP */ protected(set) mixed $prot3,
        public /* testPublicProtectedSetCPPUC */ PROTECTED(SET) mixed $prot4,

        /* testPrivateSetCPP */ private(set) mixed $priv1,
        /* testPrivateSetCPPUC */ PRIVATE(SET) mixed $priv2,
        public /* testPublicPrivateSetCPP */ private(set) mixed $priv3,
        public /* testPublicPrivateSetCPPUC */ PRIVATE(SET) mixed $priv4,

        /* testInvalidUnsetCPP */ public(unset) mixed $invalid1,
        /* testInvalidSpaceCPP */ public (set) mixed $invalid2,
        /* testInvalidCommentCPP */ protected/* foo */(set) mixed $invalid3,
        /* testInvalidGetCPP */ private(get) mixed $invalid4,
        /* testInvalidNoParenCPP */ private set mixed $invalid5,
    ) {}
}

class NonVisibilityCases {
    function /* testProtectedFunctionName */ protected() {}
    function /* testPublicFunctionName */ public(
        /* testSetParameterType */ Set $setter
    ) {}
}

// Intentional parse error. This must be the last test in the file.
class LiveCodingDemo {
    /* testLiveCoding */ private(set
