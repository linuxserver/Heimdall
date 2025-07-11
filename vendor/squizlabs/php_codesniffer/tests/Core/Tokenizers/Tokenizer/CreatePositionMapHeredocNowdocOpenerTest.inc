<?php

/* testHeredocOpenerNoSpace */
$heredoc = <<<EOD
some text
EOD;

/* testNowdocOpenerNoSpace */
$nowdoc = <<<'EOD'
some text
EOD;

/* testHeredocOpenerHasSpace */
$heredoc = <<< END
some text
END;

/* testNowdocOpenerHasSpace */
$nowdoc = <<<             'END'
some text
END;

/* testHeredocOpenerHasTab */
$heredoc = <<<			"END"
some text
END;

/* testNowdocOpenerHasTab */
$nowdoc = <<<	'END'
some text
END;
