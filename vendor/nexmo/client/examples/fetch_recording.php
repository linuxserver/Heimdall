<?php

require_once "vendor/autoload.php";

$recordingId = 'RECORDING_ID';

$keypair = new \Nexmo\Client\Credentials\Keypair(file_get_contents(__DIR__ . '/private.key'), 'APPLICATION_ID');
$client = new \Nexmo\Client($keypair);
$recording = 'https://api.nexmo.com/v1/files/'.$recordingId;
$data = $client->get($recording);

file_put_contents($recordingId.'.mp3', $data->getBody());
