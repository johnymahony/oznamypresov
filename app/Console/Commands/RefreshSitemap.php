<?php

namespace App\Console\Commands;

use App\Board;
use App\Category;
use Illuminate\Console\Command;

class RefreshSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RefreshSitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $file = storage_path() . '/sitemap.xml';
        @unlink($file);
        Category::all()->each(function (Category $category) {
            $category->addToSitemap();
        });
        $list = Board::all();
        $p = $this->output->createProgressBar($list->count());
        $list->each(function (Board $board) use ($p) {
            $board->addToSitemap();
            $p->advance();
        });
        $p->finish();
    }
}
