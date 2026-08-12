<?php

namespace App\Helpers\Faker;

abstract class Geo extends Faker {
  const LAT = 0;
  const LNG = 1;

  const LAT_MIN = -90;
  const LAT_MAX = 90;
  const LNG_MIN = -180;
  const LNG_MAX = 180;

  const PRECISION = 6;

  public static function bounds() {
    return array(
      array(static::LAT_MIN, static::LNG_MIN),
      array(static::LAT_MAX, static::LNG_MAX),
    );
  }

  public static function point(?array $bounds = null) {
    if ($bounds === null) {
      $bounds = static::bounds();
    }

    return array(
      self::LAT => self::randFloat(self::latRange($bounds)),
      self::LNG => self::randFloat(self::lngRange($bounds)),
    );
  }

  public static function pointDMS(?array $bounds = null) {
    list($lat, $lng) = static::point($bounds);

    return sprintf('%s %s', self::floatToDMS($lat), self::floatToDMS($lng));
  }

  public static function latitude(?array $bounds = null) {
    return self::randFloat(self::latRange($bounds));
  }

  public static function latitudeDMS(?array $bounds = null) {
    return self::floatToDMS(static::latitude($bounds));
  }

  public static function longitude(?array $bounds = null) {
    return self::randFloat(self::lngRange($bounds));
  }

  public static function longitudeDMS(?array $bounds = null) {
    return self::floatToDMS(static::longitude($bounds));
  }

  private static function latRange(?array $bounds = null) {
    if ($bounds === null) {
      $bounds = static::bounds();
    }

    if (is_array($bounds[0])) {
      return array($bounds[0][self::LAT], $bounds[1][self::LAT]);
    }
    else {
      return $bounds;
    }
  }

  private static function lngRange(?array $bounds = null) {
    if ($bounds === null) {
      $bounds = static::bounds();
    }

    if (is_array($bounds[0])) {
      return array($bounds[0][self::LNG], $bounds[1][self::LNG]);
    }
    else {
      return $bounds;
    }
  }

  private static function randFloat(array $range) {
    list($min, $max) = $range;

    return round($min + (lcg_value() * abs($max - $min)), static::PRECISION);
  }

  private static function floatToDMS($float) {
    $deg = floor($float);
    $minSec = abs($float - $deg) * 60;
    $min = floor($minSec);
    $sec = floor(abs($minSec - $min) * 60);

    return sprintf('%d°%d\'%d"', $deg, $min, $sec);
  }
}
