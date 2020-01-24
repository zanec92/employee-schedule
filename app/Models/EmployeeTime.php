<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTime extends Model
{
    /**
     * Имя таблицы, связанной с моделью
     *
     * @var string
     */
    protected $table = 'employee_time';

    /**
     * Атрибуты, которые должны быть доступны при массовом заполнении
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'end'
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
}
