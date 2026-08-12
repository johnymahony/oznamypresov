<?php

namespace App\Console\Commands;

use App\Board;
use App\Category;
use App\Observers\BoardObserver;
use App\Traits\GetJsonFromCity;
use Illuminate\Console\Command;

class Podnety extends Command
{
    use GetJsonFromCity;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Podnety';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Podnety';

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
        $all = $this->getFromJson('https://egov.presov.sk/Default.aspx?NavigationState=921:0::plac2138:_292003_5_8');
//        $all     = $this->getFromJson(base_path('Zoznam_Podnety_a_požiadavky_občanov.json'));
        $allOpen = collect($all)
//            ->filter(function ($podnet) {
//                return $podnet['Stav_riešenia'] !== 'Vyriešený/-á';
//            })
        ;
        $i       = 1;
        foreach ($allOpen as $item) {
            $podnetRaw = $this->translate($item);
            $this->store($podnetRaw);
            echo "\n" . $i++ . " / " . $allOpen->count() . "\n";
            if ($i === 1000) {
                break;
            }
        }
    }

    private function translate($item)
    {
        return [
            'po_id'           => $item['Označenie'],
            'title'           => $item['Názov_podnetu'],
            'description'     => $item['Popis_podnetu'],
            'published_at'    => dateNow($item['Dátum_doručenia']),
            'category_id'     => city()->podnetyCategoryId(),
            'iniciative_type' => $item['Typ_podnetu'],
            'solution'        => $item['Riešenie'],
            'person'          => $item['Riešiteľ'],
            'solution_state'  => $item['Stav_riešenia'],
            'solution_date'   => $item['Dátum_vyriešenia'],
            'address'         => $item['Lokalita'],
        ];
    }

    /**
     * @param array $podnetRaw
     *
     */
    private function store(array $podnetRaw)
    {
        $board = Board::wherePoId($podnetRaw['po_id'])->first();
        if (empty($board)) {
            Board::create($podnetRaw);

            return;
        }
        $original = $board->toArray();
        $board->update($podnetRaw);
        resolve(BoardObserver::class)->updatedCustom($board, $podnetRaw, $original);
    }
}