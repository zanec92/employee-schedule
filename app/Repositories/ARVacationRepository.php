<?php

namespace App\Repositories;

use App\Models\Employee;

class ARVacationRepository implements VacationRepositoryInterface
{
    /**
     * Модель сотрудника компании
     *
     * @var App\Employee
     */
    protected $employee;

    /**
     * Конструктор класса ARVacationRepository
     *
     * @param App\Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Получить даты отпусков сотрудника компании
     *
     * @param int $id
     *
     * @return array
     */
    public function find($id): array
    {
        return $this->employee->find($id)
            ->vacation()
            ->select('vacation_from', 'vacation_to')
            ->get()
            ->toArray();
    }
}
