<?php
global $serial;
require 'php-serial/php_serial.class.php';
$serial = new \phpSerial\phpSerial;
$serial->phpSerial();
$serial->deviceSet('/dev/ttyACM0');
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->deviceOpen();

require 'twitter-api-php/TwitterAPIExchange.php';
$oldTweet = '';

//wait for board to boot
sleep(5);

handleIt($oldTweet);

function handleIt($oldTweet) {
  $newTweet = getLatestTweet();
  print_r($newTweet . PHP_EOL);
  if ($newTweet != $oldTweet) {
    sendLatestTweet($newTweet);
  }
  $oldTweet = $newTweet;
  sleep(5);
  handleIt($oldTweet);
}

function sendLatestTweet($text) {
  global $serial;
  $message = explode("\n", wordwrap($text, 16));
  $len = count($message);

  for ($i=0; $i < $len; $i+=2) {
    $output = trim($message[$i]);
    if ($i+1 != $len) {
      $output .= ' ' . trim($message[$i+1]);
    }
    $serial->sendMessage($output);
    //some reading time
    sleep(4);
  }
}

function getLatestTweet()
{
  $url = 'https://api.twitter.com/1.1/search/tweets.json';
  $getfield = '?q=%23<HASHTAG>&count=1';
  $requestMethod = 'GET';
  $settings = array(
    'oauth_access_token' => 'YOUR_OAUTH_ACCESS_TOKEN',
    'oauth_access_token_secret' => 'YOUR_OAUTH_ACCESS_TOKEN_SECRET',
    'consumer_key' => 'YOUR_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET'
  );
  $twitter = new TwitterAPIExchange($settings);
  $twitter_json = $twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest();
  $twitter_json = json_decode($twitter_json);
  return $twitter_json->statuses[0]->text;
}
