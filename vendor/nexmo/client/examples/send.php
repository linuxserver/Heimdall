<?php
//example of sending an sms using an API key / secret
require_once '../vendor/autoload.php';

//create client with api key and secret
$client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET));

//send message using simple api params
$message = $client->message()->send([
    'to' => NEXMO_TO,
    'from' => NEXMO_FROM,
    'text' => 'Test message from the Nexmo PHP Client'
]);

//array access provides response data
echo "Sent message to " . $message['to'] . ". Balance is now " . $message['remaining-balance'] . PHP_EOL;

sleep(1);

//send message using object support
$text = new \Nexmo\Message\Text(NEXMO_TO, NEXMO_FROM, 'Test message using PHP client library');
$text->setClientRef('test-message')
     ->setClass(\Nexmo\Message\Text::CLASS_FLASH);

$client->message()->send($text);

//method access
echo "Sent message to " . $text->getTo() . ". Balance is now " . $text->getRemainingBalance() . PHP_EOL;

sleep(1);

//sending a message over 160 characters
$longwinded = <<<EOF
But soft! What light through yonder window breaks?
It is the east, and Juliet is the sun.
Arise, fair sun, and kill the envious moon,
Who is already sick and pale with grief,
That thou, her maid, art far more fair than she.
EOF;

$text = new \Nexmo\Message\Text(NEXMO_TO, NEXMO_FROM, $longwinded);
$client->message()->send($text);

echo "Sent message to " . $text->getTo() . ". Balance is now " . $text->getRemainingBalance() . PHP_EOL;
echo "Message was split into " . count($text) . " messages, those message ids are: " . PHP_EOL;
for($i = 0; $i < count($text); $i++){
    echo $text[$i]['message-id'] . PHP_EOL;
}

echo "The account balance after each message was: " . PHP_EOL;
for($i = 0; $i < count($text); $i++){
    echo $text->getRemainingBalance($i) . PHP_EOL;
}

//easier iteration, can use methods or array access
foreach($text as $index => $data){
    echo "Balance was " . $text->getRemainingBalance($index) . " after message " . $data['message-id'] . " was sent." . PHP_EOL;
}

//an invalid request
try{
    $text = new \Nexmo\Message\Text('not valid', NEXMO_FROM, $longwinded);
    $client->message()->send($text);
} catch (Nexmo\Client\Exception\Request $e) {
    //can still get the API response
    $text     = $e->getEntity();
    $request  = $text->getRequest(); //PSR-7 Request Object
    $response = $text->getResponse(); //PSR-7 Response Object
    $data     = $text->getResponseData(); //parsed response object
    $code     = $e->getCode(); //nexmo error code
    error_log($e->getMessage()); //nexmo error message
}
