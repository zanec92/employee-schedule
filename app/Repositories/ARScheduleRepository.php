<?php

namespace App\Repositories;

use App\Schedule;

class ARScheduleRepository
{
    protected $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Получить время работы сотрудника компании
     * 
     * @param int $id
     * 
     * @return array
     * 
     */
    public function findEmployeeTime($id)
    {
        return $this->schedule->select('start', 'end')
            ->where('user_id', $id)
            ->orderBy('start', 'asc')
            ->get()
            ->toArray();
    }
}