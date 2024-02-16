@echo off
REM PHP_CodeSniffer detects violations of a defined coding standard.
REM 
REM @author    Greg Sherwood <gsherwood@squiz.net>
REM @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
REM @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence

set PHPBIN=php

"%PHPBIN%" "%~dp0\phpcs" %*
