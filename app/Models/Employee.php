<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * Атрибуты, которые должны быть доступны при массовом заполнении
     *
     * @var array
     */
    protected $fillable = [
        'name'
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
     * Связь один-ко-многим с таблицей vacation
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vacation()
    {
        return $this->hasMany('App\Models\Vacation');
    }
}
