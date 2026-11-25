<?php

namespace App\Console\Commands;

use App\Board;
use App\Exceptions\UserException;
use App\Http\Ajax\FindParcel;
use App\Log;
use App\Parcel;
use Illuminate\Console\Command;

class ParcelFinder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ParcelFinder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ParcelFinder';

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
        Log::debug('ParcelFinder start');
        $parcels = Parcel::select('parcels.*')
            ->join('boards', 'boards.id', '=', 'parcels.board_id')
            ->whereNull('parcels.points')->get();
        $counts = [];
        $board = null;
        foreach ($parcels as $parcel) {
            Log::debug("FindingParcel {$parcel->id}");
            /** @var Parcel $parcel */
            /** @var Board $board */
            $board = $parcel->board;
            if ($board->status === Board::STATUS_NEW) {
                $board->status = Board::STATUS_READY;
                $board->save();
            }
            if (empty($board->cadaster_number)) {
                if (strtotime($board->created_at) < strtotime('-30 days')) {
                    $parcel->delete();
                }
                continue;
            }
            @$counts[$parcel->board_id]++;
            if ($counts[$parcel->board_id] > 20) {
                continue;
            }
            try {
                Parcel::checkNumber($parcel->number);
            } catch (UserException $e) {
                $parcel->update(['points' => 'E']);
                continue;
            }

            try {
                $parcel = (new FindParcel())->handle(['parcel_id' => $parcel->id]);
                if ($parcel instanceof Parcel) {
                    $board->status = Board::STATUS_DONE;
                    $board->save();
                }
            } catch (UserException $e) {
                Log::debug("Exception in ParcelFinder: {$parcel->id}:" . $e);
            }
            sleep(1);
        }
        if ($board && $board->status !== Board::STATUS_DONE) {
            $board->setWholeCity();
        }
        Log::debug('ParcelFinder end');
    }
}
