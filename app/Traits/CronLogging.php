<?php
namespace App\Traits;

use App\Log;

trait CronLogging
{

    public function logStart()
    {
        if ($this->output) {
            $this->info("Start cron " . get_class($this).' '.dateTimeNow());
        }
        setUser('Cron');
        Log::$logFile = 'cron';
        Log::init();
        Log::debug("Starting cron: " . get_class($this));
    }

    public function logEnd()
    {
        Log::debug("Ending cron: " . get_class($this));
        Log::$logFile = 'debug';
        if ($this->output) {
            $this->info("Ending cron " . get_class($this).' '.dateTimeNow());
        }
    }
}