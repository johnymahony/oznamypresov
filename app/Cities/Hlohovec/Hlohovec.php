<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Hlohovec;


use App\Adapter\Corageo;
use App\Cities\City;
use App\Cities\Presov\SyncStreets;
use Illuminate\Console\Scheduling\Schedule;

class Hlohovec extends City
{

    public function getBoard()
    {
        return (new Corageo())->getBoard('http://egov.hlohovec.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
    }

    public function __toString()
    {
        return 'Hlohovec';
    }

    public function contactEmail()
    {
        return 'info@oznamyhlohovec.sk';
    }

    public function contactWeb()
    {
        return 'https://www.hlohovec.sk';
    }

    public function humanName()
    {
        return 'Hlohovec';
    }

    public function domain()
    {
        return 'oznamyhlohovec.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourlyAt(25)->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('02:44');
        $schedule->command('DeleteBoard')->dailyAt('04:00');
        $schedule->command('Ocr')->hourlyAt(45);
        $schedule->command('DailySender')->dailyAt('15:45');
        $schedule->command('SyncStreets')->weeklyOn(1, '1:30');
//        $schedule->command('SendEmails')->hourlyAt('15')->between('15:00', '22:00');
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
            '785' => 'Hlohovec',
            '1632' => 'Šulekovo',
        ];
    }

    public function latitude()
    {
        return 48.42529671678687;
    }

    public function longitude()
    {
        return 17.80308785480494;
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
        return resolve(Corageo::class)->syncStreets('http://egov.hlohovec.sk/Default.aspx?NavigationState=160:0::plac1280:_144055_5_8');
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
            'Doručenie / uloženie písomnosti',
            'Oznámenia o zrušení trvalého pobytu',
        ]);
    }

    public function titlesForWholeCity()
    {
        return [
            'Oznámenie o doručení písomností',
            'Oznam o konaní zasadnutia Msz v Hlohovci',
            'Verejná vyhláška ',
            'Uznesenia z online rokovania',
            'Zápisnica z online rokovania MsZ mesta Hlohovec',
            'Oznámenie o dražbe',
            'Program rokovania MsR mesta',
            'VZN mesta Hlohovec',
            'Program rokovania Mestského zastupiteľstva',
            'Program rokovania Mestskej rady',
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
        return 'G-7J9BJ91GBC';
    }

    public function egovPage()
    {
        // TODO: Implement egovPage() method.
    }
}