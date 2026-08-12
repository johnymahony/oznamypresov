<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Zilina;


use App\Adapter\Corageo;
use App\Cities\City;
use Illuminate\Console\Scheduling\Schedule;

class Zilina extends City
{

    public function getBoard()
    {
        return (new Corageo())->getBoard('https://egov.zilina.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
    }

    public function __toString()
    {
        return 'Zilina';
    }

    public function contactEmail()
    {
        return 'info@oznamyzilina.sk';
    }

    public function contactWeb()
    {
        return 'https://www.zilina.sk';
    }

    public function humanName()
    {
        return 'Žilina';
    }

    public function domain()
    {
        return 'oznamyzilina.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourlyAt(25)->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('01:33');
        $schedule->command('DeleteBoard')->dailyAt('04:00');
        $schedule->command('Ocr')->hourlyAt(25);
        $schedule->command('DailySender')->dailyAt('16:00');
        $schedule->command('SyncStreets')->weeklyOn(1, '3:30');
//        $schedule->command('SendEmails')->hourlyAt('35')->between('15:00', '22:00');
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
//            '2379' => 'Batizovce',
        ];
    }

    public function latitude()
    {
        return 49.22343651489022;
    }

    public function longitude()
    {
        return 18.739726683762342;
    }

    public function getStreetNames()
    {
        return $this->syncStreets()->pluck('name');
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
        return resolve(Corageo::class)->syncStreets('https://egov.zilina.sk/Default.aspx?NavigationState=160:0::plac1280:_144055_5_8');
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
//            'Oznámenia samosprávy',
        ]);
    }

    public function titlesForWholeCity()
    {
        return [
            'Oznámenie o uložení písomností',
        ];
    }

    public function textForWholeCity()
    {
        return [
            'o oznámení miesta uloženia písomnosti',
        ];
    }

    public function electricityCategory()
    {
        return [
            2,
            20
        ];
    }

    public function googleAnalyticsTag()
    {
        return 'G-921XJXQ0S1';
    }

    public function egovPage()
    {

    }
}