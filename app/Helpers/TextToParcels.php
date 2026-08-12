<?php

namespace App\Helpers;

use App\Log;
use App\Parcel;
use Exception;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 3. 7. 2018
 * Time: 12:58
 */
class TextToParcels
{
    /**
     * @param $text
     * @return array
     * @throws Exception
     */
    public function resolve($text)
    {
        if (empty($text)) {
            return [];
        }
        $r = new \App\Helpers\HttpRequest();
        $result = $r->call('http://clean.oznamypresov.sk/clean.php', ['text' => $text]);
        Log::debug("Clean result: " . $result);
        $decoded = json_decode($result, 1);
        if ($decoded === null) {
            throw new \Exception("Invalid JSON result in clean service: " . $result);
        }
        $list = [];
        $parcels = explode("\n", $decoded['result']);
        foreach ($parcels as $parcel) {
            $parcel = trim($parcel);
            if (empty($parcel) || !preg_match("/[0-9]/", $parcel)) {
                continue;
            }
            $list[$parcel] = Parcel::normalize($parcel);
        }
        return $list;
    }
}