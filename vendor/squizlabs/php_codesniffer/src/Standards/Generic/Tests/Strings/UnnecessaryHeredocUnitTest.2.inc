<?php

/*
 * Test file 1 and 2 mirror each other, with file 1 containing non-indented cross-version compatible heredoc/nowdoc syntax,
 * while the code samples in file 2 use PHP 7.3+ flexible heredoc/nowdoc syntax.
 *
 * These two files should be kept in sync!
 */

$nowdoc = <<<'EOD'
    some text
    EOD;

$heredoc = <<<END
    some $foo text
    END;

$heredoc = <<<"END"
    some {$foo[0]} text
    END;

$heredoc = <<<END
    {$foo?->bar}
    END;

$heredoc = <<<    "END"
    some ${beers::softdrink}
    END;

$heredoc = <<< END
    {${$object->getName()}} text
    END;

$heredoc = <<<"END"
    some {${getName()}}
    END;

$heredoc = <<<END
    ${substr('laruence', 0, 2)}
    END;

$heredoc = <<<"END"
    some {$foo['bar']->baz()()}
    END;

$heredoc = <<<END
    {$obj->values[3]->name} text
    END;

$heredoc = <<<"END"
    some ${$bar}
    END;

$heredoc = <<<END
    ${foo->bar} text
    END;

$heredoc = <<<"END"
    ${foo["${bar}"]} text
    END;

$heredoc = <<<END
    some ${foo["${bar[\'baz\']}"]}
    END;

$heredoc = <<<"END"
    ${foo->{${'a'}}} text
    END;

$heredoc = <<<END
    some {$foo->{$baz[1]}}
    END;

$heredoc = <<<END
    some text
    {${beers::$ale}}
    some text
    END;

$heredoc = <<<"END"
    $people->john's wife greeted $people->robert.
    END;

$heredoc = <<<END
    Let's make sure it also works with this: {$arr[foo][3]}
    END;

$heredoc = <<<END
    Testing ${foo["${bar
      ['baz']
    }"]} and more testing
    END;

$heredoc = <<<"END"
    Testing {${foo["${bar
      ['baz']
    }"]}} and more testing
    END;

$heredoc = <<<END
    some text
    END;

$heredoc = <<< "END"
    some text
    some \$text
    some text \\ including a backslash
    END;

$heredoc = <<<EOD
    <?php
    echo 'The below line contains escape characters and should be recognized as needing heredoc';
    echo "aa\xC3\xC3	\xC3\xB8aa";
    EOD;

echo <<<EOT
    This should print a capital 'A': \x41
    EOT;

echo <<<EOT
    Here we should have a tab and 2 'A's: \t \101 \u{41}
    EOT;
