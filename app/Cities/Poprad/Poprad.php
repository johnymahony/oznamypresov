<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Poprad;


use App\Adapter\Corageo;
use App\Cities\City;
use App\Adapter\PopradBoardAdapter;
use Illuminate\Console\Scheduling\Schedule;

class Poprad extends City
{

    public function getBoard()
    {
        return (new PopradBoardAdapter())->process();
    }

    public function __toString()
    {
        return 'Poprad';
    }

    public function contactEmail()
    {
        return 'info@oznamypoprad.sk';
    }

    public function contactWeb()
    {
        return 'https://www.poprad.sk';
    }

    public function humanName()
    {
        return 'Poprad';
    }

    public function domain()
    {
        return 'oznamypoprad.sk';
    }

    public function podnetyCategoryId()
    {
        // TODO: Implement podnetyCategoryId() method.
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourly()->between('6:00', '22:00');;
        $schedule->command('ParcelFinder')->dailyAt('02:17');
        $schedule->command('DeleteBoard')->dailyAt('03:00');
        $schedule->command('Ocr')->hourlyAt(40);
        $schedule->command('DailySender')->dailyAt('14:00');
        $schedule->command('SyncStreets')->weeklyOn(1, '3:00');
//        $schedule->command('SendEmails')->hourlyAt('20')->between('15:00', '22:00');
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
            '713' => 'Gánovce',
            '2614' => 'Filice',
            '2887' => 'Gerlachov',
            '2958' => 'Hôrka',
            '1046' => 'Hozelec',
//            '' => 'Hranovica',
//            '' => 'Jánovce',
//            '' => 'Machalovce',
//            '' => 'Kravany',
//            '' => 'Liptovská Teplička',
//            '' => 'Lúčivná',
            '3482' => 'Matejovce',
//            '' => 'Mengusovce',
//            '' => 'Mlynica',
//            '' => 'Nová Lesná',
//            '' => 'Poprad',
//            '' => 'Matejovce',
//            '' => 'Spišská Sobota',
//            '' => 'Stráže pod Tatrami',
//            '' => 'Veľká',
//            '' => 'Spišská Teplica',
            '3515' => 'Poprad',
            '1404' => 'Spišská sobota',
//            '' => 'Spišské Bystré',
//            '' => 'Spišský Štiavnik',
//            '' => 'Svit',
//            '' => 'Štôla',
//            '' => 'Štrba',
//            '' => 'Šuňava',
//            '' => 'Nižná Šuňava',
//            '' => 'Vyšná Šuňava',
//            '' => 'Švábovce',
//            '' => 'Tatranská Javorina',
            '3538' => 'Veľká',
//            '' => 'Veľký Slavkov',
//            '' => 'Vernár',
//            '' => 'Vikartovce',
//            '' => 'Vydrník',
//            '' => 'Vysoké Tatry',
//            '' => 'Starý Smokovec',
//            '' => 'Štrbské pleso',
//            '' => 'Tatranská Lomnica',
//            '' => 'Ždiar',
        ];
    }

    public function latitude()
    {
        return 49.055889;
    }

    public function longitude()
    {
        return 20.291433;
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
        return resolve(Corageo::class)->syncStreets('https://egov.poprad.sk/Default.aspx?NavigationState=160:0::plac1280:_144055_5_8');
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
            'Oznámenie o mieste uloženia písomnosti',
            'Všebecne záväzné nariadenia',
            'Obchodné verejné súťaže',
            'Dražby',
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
        return 'UA-12442622-12';
    }

    public function egovPage()
    {
        // TODO: Implement egovPage() method.
    }
}