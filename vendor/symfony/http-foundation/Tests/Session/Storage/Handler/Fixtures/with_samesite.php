<?php

require __DIR__.'/common.inc';

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage(array('cookie_samesite' => 'lax'));
$storage->setSaveHandler(new TestSessionHandler());
$storage->start();

$_SESSION = array('foo' => 'bar');

ob_start(function ($buffer) { return str_replace(session_id(), 'random_session_id', $buffer); });
