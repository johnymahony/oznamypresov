<?php
namespace App\Traits;


use App\TraceLog;

trait TraceLogContext
{
    public $contextModelId;
    public $contextModelName;
    public $contextComment;

    public function saveTraceLogContext()
    {
        $this->contextComment = TraceLog::$contextComment;
        $this->contextModelName = TraceLog::$contextModelName;
        $this->contextModelId = TraceLog::$contextModelId;
    }

    public function loadTraceLogContext()
    {
        if (!empty($this->contextComment)) {
            TraceLog::$contextComment = $this->contextComment;
        }
        if (!empty($this->contextModelName)) {
            TraceLog::$contextModelName = $this->contextModelName;
        }
        if (!empty($this->contextModelId)) {
            TraceLog::$contextModelId = $this->contextModelId;
        }
    }
}