<?php

namespace App\Helpers;

class Gps
{
    private $latitude;
    private $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function lat()
    {
        return $this->latitude;
    }

    public function long()
    {
        return $this->longitude;
    }

    public function distanceFrom(Gps $point)
    {
        return distance(
            $this->lat(),
            $this->long(),
            $point->lat(),
            $point->long()
        );
    }

}