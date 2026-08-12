<?php

namespace App\Console\Commands;

use App\Board;
use App\Cityzen;
use App\CityzenBoard;
use App\Log;
use App\Traits\CronLogging;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailySender extends Command
{
    use CronLogging;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailySender';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DailySender';
    /**
     * @var SendEmails
     */
    private $sender;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SendEmails $sender)
    {
        parent::__construct();
        $this->sender = $sender;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logStart();
        $cityzens = Cityzen::where('receive_type',
            Cityzen::DAILY)->whereNotNull('email')
            ->get();
        $sent = [];
        foreach ($cityzens as $cityzen) {
            /** @var Cityzen $cityzen */
            $boardsStreetMatch = Board::select(
                'boards.*',
                DB::raw("GROUP_CONCAT(tagging_tagged.tag_name SEPARATOR ', ') as streets"),
                DB::raw('"' . Board::MATCH_STREET . '" as reason'))
                ->notForWholeCity()
                ->notSent('sent_daily')
                ->byStreet($cityzen)
                ->get();
            $boards = $boardsStreetMatch;
            $forWholeCity = Board::select('boards.*',
                DB::raw('"' . Board::MATCH_CITY . '" as reason'))->forWholeCity()->notSent('sent_daily')->get();
            $all = $boards->union($this->getBoardsByPoint($cityzen));
            $all = $all->merge($forWholeCity);
            if ($all->count() == 0) {
                Log::debug("No boards for cityzen " . $cityzen->id);
                continue;
            }
            $cityzenCategories = $cityzen->categories()->pluck('categories.id')->toArray();
            $allResult = collect([]);
            foreach ($all as $board) {
                if (empty($cityzenCategories) || in_array($board->category_id, $cityzenCategories)) {
                    $allResult[$board->id] = $board;
                }
            }
            if ($allResult->count() === 0) {
                Log::debug("No categories " . $cityzen->id);
                continue;
            }
            foreach ($allResult as $board) {
                CityzenBoard::create([
                    'cityzen_id' => $cityzen->id,
                    'board_id' => $board->id,
                    'date_sent' => '0000-00-00 00:00:00',
                    'reason' => $board->reason,
                ]);
                $sent[$board->id] = $board->id;
            }
        }
        Board::where([
            'sent_daily' => false,
            'status' => Board::STATUS_DONE
        ])->whereIn('id', $sent)
            ->update(['sent_daily' => true]);
        $this->sender->handle();
        $this->logEnd();
    }

    public function getBoardsByPoint(Cityzen $cityzen)
    {
        $boardsResult = [];
        $boards = Board::select('boards.*',
            DB::raw('"' . Board::MATCH_POINT . '" as reason'))->notForWholeCity()->notSent('sent_daily')->get();
        $radius = $cityzen->radius;
        $x1 = addToLatLng($radius, $radius, $cityzen->latitude, $cityzen->longitude);
        $x2 = addToLatLng($radius, $radius * -1, $cityzen->latitude, $cityzen->longitude);
        $x3 = addToLatLng($radius * -1, $radius, $cityzen->latitude, $cityzen->longitude);
        $x4 = addToLatLng($radius * -1, $radius * -1, $cityzen->latitude, $cityzen->longitude);

        $vertices_x = array(
            $x4[0],
            $x2[0],
            $x1[0],
            $x3[0],
        );    // x-coordinates of the vertices of the polygon
        $vertices_y = array(
            $x4[1],
            $x2[1],
            $x1[1],
            $x3[1],
        );
        Log::debug("vertices_x " . print_r($vertices_x, 1));
        Log::debug("vertices_y " . print_r($vertices_y, 1));
        $points_polygon = count($vertices_x);  // number vertices - zero-based array
        foreach ($boards as $board) {
            if (!$cityzen->isSetUp()) {
                $board['reason'] = Board::MATCH_POINT_NOT_SET_UP;
                $boardsResult[$board->id] = $board;
                continue;
            }
            if ($this->checkPointsInPolygon($board, $points_polygon, $vertices_x, $vertices_y)) {
                $boardsResult[$board->id] = $board;
                continue;
            }
            if ($this->checkParcelsInPolygon($board, $points_polygon, $vertices_x, $vertices_y)) {
                $boardsResult[$board->id] = $board;
                continue;
            }
        }

        return collect($boardsResult);
    }


    function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $vertices_x = collect($vertices_x);
        $vertices_y = collect($vertices_y);
        Log::debug("is_in_polygon(" . print_r($points_polygon,
                1) . ", $vertices_x, $vertices_y, $longitude_x, $latitude_y)");
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon - 1; $i < $points_polygon; $j = $i++) {
            if ((($vertices_y[$i] > $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
                ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]))) {
                $c = !$c;
            }
        }
        Log::debug("Is $c");

        return $c;
    }

    private function checkPointsInPolygon(Board $board, $points_polygon, $vertices_x, $vertices_y)
    {
        foreach ($board->points as $point) {
            if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $point->latitude,
                $point->longitude)) {
                return true;
            }
        }

        return false;
    }

    private function checkParcelsInPolygon(Board $board, $points_polygon, $vertices_x, $vertices_y)
    {
        foreach ($board->parcels as $parcel) {
            $points = json_decode($parcel->points, 1);
            if (empty($points)) {
                continue;
            }
            foreach ($points as $latLon) {
                if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latLon[0],
                    $latLon[1])) {
                    return true;
                }
            }
        }

        return false;
    }
}
