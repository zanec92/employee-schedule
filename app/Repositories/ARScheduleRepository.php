<?php

namespace App\Repositories;

use App\Schedule;
use App\User;

class ARScheduleRepository
{
    /**
     * Модель расписания рабочего времени сотрудника
     * 
     * @var App\Schedule
     * 
     */
    protected $schedule;

    /**
     * Конструктор класса ARScheduleRepository
     * 
     * @param App\Schedule $schedule
     * @param App\User $user
     * 
     */
    public function __construct(Schedule $schedule, User $user)
    {
        $this->schedule = $schedule;
        $this->user = $user;
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

    /**
     * Получить даты отпусков сотрудника компании
     * 
     * @param int $id
     * 
     * @return array
     * 
     */
    public function findEmployeeVacationDays($id)
    {    
        return $this->user->find($id)
            ->vacation()
            ->select('vacation_from', 'vacation_to')
            ->get()
            ->toArray();
    }
}