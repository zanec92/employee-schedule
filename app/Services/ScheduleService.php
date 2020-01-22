<?php
namespace App\Services;

use App\GoogleCalendar\GoogleCalendar;
use App\Repositories\ARScheduleRepository;
use Carbon\CarbonPeriod;

class ScheduleService
{
    /**
     * Репозиторий расписания рабочего времени сотрудника
     * 
     * @var App\Repositories\ARScheduleRepository
     */
    protected $schedule;

    /**
     * Конструктор класса ScheduleService
     * 
     * @param App\Repositories\ARScheduleRepository $schedule
     * @param App\GoogleCalendar\GoogleCalendar $googleCalendar
     * 
     */
    public function __construct(ARScheduleRepository $schedule, GoogleCalendar $googleCalendar)
    {
        $this->schedule = $schedule;
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Получить рабочее расписание сотрудника за определенный срок
     * 
     * @param int $id
     * @param string $startDate
     * @param string $endDate
     * 
     * @return array
     */
    public function getEmployeeTimetable($id, $startDate, $endDate)
    {
        return $this->generateTimetable($id, $startDate, $endDate);
    }

    /**
     * Генерация рабочего расписания сотрудника за определенный срок
     * 
     * @param string $startDate
     * @param string $endDate
     * @param array $timeRanges
     * 
     * @return array
     * 
     */
    private function generateTimetable($id, $startDate, $endDate)
    {
        $data = [];

        $timeRanges = $this->schedule->findEmployeeTime($id);

        $businessDaysPeriod = $this->getBusinessDaysPeriod($startDate, $endDate, $id);

        foreach ($businessDaysPeriod as $n => $date) {
            $data['schedule'][$n]['day'] = $date->format('Y-m-d');
            $data['schedule'][$n]['timeRanges'] = $timeRanges;
        }

        return $data;
    }

    /**
     * Получение рабочих дней сотрудника
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int $id
     * 
     * @return \Carbon\CarbonPeriod
     * 
     */
    private function getBusinessDaysPeriod($startDate, $endDate, $id)
    {
        $holidays = $this->googleCalendar->getHolidays();

        $vacations = $this->schedule->findEmployeeVacationDays($id);

        return CarbonPeriod::create($startDate, $endDate)
            ->filter(function ($date) use ($holidays, $vacations) {
                return $date->isWeekday() 
                    && !$this->isHoliday($holidays, $date)
                    && !$this->isVacationDay($vacations, $date);
            });
    }

    /**
     * Проверка является ли день отпускным
     * 
     * @param array $vacations
     * @param Carbon\Carbon $date
     * 
     * @return bool
     * 
     */
    private function isVacationDay($vacations, $date)
    {
        foreach ($vacations as $vacation) {
            if ($vacation['vacation_from'] <= $date && $vacation['vacation_to'] >= $date) return true;
        }
        return false;
    }

    /**
     * Проверка является ли день праздником
     * 
     * @param 
     * @param Carbon\Carbon $date
     * 
     * @return bool
     * 
     */
    private function isHoliday($holidays, $date)
    {
        return in_array($date, $holidays);
    }
}