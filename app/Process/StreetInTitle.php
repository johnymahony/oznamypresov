<?php


namespace App\Process;


use App\Board;
use App\Geo\Geocoder;
use App\Log;
use App\StreetName;

class StreetInTitle
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @throws \Exception
     */
    public function process(Board $board): bool
    {
//        $isElectricity = collect(city()->electricityCategory())->filter(function ($id) use ($board) {
//                return $board->category_id == $id;
//            })->count() > 0;
//        if ( ! $isElectricity) {
//            return false;
//        }
        Log::debug("Is electricity");
        foreach (StreetName::all() as $street) {
            $streetName = trim($street->name);
            if ($streetName && preg_match("/$streetName/", $board->description)) {
                Log::debug("Street found " . $streetName);
                $point = $this->addStreetPoint($street, $board);
                if ($point) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    private function addStreetPoint(StreetName $street, Board $board): bool
    {
        return $this->geocoder->addressToPoint($board, $street->name . ', ' . city()->humanName());
    }
}