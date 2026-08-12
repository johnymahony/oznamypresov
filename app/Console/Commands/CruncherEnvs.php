<?php

namespace App\Console\Commands;

use App\Cruncher\DefaultDriver;
use Illuminate\Console\Command;

class CruncherEnvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruncher:envs';

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
        /** @var DefaultDriver $cruncher */
        $cruncher = resolve(DefaultDriver::class);
        $env = $cruncher->listEnvs();
        dd($env);
    }
}
