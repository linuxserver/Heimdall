<?php

// Parentheses in broken DNF type declarations will remain tokenized as normal parentheses.
// This test is in a separate file as the 'nested_parenthesis' indexes will be off after this code.
//
// Also note that the order of these tests is deliberate to try and trick the parentheses handling
// in the Tokenizer class into matching parentheses pairs, even though the parentheses do
// no belong together.

class UnmatchedParentheses {
    /* testBrokenConstDNFTypeParensMissingClose */
    const A|(B&C PARSE_ERROR_1 = null;

    /* testBrokenConstDNFTypeParensMissingOpen */
    const A|B&C) PARSE_ERROR_2 = null;

    /* testBrokenPropertyDNFTypeParensMissingClose */
    private A|(B&C $parseError1;

    /* testBrokenPropertyDNFTypeParensMissingOpen */
    protected A|B&C) $parseError2;

    function unmatchedParens1 (
        /* testBrokenParamDNFTypeParensMissingClose */
        A|(B&C $parseError,
    /* testBrokenReturnDNFTypeParensMissingOpen */
    ) : A|B&C) {}

    function unmatchedParens2 (
        /* testBrokenParamDNFTypeParensMissingOpen */
        A|B&C) $parseError
    /* testBrokenReturnDNFTypeParensMissingClose */
    ) : A|(B&C {}
}

class MatchedAndUnmatchedParentheses {
    /* testBrokenConstDNFTypeParensMissingOneClose */
    const (A&B)|(B&C PARSE_ERROR = null;

    /* testBrokenPropertyDNFTypeParensMissingOneOpen */
    protected (A&B)|B&C) $parseError;

    function unmatchedParens (
        /* testBrokenParamDNFTypeParensMissingOneClose */
        (A&B)|(B&C $parseError,
    /* testBrokenReturnDNFTypeParensMissingOneOpen */
    ) : (A&B)|B&C) {}
}
