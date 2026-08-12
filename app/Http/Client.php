<?php

namespace App\Http;

use App\Log;
use AppLog;
use GuzzleHttp\Client as Guzzle;

/**
 * Thin, JSON-oriented wrapper around guzzlehttp/guzzle.
 */
class Client
{
    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(Guzzle $client)
    {
        $this->client = $client;
    }

    public function getJson(string $url)
    {
        Log::debug("HttpClient::get $url");
        $response = $this->client->get($url);
        $json = $response->getBody()->getContents();
        Log::debug("HttpClient::response " . $json);
        return json_decode($json);
    }

    public function get(string $url)
    {
        Log::debug("HttpClient::get $url");
        $response = $this->client->get($url);
        $json = $response->getBody()->getContents();
        Log::debug("HttpClient::response " . $json);
        return $json;
    }

    public function post(string $url, array $data = [])
    {
        Log::debug("HttpClient::post $url");
        $response = $this->client->post($url, [
            'json' => $data,
        ]);

        Log::debug("HttpClient::response " . $response->getBody()->getContents());
        return json_decode($response->getBody()->getContents());
    }

    public function upload(string $url, array $file)
    {
        Log::debug("HttpClient::upload $url");
        $response = $this->client->post($url, [
            'multipart' => [$file],
        ]);

        $responseText = $response->getBody()->getContents();
        Log::debug("HttpClient::response $responseText");
        $decoded = json_decode($responseText);
        if ($decoded === null) {
            return $responseText;
        }
        return $decoded;
    }

    public function configure(array $config): void
    {
        $this->client = app(Guzzle::class, ['config' => $config]);
    }
}
