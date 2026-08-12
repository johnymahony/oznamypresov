<?php

namespace App\Traits;

use App\Helpers\HttpRequest;

trait GetJsonFromCity
{

    private function getFromJson($url)
    {
//        $json = file_get_contents($url);
        $json = (new HttpRequest())->call($url);
        if ($json === false) {
            throw new \Exception("Unable to dowload data json");
        }
        $data = json_decode($json, true);
        if ($data === null) {
            throw new \Exception("Invalid JSON: ".substr($json, 0, 300));
        }

        return $data;
    }
}