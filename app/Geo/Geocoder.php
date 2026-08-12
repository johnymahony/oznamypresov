<?php

namespace App\Geo;

use App\Board;
use App\Log;

class Geocoder
{
    /**
     * @param $address
     *
     * @throws \Exception
     */
    public function addressToPoint(Board $board, $address): bool
    {
        $url = 'https://geocoder.api.here.com/6.2/geocode.json?app_id=uNTQL82Wtzxno6haLUd3&app_code=rA2FCB8vMQXeOZOC18hUpw&searchtext=' . urlencode($address);

        $json = file_get_contents($url);
        $parsed = json_decode($json, 1);
        if ($parsed === null) {
            throw new \Exception("Invalid json in geocoder: " . $json);
        }
        Log::debug("Geocode response ({$address}): " . json_encode($parsed));
        if (empty($parsed['Response']['View'])) {
            return false;
        }
        $point = $parsed['Response']['View'][0]['Result'][0]['Location']['NavigationPosition'][0];
        if (empty($point)) {
            return false;
        }
        $board->points()->firstOrCreate([
            'latitude' => $point['Latitude'],
            'longitude' => $point['Longitude'],
        ]);

        return true;
    }
}