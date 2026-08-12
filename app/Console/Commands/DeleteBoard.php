<?php

namespace App\Console\Commands;

use App\Board;
use Illuminate\Console\Command;

class DeleteBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteBoard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DeleteBoard';

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
        $categories = city()->categoriesToDelete();
        if (empty($categories)) {
            return;
        }
        Board::whereIn('category_id', $categories)
            ->where('deleted', 0)
            ->where('published_uet_until', '<', dateNow())
            ->update([
                'deleted' => 1,
                'deleted_date' => dateTimeNow()
            ]);
    }
}
