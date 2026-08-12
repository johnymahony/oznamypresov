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

class Retag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Retag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retag';
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

    public function getStreets()
    {
        $this->streets = Tag::where('tag_group_id', 1)->get();
        return $this->streets;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug('Retag start');
        DB::table('tagging_tagged')->delete();
        DB::table('tagging_tags')->update(['count' => 0]);
        $texts = Attachment::where('plain_text', '!=', '')
//            ->where('board_id', 42)
            ->get();
        $job = new Scrapper();
        foreach ($texts as $attachment) {
            $job->resolveTags($attachment, $attachment->board);
        }
    }

}
