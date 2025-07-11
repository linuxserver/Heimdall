<?php

/* testHeredocSingleLine */
echo <<<EOD
Some $var text
EOD;

/* testNowdocSingleLine */
echo <<<'MARKER'
Some text
MARKER;

/* testHeredocMultiLine */
echo <<<"😬"
Lorum ipsum
Some $var text
dolor sit amet
😬;

/* testNowdocMultiLine */
echo <<<'multi_line'
Lorum ipsum
Some text
dolor sit amet
multi_line;

/* testHeredocEndsOnBlankLine */
echo <<<EOD
Lorum ipsum
dolor sit amet

EOD;

/* testNowdocEndsOnBlankLine */
echo <<<'EOD'
Lorum ipsum
dolor sit amet

EOD;
