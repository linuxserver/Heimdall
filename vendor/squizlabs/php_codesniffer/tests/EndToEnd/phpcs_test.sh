#!/usr/bin/env bash

function test_phpcs_is_working() {
  assert_successful_code "$(bin/phpcs --no-cache --standard=tests/EndToEnd/Fixtures/endtoend.xml.dist tests/EndToEnd/Fixtures/ClassOneWithoutStyleError.inc tests/EndToEnd/Fixtures/ClassTwoWithoutStyleError.inc)"
}

function test_phpcs_is_working_in_parallel() {
  assert_successful_code "$(bin/phpcs --no-cache --parallel=2 --standard=tests/EndToEnd/Fixtures/endtoend.xml.dist tests/EndToEnd/Fixtures/ClassOneWithoutStyleError.inc tests/EndToEnd/Fixtures/ClassTwoWithoutStyleError.inc)"
}

function test_phpcs_returns_error_on_issues() {
  OUTPUT="$(bin/phpcs --no-colors --no-cache --standard=tests/EndToEnd/Fixtures/endtoend.xml.dist tests/EndToEnd/Fixtures/ClassWithStyleError.inc)"
  assert_exit_code 2

  assert_contains "E 1 / 1 (100%)" "$OUTPUT"
  assert_contains "FOUND 1 ERROR AFFECTING 1 LINE" "$OUTPUT"
}

function test_phpcs_bug_1112() {
  # See https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/1112
    if [[ "$(uname)" == "Darwin" ]]; then
      # Perform some magic with `& fg` to prevent the processes from turning into a background job.
      assert_successful_code "$(bash -ic 'bash --init-file <(echo "echo \"Subprocess\"") -c "bin/phpcs --no-cache --parallel=2 --standard=tests/EndToEnd/Fixtures/endtoend.xml.dist tests/EndToEnd/Fixtures/ClassOneWithoutStyleError.inc tests/EndToEnd/Fixtures/ClassTwoWithoutStyleError.inc" & fg')"
    else
      # This is not needed on Linux / GitHub Actions
      assert_successful_code "$(bash -ic 'bash --init-file <(echo "echo \"Subprocess\"") -c "bin/phpcs --no-cache --parallel=2 --standard=tests/EndToEnd/Fixtures/endtoend.xml.dist tests/EndToEnd/Fixtures/ClassOneWithoutStyleError.inc tests/EndToEnd/Fixtures/ClassTwoWithoutStyleError.inc"')"
    fi
}
