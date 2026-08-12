<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 24. 9. 2020
 * Time: 8:26
 */

namespace App\Cities\Presov;


use App\Adapter\Corageo;
use App\Cities\City;
use App\Helpers\Rss;
use Illuminate\Console\Scheduling\Schedule;

class Presov extends City
{

    public function getBoard()
    {
        $list = (new Corageo())->getBoard('https://egov.presov.sk/Default.aspx?NavigationState=610:0::plac2150:_144134_5_8');
        $list = $this->rss($list);

        return $list;
    }

    public function __toString()
    {
        return 'Presov';
    }

    public function contactEmail()
    {
        return 'kontakt@oznamypresov.sk';
    }

    public function contactWeb()
    {
        return 'https://www.presov.sk/kontakt.html';
    }

    public function humanName()
    {
        return 'Prešov';
    }

    public function domain()
    {
        return 'oznamypresov.sk';
    }

    public function podnetyCategoryId()
    {
        return 11;
    }

    public function scheduleCrons(Schedule $schedule)
    {
        $schedule->command('Scrapper')->hourly()->between('6:00', '22:00');;
        $schedule->command('Objednavky')->dailyAt('20:00');
        $schedule->command('ScrapperZmluvy')->dailyAt('21:00');
        $schedule->command('ScrapperObjednavky')->dailyAt('21:30');
        $schedule->command('ScrapperFaktury')->dailyAt('22:00');
        $schedule->command('DeleteBoard')->dailyAt('03:00');
        $schedule->command('ParcelFinder')->dailyAt('03:17');
        $schedule->command('DailySender')->dailyAt('14:30');
        $schedule->command('SendEmails')->hourlyAt('5')->between('15:00', '22:00');
        $schedule->command('SyncStreets')->weeklyOn(1, '2:00');
        $schedule->command('Ocr')->hourlyAt(10);
        $schedule->command('NoOutput')->everyMinute();
    }

    public function categoriesToDelete()
    {
        return [14];
    }

    public function cadasterNumbers()
    {
        return [
            '' => '[neznáme]',
            '2403' => 'Dulova Ves',
            '2187' => 'Brestov',
            '1577' => 'Bzenov',
            '2790' => 'Červenica',
            '3188' => 'Fintice',
            '758' => 'Gregorovce',
            '1905' => 'Haniska',
            '2314' => 'Kendice',
            '70' => 'Lipovce',
            '2703' => 'Ľubotice',
            '267' => 'Ľubovec',
            '117' => 'Malý Šariš',
            '54' => 'Mirkovce',
            '3292' => 'Petrovany',
            '323' => 'Podhradík',
            '2185' => 'Prešov',
            '3120' => 'Radatice',
            '2519' => 'Sedlice',
            '2262' => 'Šalgovík',
            '497' => 'Solivar',
            '3470' => 'Šarišské bohdanovce',
            '3361' => 'Teriakovce',
            '1841' => 'Terňa',
            '3456' => 'Nižná Šebastová',
            '1062' => 'Veľký Šariš',
            '2796' => 'Vyšná Šebastová',
            '2035' => 'Záborské',
            '676' => 'Župčany',
        ];
    }

    public function latitude()
    {
        return 48.997312;
    }

    public function longitude()
    {
        return 21.241390;
    }

    public function getStreetNames()
    {
        $ulice = json_decode(file_get_contents('http://egov.presov.sk/GeoDataKatalog/ulice.json'));
        if ($ulice === null) {
            throw new \Exception("Invalid JSON in ulice");
        }

        return collect($ulice->features)->map(function ($item) {
            return $item->properties->N_GM_U;
        });
    }

    public function hasGeoAddresses()
    {
        return true;
    }

    public function syncStreets()
    {
        resolve(SyncStreets::class)->handle("https://egov.presov.sk/GeoDataKatalog/ulice.json");
    }

    public function isCategoryForWholeCity($category)
    {
        return in_array($category, [
            'Aktuálne udalosti',
            'VZN mesta Prešov',
            'Dražby',
            'Výberové konania',
            'Prenájom / predaj nehnuteľností (zámer)',
            'Doručenie / uloženie písomnosti',
            'Oznámenia o zrušení trvalého pobytu',
        ]);
    }

    public function titlesForWholeCity()
    {
        return [
            'Termíny zasadnutí výborov v mestských častiach',
            'Oznámenie o zániku mandátu poslanca Mestského zastupiteľstva',
            'Poradovník žiadateľov o pridelenie nájomného bytu',
            'Poradovník žiadatel\'ov o pridelenie nájomného bytu',
            'Oznámenie o zasadnutí Mestského zastupiteľstva',
            'Evidencia čakateľov na zabezpečenie poskytovania sociálnej služby',
            'Evidencia čakatel\'ov na zabezpečenie poskytovania sociálnej služby',
            'Evidencia žiadateľov o zabezpečenie poskytovania sociálnej služby',
        ];
    }

    public function textForWholeCity()
    {
        // TODO: Implement textForWholeCity() method.
    }

    private function rss(array $list)
    {
        $xml = file_get_contents('https://www.presov.sk/get_rss.php?id=1_atom_5251');
        $xml = new \SimpleXMLElement($xml);
        foreach ($xml->entry as $line) {
            $line = (array)$line;
            $data = resolve(Rss::class)->createData(trim($line['category']['term']), trim($line['title']), trim($line['summary']),
                trim($line['published']));
            if ($data === false) {
                continue;
            }

            $list[] = $data;
        }

        return $list;

    }

    public function electricityCategory()
    {
        return [
            1
        ];
    }

    public function googleAnalyticsTag()
    {
        return 'UA-12442622-8';
    }

    public function egovPage()
    {
        return 'https://egov.presov.sk/Default.aspx?NavigationState=1100:0:';
    }
}