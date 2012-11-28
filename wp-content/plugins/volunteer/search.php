<?php

$address = urlencode($_GET['address']);
$url = "http://maps.googleapis.com/maps/api/geocode/json?address={$address}&sensor=false";
$curl = curl_init($url);
//  $cookieJar = 'cookies.txt';
//  curl_setopt($this->curl, CURLOPT_COOKIEJAR, $cookieJar);
//  curl_setopt($this->curl, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($curl, CURLOPT_AUTOREFERER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 25);
//  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
$result = curl_exec($curl);
echo $result;
exit;