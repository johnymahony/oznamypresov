<?php

namespace App\Helpers;

use App\Log;
use App\Parcel;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 3. 7. 2018
 * Time: 12:58
 */
class CadasterNumber
{
    private $text;
    public $parcels = [];

    /**
     * @param $text
     *
     * @return bool|string
     * @throws Exception
     */
    public function resolve($text)
    {

        $text = str_replace(["\n", "\t"], ' ', $text);
        $text = preg_replace("/\s+/", ' ', $text);
        $this->text = $text;


        $patterns = [
            '/kat.{1,13}zem.{1,2}(.{1,50})/is',
            '/kat.{1,13}z.{1,2}(.{1,50})/is',
            '/k\.\s*ú\.(.{1,50})/is',
            '/kú\.(.{1,50})/is',
            '/k\.\s*Ú\.(.{1,50})/is',
        ];

        foreach ($patterns as $pattern) {
            $number = $this->runPattern($pattern);
            if ($number) {
                return $number;
            }
        }

        return false;
    }

    private function runPattern($pattern)
    {
        $text = $this->text;
        if ( ! preg_match($pattern, $text, $m)) {
            return false;
        }
        preg_match_all($pattern, $text, $matches);
        if (empty($matches)) {
            return false;
        }
        $cadasterNumbers = cadasters();
        foreach ($matches as $line) {
            $line = $line[0];
            $this->writeMissingCadaster($line);
            foreach ($cadasterNumbers as $number => $city) {
                if (empty($number)) {
                    continue;
                }
                if (preg_match('/' . $city . '/', $line)) {
                    if ( ! $cadasterNumbers->contains($city)) {
                        return false;
                    }

                    $this->findParcelsNearOccurence($city);

                    return $cadasterNumbers->search($city);
                }
            }
        }

        return false;
    }

    private function writeMissingCadaster($city)
    {
        $file = 'cadasters.json';
        $city = str_replace(['.', ','], '|', $city);
        $parts = explode('|', $city);
        $city = trim($parts[0]);
        if (Storage::exists($file)) {
            $data = json_decode(Storage::get($file), 1);
        } else {
            $data = [];
        }

        $data[city()->__toString()][$city] = $city;
        Storage::put($file, json_encode($data));
    }

    private function findParcelsNearOccurence($city)
    {
        $parts = explode($city, $this->text);
        $textBefore = substr($parts[0], -200);
        $textAfter = substr($parts[1], 0, 200);
        $parcels = [];
//        var_dump($text);
        if (preg_match_all("/(\d+\/*\d*)/", $textBefore, $matches)) {
            $parcels = array_merge($parcels, $matches[0]);
        }
        if (preg_match_all("/(\d+\/*\d*)/", $textAfter, $matches)) {
            $parcels = array_merge($parcels, $matches[0]);
        }
        $parcels = collect($parcels);
        $parcels = $parcels->filter(function ($number) {
            return strlen($number) > 2;
        });

        $completeParcels = $parcels->filter(function ($number) {
            return preg_match("/\//", $number);
        });
        if ($completeParcels->count() > 0) {
            $this->parcels += $completeParcels->toArray();
        } else {
            $this->parcels += $parcels->toArray();
        }
    }
}