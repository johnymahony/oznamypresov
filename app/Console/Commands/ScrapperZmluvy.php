<?php

namespace App\Console\Commands;

use App\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class ScrapperZmluvy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapperZmluvy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ScrapperZmluvy';
    private $streets;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $data = $this->handleXml();
        } catch (\Exception $e) {
            Log::error("Problem in ScrapperZmluvy: " . $e);
            return;
        }
    }

    public function handleXml()
    {
        Log::debug('ScrapperZmluvy start');
        $content = file_get_contents('http://egov.presov.sk/default.aspx?NavigationState=778:0:');
        $content = str_replace("\r\n", '', $content);
        $content = str_replace("\n\r", '', $content);
        $content = str_replace("\n", '', $content);
        $content = str_replace("\t", '', $content);
        $content = str_replace("<br>", '', $content);
        $content = preg_replace("/\s+/", ' ', $content);
        $content = preg_replace("/class=\".*?\"/", ' ', $content);
        $content = substr_replace($content, '', 0, strpos($content, '<tr rId'));
        $content = substr($content, 0, strpos($content, '</tbody'));
        $path = 'attachments/zmluvy/' . date('Y-m') . '/data'.date('d').'.xml';
        $file = Storage::put($path, $content);
        try {

            $xml = new SimpleXmlElement("<table>$content</table>");
        } catch (\Exception $e) {
            Log::error("Invalid XML in scrapper: " . $content);

            return;
        }
        $i = 1;
        foreach ($xml->tr as $row) {
            $poId = $row['rId'];
            $row  = $row->td;

            /*
             SimpleXMLElement {#689
  +"@attributes": array:1 [
    "rId" => "20801"
  ]
  +"td": array:19 [
    0 => SimpleXMLElement {#699
      +"div": "Detail"
    }
    1 => "201900209"
    2 => "Zmluva o dielo"
    3 => "na zhotovenie stavby"
    4 => "Mesto Prešov,EKOPRIM, s.r.o."
    5 => "Zhotovenie stavby stavby v rozsahu podľa spracovanej DSP pod názvom Revitalizácia vnútroblokového priestoru na ul. M. Čulena č. 29 - 49 1. etapa."
    6 => " 179 386,25"
    7 => "EUR"
    8 => "06.03.2019"
    9 => "06.03.2019"
    10 => SimpleXMLElement {#700
      +"a": SimpleXMLElement {#706
        +"@attributes": array:2 [
          "target" => "_blank"
          "href" => "FileOutputHttpHandler.ashx?arguments=CAnr7fptBfZbYfocncIOYe5N6CoZK9otbQm9QSoKTPJJa%2fvYYso2y9vsOjF9IuZ7JOG7LV9yCL4nSJqtLOHn3FZ%2b0PmBBI%2bYbA%3d%3d"
        ]
        +"img": SimpleXMLElement {#711
          +"@attributes": array:3 [
            "alt" => "Dokument_1"
            "src" => "ResourceHttpHandler.ashx?Cora.Natec.Frm.UI%2fImages.document.gif"
            "title" => "Dokument "ZMLUVA O DIELO - 201900209". Dátum zverejnenia 6.3.2019. Po kliknutí na túto ikonu sa otvorí súbor "ZMLUVA O DIELO - 201900209.PDF" o veľkosti 6,5 MB."
          ]
        }
      }
    }
    11 => "Zmluva o dielo na zhotovenie stavby: "Revitalizácia vnútroblokového priestoru na ul. M. Čulena č. 29 - 49 1. etapa""
    12 => "Hlavná"
    13 => "IČO: 00327646 Mesto Prešov,IČO: 31710115 EKOPRIM, s.r.o."
    14 => "Mesto Prešov - Hlavná 2907/73, Prešov, 08001,EKOPRIM, s.r.o. - Strojnícka 2084/17, Prešov, 08006"
    15 => SimpleXMLElement {#701}
    16 => "07.03.2019"
    17 => SimpleXMLElement {#702}
    18 => SimpleXMLElement {#703}
  ]
}
             */
//            dd($row[6]->a['href']);
            try {
//                dd($row[10]->a['href']);
//                var_dump($row);
                $path = 'attachments/zmluvy/' . date('Y-m') . '/' . $row[1] . '.pdf';
                $file = Storage::put($path, file_get_contents("https://egov.presov.sk/".$row[10]->a['href']));
                echo "OK\n";
                sleep(2);
            } catch (\Exception $e) {
                Log::error("Problem in ScrapperZmluvy ($i): " . $e);
            }
            $i++;
        }
    }
}
