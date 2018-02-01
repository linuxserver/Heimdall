<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = [
        'title', 'url', 'colour', 'icon', 'description', 'pinned'
    ];
}
