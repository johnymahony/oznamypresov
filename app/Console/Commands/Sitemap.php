<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Cityzen;
use App\Log;
use App\Street;
use App\StreetName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use proj4php\Point;
use proj4php\Proj;
use proj4php\Proj4php;

class Sitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sitemap';

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
        foreach (\App\Category::all() as $category) {
            $category->slug = toSlug($category->name);
            $category->addToSitemap();
        }
        foreach (\App\Board::all() as $board) {
            $board->slug = toSlug($board->description);
            $board->save();
            $board->addToSitemap();
        }
    }
}
