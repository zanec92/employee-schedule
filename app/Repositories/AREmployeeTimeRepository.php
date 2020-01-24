<?php

namespace App\Repositories;

use App\Models\EmployeeTime;

class AREmployeeTimeRepository implements EmployeeTimeRepositoryInterface
{
    /**
     * Модель расписания рабочего времени сотрудника
     *
     * @var App\EmployeeTime
     */
    protected $employeeTime;

    /**
     * Конструктор класса AREmployeeTimeRepository
     *
     * @param App\EmployeeTime $employeeTime
     */
    public function __construct(EmployeeTime $employeeTime)
    {
        $this->employeeTime = $employeeTime;
    }

    /**
     * Получить время работы сотрудника компании
     *
     * @param int $id
     *
     * @return array
     */
    public function find($id): array
    {
        return $this->employeeTime->select('start', 'end')
            ->where('employee_id', $id)
            ->orderBy('start', 'asc')
            ->get()
            ->toArray();
    }
}
