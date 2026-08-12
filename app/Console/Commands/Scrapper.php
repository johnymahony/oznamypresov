<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Helpers\CadasterNumber;
use App\Helpers\HttpRequest;
use App\Helpers\TextToParcels;
use App\Log;
use App\Problem;
use App\Traits\CronLogging;
use App\Traits\GetJsonFromCity;
use Conner\Tagging\Model\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Scrapper extends Command
{
    use GetJsonFromCity, CronLogging;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Scrapper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapper';
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

    public static function exists($categorId, $title, $description, $date)
    {
        $where = [
            'category_id' => $categorId,
            'title' => trim($title),
            'description' => trim($description),
        ];
        /** @var Board $exists */
        $date = dateNow($date);

        return Board::where($where)->where(DB::raw('date(published_at)'), $date)->first();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $this->logStart();

        Log::debug('Scrapper start');
        $board_rows = city()->getBoard();

        $i = 1;
        foreach ($board_rows as $board_row) {
            echo $i . "/" . count($board_rows) . "\n";
            $exists = Scrapper::exists($board_row['category_id'], $board_row['title'], $board_row['description'],
                $board_row['published_at']);
            if (empty($exists)) {
                $data = [];
                $data['category_id'] = $board_row['category_id'];
                $data['title'] = trim($board_row['title']);
                $data['description'] = trim($board_row['description']);
                $data['published_at'] = trim($board_row['published_at']);
                $data['published_uet_until'] = @$board_row['published_uet_until'] ? trim($board_row['published_uet_until']) : dateNow('+21 days');
                $data['status'] = Board::STATUS_NEW;
                $data['link'] = $board_row['link'];
                $data['slug'] = $board_row['slug'];
                $data['po_id'] = md5(implode('', $data));
                /** @var Board $board */
                $board = Board::create($data);
                $board->addToSitemap();
                if (city()->isCategoryForWholeCity($board_row['category_label']) || city()->isTitleForWholeCity($board->title)) {
                    $board->setWholeCity();
                }
                if (empty($board->link)) {
                    Problem::make("Missing document link", $board->id);
                }
                $this->download($data, $board);
                echo "New\n";
            } else {
                $data['link'] = $board_row['link'];

                if (empty($exists->link)) {
                    $this->download($data, $exists);
                }
                echo "Exists\n";
            }
            $i++;
        }
        $this->logEnd();
    }

    /**
     * @param Board $board
     *
     * @return \App\Attachment
     * @throws \Exception
     */
    private function downloadAttachments(Board $board)
    {
        $request = new HttpRequest();
        $fileContent = $request->call($board->link);

        if (preg_match("/vnd\.etsi\.asic/", $request->responseHeaders)) {
            $extension = 'asice';
        } elseif (preg_match("/application\/pdf/", $request->responseHeaders)) {
            $extension = 'pdf';
        } else {
            throw new \Exception("unknown file format in scrapper " . $request->responseHeaders);
        }
        $path = 'attachments/' . date('Y-m') . '/' . $board->id . '.' . $extension;

        Storage::put($path, $fileContent);
        /** @var Attachment $attachment */
        $attachment = $board->attachments()->create([
            'title' => '',
            'path' => $path
        ]);

        $board->update([
            'attachment_path' => $path
        ]);

//        require_once(base_path() . '/app/External/PdfToText/PdfToText.php');
//
//        $plainText = '[OCR parsing failed]';
//
//        try {
//            $pdf = new PdfToText (storage_path() . '/app/' . $path);
//            if ($pdf->Text) {
//                $plainText = $pdf->Text;
//            }
//        } catch (\Exception $e) {
//            Log::error("OCR exception: [board: {$board->id}]" . $e->getMessage());
//        }
//        $attachment->update(['plain_text' => $plainText]);

        return $attachment;
    }

    public function resolveTags(Attachment $attachment, Board $board)
    {
        $text = strtolower(diakritika($attachment->plain_text));
        $text = trim($text);
        if (empty($text)) {
            return;
        }

        $blacklist = [
            "\n",
            "\r",
            "\r\n",
            "\n\r",
            'hlavna 73',
            'hlavna budova',
            'hlavna promenada',
            'hlavna a',
            'hurbanistov 3',
            'hviezdoslavova 7',
            'hlavna ul. c. 73',
            'jarkova c. 26',
            'jarkova c. 24',
            'jarkova 26',
            'jarkova 24',
            'vychodoslovenska',
            'slovenska republika',
            'mlynska 31',
            'bastova 44',
            'namestie mieru 3',
        ];
        foreach ($blacklist as $word) {
            $text = preg_replace("/" . $word . "/", '', $text);
        }

        $stripToEnd = [
            'navedomie:'
        ];
        foreach ($stripToEnd as $strip) {
            $start = strpos($text, $strip);
            if ($start !== false) {
                $text = substr_replace($text, '', $start, strlen($text) - $start);
            }
        }
        $this->streets = Tag::where('tag_group_id', 1)->get();
        foreach ($this->streets as $street) {
            $streetToSearch = $street->name;
            $streetToSearch = strtolower(diakritika($streetToSearch));
            if (preg_match("/" . $streetToSearch . "/", $text)) {
                $board->tag($street->name);
            }
        }
    }

    /**
     * @param Attachment $attachment
     * @param Board $board
     *
     * @throws \Exception
     */
    public function resolveParcels(Attachment $attachment, Board $board)
    {
        $parcels = $this->resolveCadasterNumber($attachment);
        $board->parcels()->delete();
        $parcels = array_merge($parcels, $this->textToParcelNumbers($attachment->plain_text));
        $parcels = array_merge($parcels, $this->textToParcelNumbers($board->title));
        $parcels = array_merge($parcels, $this->textToParcelNumbers($board->description));

        if (empty($parcels) and $board->status === Board::STATUS_NEW) {
            $board->update([
                'status' => Board::STATUS_READY
            ]);

            return [];
        }
        if ( ! empty($parcels) and $board->status === Board::STATUS_READY) {
            $board->update([
                'status' => Board::STATUS_NEW
            ]);

            return [];
        }
        foreach ($parcels as $parcel) {
            $board->parcels()->create([
                'number' => $parcel
            ]);
        }

        return $parcels;
    }

    public function resolveCadasterNumber(Attachment $attachment)
    {
        /** @var CadasterNumber $resolver */
        $resolver = resolve(CadasterNumber::class);
        $resolver->parcels = [];
        $number = $resolver->resolve($attachment->plain_text);
        if ($number === false) {
            $number = $resolver->resolve($attachment->board->description);
            if ($number === false) {
                $this->setNoCadaster($attachment);
            }
        }
        if ($attachment->board->status === Board::STATUS_NEW) {
            $attachment->board->update([
                'cadaster_number' => $number,
                'comment' => ''
            ]);
        }

        return $resolver->parcels;
    }


    /**
     * @param string $text
     *
     * @return array
     * @throws \Exception
     */
    private function textToParcelNumbers($text): array
    {
        return (new TextToParcels())->resolve($text);
    }

    /**
     * @param Attachment $attachment
     */
    public function setNoCadaster(Attachment $attachment): void
    {
        $update = [
            'cadaster_number' => '',
            'comment' => 'Katastrálne územie nenájdené'
        ];
        if ($attachment->board->status !== Board::STATUS_DONE) {
            $update['status'] = Board::STATUS_READY;
        }
        $attachment->board->update($update);
    }

    /**
     * @param array $data
     * @param Board $board
     *
     * @return array
     * @throws \Exception
     */
    private function download(array $data, Board $board)
    {
        if ( ! empty($data['link'])) {
            $board->update([
                'link' => $data['link']
            ]);
            try {
                $attachment = $this->downloadAttachments($board);
            } catch (\Exception $e) {
                Problem::make("Exception in download attachment", $board->id, $e . '');
            }
        }
    }
}
