<?php

namespace App\Helpers;

use App\Log;

class HttpRequest
{
    use \Illuminate\Queue\SerializesModels;

    public $mock;
    public $ch;
    public $httpCode;
    public $responseHeaders;
    public $logs;

    public function __construct($logs = true)
    {
        $this->init();
        $this->logs = $logs;
        //this is done because of serialization when inserting to queue
        $this->mock = @\App\Helpers\Session::$data['http_request_mock'];
    }

    /**
     * This method solves problem with serialization of curl resource (when whole object is serialized into the queue)
     */
    private function init()
    {
        if (empty($this->ch)) {
            $this->ch = curl_init();
        }
    }

    public function setHttpAuth($username, $password)
    {
        $this->init();
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->ch, CURLOPT_USERPWD, $username . ":" . $password);
    }

    public function setOption($key, $value)
    {
        $this->init();
        curl_setopt($this->ch, $key, $value);
    }

    public function setTimeout($timeout, $connectTimeout = null)
    {
        $this->init();
        if ($connectTimeout) {
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        }
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
    }

    public function call($url, $data = [])
    {
        $this->init();
        if ($this->logs) {
            \App\Log::debug("HttpRequest url: $url, data: " . json_encode($data));
        }
        if (empty($this->ch)) {
            $this->ch = curl_init();
        }
        $ch = $this->ch;
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOPROGRESS, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if ( ! empty($this->mock)) {
            $response = array_shift($this->mock);
            $this->httpCode = 200;
            Log::debug("Http response mock response: " . $response);

            return $response;
        }


        $response = curl_exec($ch);

// Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $result = substr($response, $header_size);
        $this->responseHeaders = $header;
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($this->logs) {
            \App\Log::debug("Total curl time: " . curl_getinfo($ch, CURLINFO_TOTAL_TIME));
        }
        if (curl_error($ch)) {
            throw new \Exception("Curl error in HTTP request [" . curl_error($ch) . '], error: ' . curl_error($ch) . "Url: $url, data: " . print_r($data,
                    1));
        }
        curl_close($ch);

        return $result;
    }
}