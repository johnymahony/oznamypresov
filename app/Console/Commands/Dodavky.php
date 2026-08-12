<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Category;
use App\Log;
use Conner\Tagging\Model\Tag;
use Conner\Tagging\Model\Tagged;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PdfToText;
use SimpleXMLElement;

class Dodavky extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Dodavky';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dodavky';
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
     */
    public function handle()
    {
        Log::debug('Dodavky start');

        $json = file_get_contents('Zoznam_Dodávateľské_faktúry.json');
        $data = json_decode($json);
        $ico = [];
        foreach ($data as $row) {
            $year = date('Y', strtotime($row->{'Dátum_zverejnenia'}));
            if($year != 2017) {
                continue;
            }
//            @$ico[$row->{'IČO'}]['name'] = $row->{"Dodávateľ"};
//            @$ico[$row->{'IČO'}]['sum'] += floatval($row->{"Hodnota_objednávky"});
            @$ico[$row->{'Dodávateľ'}.', ICO: '.@$row->{'IČO_-_Identifikačné_číslo_dodávateľa'}] += floatval(@$row->{"Celková_cena"});
        }
        asort($ico);
        dd($ico);
    }

}
