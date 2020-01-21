<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'start',
        'end'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
