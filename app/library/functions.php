<?php

function curl_request_page(string $url)
{
    $sessid = 'PHPSESSID=' . $_COOKIE['PHPSESSID'];
    $options = [CURLOPT_COOKIE => $sessid, CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $url];
    $curld = curl_init();
    curl_setopt_array($curld, $options);
    $output = curl_exec($curld);
    curl_close($curld);
    return $output;
}
