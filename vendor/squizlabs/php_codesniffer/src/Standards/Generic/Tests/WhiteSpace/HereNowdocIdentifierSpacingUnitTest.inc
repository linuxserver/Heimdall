<?php

$heredoc = <<<EOD
some text
EOD;

$nowdoc = <<<'EOD'
some text
EOD;

$heredoc = <<< END
some text
END;

$nowdoc = <<<             'END'
some text
END;

$heredoc = <<<			"END"
some text
END;

$nowdoc = <<<	'END'
some text
END;
