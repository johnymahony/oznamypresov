<?php

namespace App\Console\Commands;

use App\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class ScrapperFaktury extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapperFaktury';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ScrapperFaktury';
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
            Log::error("Problem in ScrapperFaktury: " . $e);
            return;
        }
    }

    public function handleXml()
    {
        Log::debug('ScrapperFaktury start');
        $content = file_get_contents('http://egov.presov.sk/default.aspx?NavigationState=779:0:');
        $content = str_replace("\r\n", '', $content);
        $content = str_replace("\n\r", '', $content);
        $content = str_replace("\n", '', $content);
        $content = str_replace("\t", '', $content);
        $content = str_replace("<br>", '', $content);
        $content = preg_replace("/\s+/", ' ', $content);
        $content = preg_replace("/class=\".*?\"/", ' ', $content);
        $content = substr_replace($content, '', 0, strpos($content, '<tr rId'));
        $content = substr($content, 0, strpos($content, '</tbody'));
        $path = 'attachments/faktury/' . date('Y-m') . '/data'.date('d').'.xml';
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
SimpleXMLElement {#702
  +"@attributes": array:1 [
    "rId" => "146315"
  ]
  +"td": array:19 [
    0 => SimpleXMLElement {#711
      +"div": "Detail"
    }
    1 => "2019000865"
    2 => "DD21, s.r.o."
    3 => "Mesto Prešov"
    4 => "-čiernobiele kópie A3, A4"
    5 => " 94,73"
    6 => "EUR"
    7 => SimpleXMLElement {#712}
    8 => SimpleXMLElement {#713}
    9 => SimpleXMLElement {#714}
    10 => "07.03.2019"
    11 => SimpleXMLElement {#715
      +"a": SimpleXMLElement {#722
        +"@attributes": array:2 [
          "target" => "_blank"
          "href" => "FileOutputHttpHandler.ashx?arguments=CMERfbTJqCgNsn74xltkDOud%2fxzulfEGyBVkCuDi6pePo%2f2iomUZW9xFLBU0K7rQT3%2blkpU95nu%2f%2bsEQlAWOacj0dCfM726WOA%3d%3d"
        ]
        +"img": SimpleXMLElement {#725
          +"@attributes": array:3 [
            "alt" => "Dokument_1"
            "src" => "ResourceHttpHandler.ashx?Cora.Natec.Frm.UI%2fImages.document.gif"
            "title" => "Dokument "2019000865". Dátum zverejnenia 7.3.2019. Po kliknutí na túto ikonu sa otvorí súbor "2019000865.PDF" o veľkosti 51,4 kB."
          ]
        }
      }
    }
    12 => "43818030 "
    13 => "Švábska 240/107, Prešov, 08005"
    14 => "nezaplatené"
    15 => "OO ORGANIZAČNÝ ODBOR"
    16 => "190158"
    17 => "Finálna"
    18 => SimpleXMLElement {#716}
  ]
}
             */
//            dd($row[6]->a['href']);
            try {
//                dd($row[10]->a['href']);
//                var_dump($row);
                $path = 'attachments/faktury/' . date('Y-m') . '/' . $row[1] . '.pdf';
                $file = Storage::put($path, file_get_contents("https://egov.presov.sk/".$row[11]->a['href']));
                echo "OK\n";
                sleep(2);
            } catch (\Exception $e) {
                Log::error("Problem in ScrapperFaktury ($i): " . $e);
            }
            $i++;
        }
    }
}
