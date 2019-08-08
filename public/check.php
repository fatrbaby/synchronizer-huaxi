<?php

/**
 * @author fatrbaby
 * @copyright 2016
 */

require __DIR__ . '/../vendor/autoload.php';

//$url = 'http://xiaker.org';

$url = 'http://10.0.0.43:8000/response.php';
//$handle = curl_init($url);
//curl_setopt($handle, CURLOPT_HEADER, false);
//curl_setopt($handle, CURLOPT_RETURNTRANSFER, ture);
//curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
//
//$response = curl_exec($handle);
//curl_close($handle);
//echo $response;

$client = new GuzzleHttp\Client();
$response = $client->request('GET', $url, [
    'query'=> [
        "name"=>"fatrbaby",
        "age"=>"27",
        "hobby"=>"girls",
    ]
]);

echo $response->getBody();

