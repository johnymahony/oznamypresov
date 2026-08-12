<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    public $fillable = ['name', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
