<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:27
 */

namespace App\Cities;


use Illuminate\Console\Scheduling\Schedule;

abstract class City
{

    public abstract function getBoard();

    public abstract function getStreetNames();

    public abstract function contactEmail();

    public abstract function contactWeb();

    public abstract function humanName();

    /**
     * Zoznam kategorii ktorych oznamy sa musia zvesit po urcitom case
     * @return mixed
     */
    public abstract function categoriesToDelete();

    public abstract function domain();

    public abstract function podnetyCategoryId();

    public abstract function cadasterNumbers();

    public abstract function latitude();

    public abstract function longitude();

    public abstract function googleAnalyticsTag();

    public abstract function egovPage();

    public abstract function scheduleCrons(Schedule $schedule);

    public abstract function __toString();

    public function toJavascript()
    {
        return [
            'has_geo_addresses' => $this->hasGeoAddresses(),
            'latitude' => $this->latitude(),
            'longitude' => $this->longitude(),
            'mapZoom' => 14,
        ];
    }

    public abstract function hasGeoAddresses();

    public abstract function syncStreets();

    public abstract function isCategoryForWholeCity($category);


    /**
     *
     * SELECT distinct(description), count(*), SUBSTRING(description, 1, 30) FROM `boards` as b join categories as c on c.id = b.category_id WHERE whole_city = 1 and c.name not in ('') group by SUBSTRING(description, 1, 30) order by count(*) desc
     *
     * @param $title
     *
     * @return mixed
     */
    public abstract function titlesForWholeCity();

    public abstract function textForWholeCity();

    public abstract function electricityCategory();

    public function isTitleForWholeCity($title)
    {
        $patterns = $this->titlesForWholeCity();
        if (empty($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern) {
            if (preg_match("/$pattern/", $title)) {
                return true;
            }
        }

        return false;
    }

    public function isTextForWholeCity($text)
    {
        $patterns = $this->textForWholeCity();
        if (empty($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern) {
            if (preg_match("/$pattern/", $text)) {
                return true;
            }
        }

        return false;
    }
}