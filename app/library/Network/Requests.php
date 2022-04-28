<?php

declare(strict_types=1);

namespace Application\Network;

/**
 * Connect to and fetch web resources
 */
class Requests
{
    protected $curld;
    protected $default_options;

    public function __construct()
    {
        $sessid = 'PHPSESSID=' . $_COOKIE['PHPSESSID'];
        $this->curld = curl_init();
        $this->default_options = [CURLOPT_COOKIE => $sessid, CURLOPT_RETURNTRANSFER => true];
    }

    public function get(string $url)
    {
        $options = $this->default_options + [CURLOPT_URL => $url];
        curl_setopt_array($this->curld, $options);
        $output = curl_exec($this->curld);
        return $output;
    }

    public function post(string $url, array $postData)
    {
        $options = [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_URL => $url];
        $options += $this->default_options;
        curl_setopt_array($this->curld, $options);
        $output = curl_exec($this->curld);
        return $output;
    }

    public function __destruct()
    {
        curl_close($this->curld);
    }
}
