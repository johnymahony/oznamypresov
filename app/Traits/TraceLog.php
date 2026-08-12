<?php
namespace App\Traits;

use App\Log;
use Illuminate\Support\Facades\DB;

trait TraceLog
{

    public function save(array $options = [])
    {
        if ($this->id && config('app.env') != 'testing') {
            $className = get_class($this);
            $oldData = $className::find($this->id);
            if(empty($oldData)) {
                return;
            }
            $changed = [];
            foreach ($oldData->original as $column => $value) {
                if ($value != $this->{$column}) {
                    $changed[$column]['from'] = $value;
                    $changed[$column]['to'] = $this->{$column};
                }
            }
            $this->addManualTrace($changed);
        }
        return parent::save($options);
    }

    public function addManualTrace($changed)
    {
        if (empty($changed)) {
            return;
        }
        $current = Log::$disableQueryLog;
        Log::$disableQueryLog = true;
        $traceId = DB::table('trace_log')->insertGetId([
            'user' => user(),
            'model' => @array_pop(explode('\\', get_class($this))),
            'model_id' => $this->id,
            'date' => date('Y-m-d H:i:s'),
            'context_model_id' => \App\TraceLog::$contextModelId,
            'context_model_name' => \App\TraceLog::$contextModelName,
            'context_comment' => substr(\App\TraceLog::$contextComment, 0, 255),
        ]);
        foreach ($changed as $column => $data) {
            DB::table('trace_log_values')->insertGetId([
                'trace_log_id' => $traceId,
                'column' => $column,
                'from' => $data['from'],
                'to' => $data['to'],
                'date' => date('Y-m-d H:i:s')
            ]);
        }
        Log::$disableQueryLog = $current;
    }
}