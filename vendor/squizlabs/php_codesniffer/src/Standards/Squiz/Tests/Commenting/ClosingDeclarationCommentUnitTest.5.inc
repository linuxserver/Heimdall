//end closingBraceAtEndOfFileMissingComment()

<?php

// This should be the only test in this file.
// Testing that the sniff is triggered and the fixer works when the closing bracket is
// the last character in the file (no newline after it) and the content of the first token
// is a "//end closingBraceAtEndOfFileMissingComment()" comment.

function closingBraceAtEndOfFileMissingComment() {
}