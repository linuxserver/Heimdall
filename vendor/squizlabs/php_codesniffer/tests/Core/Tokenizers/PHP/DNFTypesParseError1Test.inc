<?php

// Parentheses in broken DNF type declarations will remain tokenized as normal parentheses.
// This test is in a separate file as the 'nested_parenthesis' indexes will be off after this code.
class ParseErrors {
    /* testBrokenConstDNFTypeEndOnOpenParenthesis */
    const A|(B PARSE_ERROR = null;

    /* testBrokenPropertyDNFTypeEndOnOpenParenthesis */
    public A|(B $parseError;

    function unmatchedParens {
        /* testBrokenParamDNFTypeEndOnOpenParenthesis */
        A|(B $parseError,
    /* testBrokenReturnDNFTypeEndOnOpenParenthesis */
    ) : A|(B {}
}
