<?php

// Intentional parse error. Making sure that the sniff does not act on unfinished heredocs during live coding.
$heredoc = <<<EOD
Some text
Some txt
