<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    /**
     * Атрибуты, которые должны быть доступны при массовом заполнении
     *
     * @var array
     */
    protected $fillable = [
        'vacation_from',
        'vacation_to'
    ];

    /**
     * Атрибуты, которые должны быть скрыты в преобразованном массиве
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Атрибуты, которые должны быть видоизменены по датам
     *
     * @var array
     */
    protected $dates = [
        'vacation_from',
        'vacation_to'
    ];
}
