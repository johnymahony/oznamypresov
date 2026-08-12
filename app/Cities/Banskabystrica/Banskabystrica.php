<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Banskabystrica;


use App\Adapter\Corageo;
use App\Cities\City;
use Illuminate\Console\Scheduling\Schedule;

class Banskabystrica extends City
{

    public function getBoard()
    {
        return (new Corageo())->getBoard('https://egov.banskabystrica.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
    }

    public function __toString()
    {
        return 'Banskabystrica';
    }

    public function contactEmail()
    {
        return 'info@oznamybanskabystrica.sk';
    }

    public function contactWeb()
    {
        return 'https://www.banskabystrica.sk';
    }

    public function humanName()
    {
        return 'Banská Bystrica';
    }

    public function domain()
    {
        return 'oznamybanskabystrica.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourlyAt(45)->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('01:17');
        $schedule->command('DeleteBoard')->dailyAt('03:00');
        $schedule->command('Ocr')->hourlyAt(55);
        $schedule->command('SyncStreets')->weeklyOn(1, '2:30');
        $schedule->command('DailySender')->dailyAt('16:00');
//        $schedule->command('SendEmails')->hourlyAt('10')->between('15:00', '22:00');
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
        return 48.7362132023438;
    }

    public function longitude()
    {
        return 19.14645132933942;
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
        return resolve(Corageo::class)->syncStreets('https://egov.banskabystrica.sk/Default.aspx?NavigationState=160:0::plac1280:_144055_5_8');
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
            'Doručenie / uloženie písomnostii',
            'Návrhy všeobecne záväzných nariadení',
            'Doručenie / uloženie písomnosti',
            'Dražby',
            'Návrhy rozpočtov a záverečných účtov',
            'Oznámenia o zrušení trvalého pobytu',
        ]);
    }

    public function titlesForWholeCity()
    {
        return [
        ];
    }

    public function textForWholeCity()
    {
        // TODO: Implement textForWholeCity() method.
    }

    public function electricityCategory()
    {
        return [
            3
        ];
    }

    public function googleAnalyticsTag()
    {
        return 'G-9V4QX0XXH1';
    }

    public function egovPage()
    {
        // TODO: Implement egovPage() method.
    }
}