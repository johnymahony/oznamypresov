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

class Objednavky extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Objednavky';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Objednavky';
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
        Log::debug('Objednavky start');
        $currentFile = storage_path() . '/app/Zoznam_Objednavky.json';
        if (file_exists($currentFile)) {
            Storage::put('Zoznam_ObjednavkyOld' . date('YmdHis') . '.json', file_get_contents($currentFile));
        }
        Storage::put('Zoznam_Objednavky.json', file_get_contents('https://egov.presov.sk/Default.aspx?NavigationState=781:0::plac1931:_144104_5_8'));
    }

}
