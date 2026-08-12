<?php

namespace App;

use App\Console\Commands\Scrapper;
use App\Jobs\UploadToCruncher;
use App\Process\BoardProcessor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property Board $board
 * @property string $cruncher_id
 */
class Attachment extends Model
{
    use HasFactory;

    public $fillable = ['title', 'path', 'plain_text', 'ocr', 'cruncher_id'];

    public function board()
    {
        return $this->belongsTo('App\Board');
    }

    public function uploadForOcr()
    {
        if (config('app.cruncher.driver') !== 'null') {

            dispatch(new UploadToCruncher($this));

            return;
        }
        $folder = 'sub/ocr/' . $this->board_id . city() . '.pdf';
        $content = Storage::get($this->board->attachment_path);
        if (isPresov()) {
            file_put_contents(base_path() . '/../' . $folder,
                $content);

            return;
        }

        Storage::disk('presov-ftp')->put($folder, $content);
    }

    public function scopeWithoutText($query)
    {
        $query->where(function ($query) {
            $query->whereNull('plain_text')->orWhere('plain_text', '');
        });
    }

    public function scopeWithText($query)
    {
        $query->whereNotNull('plain_text');
    }

    public function processText()
    {
        if (strtotime($this->created_at) < strtotime('2020-02-10 11:00:00')) {
            Log::debug("Too old");

            return;
        }
        /** @var Scrapper $scrapper */
        $scrapper = resolve(Scrapper::class);
        $scrapper->resolveParcels($this, $this->board);
        Log::debug("OCR finished");
        /** @var Board $board */
        $board = Board::find($this->board_id);
        if ($board->parcels()->count() > 0 && ! $board->cadaster_number) {
            Problem::make("Cadaster not found", $board->id);
        }
        if (city()->isTextForWholeCity($this->plain_text)) {
            $this->board->setWholeCity();
        }
        (new BoardProcessor())->process($this->board);
    }
}
