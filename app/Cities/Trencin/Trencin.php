<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Trencin;


use App\Adapter\Corageo;
use App\Cities\City;
use Illuminate\Console\Scheduling\Schedule;

class Trencin extends City
{

    public function getBoard()
    {
        return (new Corageo())->getBoard('https://egov.trencin.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
    }

    public function __toString()
    {
        return 'Trencin';
    }

    public function contactEmail()
    {
        return 'info@oznamytrencin.sk';
    }

    public function contactWeb()
    {
        return 'https://www.trencin.sk';
    }

    public function humanName()
    {
        return 'Trenčín';
    }

    public function domain()
    {
        return 'oznamytrencin.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourly()->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('01:17');
        $schedule->command('DeleteBoard')->dailyAt('03:00');
        $schedule->command('Ocr')->hourlyAt(55);
        $schedule->command('DailySender')->dailyAt('15:30');
//        $schedule->command('SendEmails')->hourlyAt('25')->between('15:00', '22:00');
    }

    /**
     * Zoznam kategorii ktorych oznamy sa musia zvesit po urcitom case
     * @return mixed
     */
    public function categoriesToDelete()
    {
        return [];
    }

    public function cadasterNumbers()
    {
        return [
            '' => '[neznáme]',
            '2379' => 'Batizovce',
            '152' => 'Kubrá',
            '312' => 'Kubrica',
            '1631' => 'Trenčín, Trenčianske Biskupice',
            '2128' => 'Záblatie',
            '2642' => 'Zlatovce',
        ];
    }

    public function latitude()
    {
        return 48.890425;
    }

    public function longitude()
    {
        return 18.030240;
    }

    public function getStreetNames()
    {
        return collect([]);
    }

    public function hasGeoAddresses()
    {
        return false;
    }

    /**
     * @throws \Exception
     */
    public function syncStreets()
    {
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
            'Oznámenie o uložení zásielky',
            'VZN mesta Trenčín',
            'Oznámenie o zrušení trvalého pobytu',
            'Oznámenie o zrušení  trvalého pobytu',
            'Oznámenia samosprávy',
            'Dražby',
        ]);
    }

    public function titlesForWholeCity()
    {
        return [
            'Oznámenie o dobrovoľnej dražbe'
        ];
    }

    public function textForWholeCity()
    {
        // TODO: Implement textForWholeCity() method.
    }

    public function electricityCategory()
    {
        // TODO: Implement electricityCategory() method.
    }

    public function googleAnalyticsTag()
    {
        return 'G-J753Z3JHBF';
    }

    public function egovPage()
    {
        // TODO: Implement egovPage() method.
    }
}