<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Cityzen;
use App\Cruncher\DefaultDriver;
use App\Log;
use App\Mail\MailSender;
use App\Parcel;
use App\Project;
use App\Street;
use App\StreetName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use proj4php\Point;
use proj4php\Proj;
use proj4php\Proj4php;

class CronTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CronTest';

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
        Attachment::all()->each(function (Attachment $attachment) {
            if(!Storage::exists($attachment->path)) {
                echo "{$attachment->created_at}: {$attachment->id} {$attachment->path}\n";
            }
        });
        echo "OK";
        return;
        dd(resolve(DefaultDriver::class)->listEnvs());

        $attachmens = Attachment::whereNotNull('ocr')->get();
        $found2     = $notFound = $noCataster = $noCityMatch = [];
        foreach ($attachmens as $i => $attachment) {
            $text = str_replace(["\n"], ' ', $attachment->ocr);
            if ( ! preg_match('/(katastr.{1,10}zem)/', $text, $m)) {
                echo "$i No kataster{$attachment->id}\n";
                $noCataster[] = $attachment->id;

                continue;
            }

            preg_match_all('/katastr.{1,10}zem.{1,2}(.{1,50})/s', $text, $matches);
            if (empty($matches)) {
                echo "$i No match kataster{$attachment->id}\n";
                $noCityMatch[] = $attachment->id;

                continue;
            }
            $found = false;
            $cadasterNumbers = cadasters();
            foreach ($matches[1] as $line) {
                foreach ($cadasterNumbers as $number => $city) {
                    if (empty($city)) {
                        continue;
                    }
                    if (preg_match('/' . $city . '/', $line)) {
                        $board = $attachment->board;
//                        $board->update(['comment' => $board->comment . ', kat: ' . $city]);
                        $found2[] = [
                            'attachment_id' => $attachment->id,
                            'old'           => @$cadasterNumbers[$board->cadaster_number],
                            'new'           => $city,
                            'matches' => $matches,
                        ];
                        echo "$i FOUND " . $city . "\n";
                    }
                }
            }

            if ( ! $found) {
                echo "$i NOT " . $city . "\n";
                $notFound[$attachment->id] = $matches;
            }
        }
        Log::debug(json_encode([
            'noCataster'  => $noCataster,
            'noCityMatch' => $noCityMatch,
            'found2'      => $found2,
            'notFound'    => $notFound,
        ]));
        echo json_encode([
            'noCataster'  => $noCataster,
            'noCityMatch' => $noCityMatch,
            'found2'      => $found2,
            'notFound'    => $notFound,
        ]);
    }
}
