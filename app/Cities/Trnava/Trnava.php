<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Trnava;


use App\Adapter\Corageo;
use App\Cities\City;
use Illuminate\Console\Scheduling\Schedule;

class Trnava extends City
{

    public function getBoard()
    {
        return (new Corageo())->getBoard('https://egov.trnava.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
    }

    public function __toString()
    {
        return 'Trnava';
    }

    public function contactEmail()
    {
        return 'info@oznamytrnava.sk';
    }

    public function contactWeb()
    {
        return 'https://www.trnava.sk';
    }

    public function humanName()
    {
        return 'Trnava';
    }

    public function domain()
    {
        return 'oznamytrnava.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourlyAt(15)->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('03:33');
        $schedule->command('DeleteBoard')->dailyAt('04:00');
//        $schedule->command('Ocr')->hourlyAt(25);
        $schedule->command('DailySender')->dailyAt('15:00');
        $schedule->command('SyncStreets')->weeklyOn(1, '4:30');
//        $schedule->command('SendEmails')->hourlyAt('30')->between('15:00', '22:00');
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
        return 48.37787286134459;
    }

    public function longitude()
    {
        return 17.58636353467492;
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
        return resolve(Corageo::class)->syncStreets('https://egov.trnava.sk/Default.aspx?NavigationState=160:0::plac1280:_144055_5_8');
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
//            'Oznámenia samosprávy',
        ]);
    }

    public function titlesForWholeCity()
    {
        // TODO: Implement isTitleForWholeCity() method.
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
        // TODO: Implement googleAnalyticsTag() method.
    }

    public function egovPage()
    {
        // TODO: Implement egovPage() method.
    }
}