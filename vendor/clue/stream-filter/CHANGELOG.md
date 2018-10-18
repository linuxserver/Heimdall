# Changelog

## 1.4.0 (2017-08-18)

*   Feature / Fix: The `fun()` function does not pass filter parameter `null`
    to underlying `stream_filter_append()` by default
    (#15 by @Nyholm)

    Certain filters (such as `convert.quoted-printable-encode`) do not accept
    a filter parameter at all. If no explicit filter parameter is given, we no
    longer pass a default `null` value.

    ```php
    $encode = Filter\fun('convert.quoted-printable-encode');
    assert('t=C3=A4st' === $encode('täst'));
    ```

*   Add examples and improve documentation
    (#13 and #20 by @clue and #18 by @Nyholm)

*   Improve test suite by adding PHPUnit to require-dev,
    fix HHVM build for now again and ignore future HHVM build errors,
    lock Travis distro so new future defaults will not break the build
    and test on PHP 7.1
    (#12, #14 and #19 by @clue and #16 by @Nyholm)

## 1.3.0 (2015-11-08)

*   Feature: Support accessing built-in filters as callbacks
    (#5 by @clue)

    ```php
    $fun = Filter\fun('zlib.deflate');

    $ret = $fun('hello') . $fun('world') . $fun();
    assert('helloworld' === gzinflate($ret));
    ```

## 1.2.0 (2015-10-23)

* Feature: Invoke close event when closing filter (flush buffer)
  (#9 by @clue)

## 1.1.0 (2015-10-22)

* Feature: Abort filter operation when catching an Exception
  (#10 by @clue)

* Feature: Additional safeguards to prevent filter state corruption
  (#7 by @clue)

## 1.0.0 (2015-10-18)

* First tagged release
