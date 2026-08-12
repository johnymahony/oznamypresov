<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $fillable = ['name', 'slug'];

    public function addToSitemap()
    {
        dispatchJob(new \App\Jobs\AddToSitemap(url('/') . '/filter/' . date('Y') . '/' . $this->slug . ''));
        dispatchJob(new \App\Jobs\AddToSitemap(url('/') . '/filter/' . $this->slug . ''));
    }
}
