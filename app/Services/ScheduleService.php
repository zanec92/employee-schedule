<?php
namespace App\Services;

use App\GoogleCalendar\GoogleCalendar;
use App\Repositories\ARScheduleRepository;
use Carbon\CarbonPeriod;

class ScheduleService
{
    protected $schedule;

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
        $timeRanges = $this->schedule->findEmployeeTime($id);

        return $this->generateTimetable($startDate, $endDate, $timeRanges);
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
    private function generateTimetable($startDate, $endDate, $timeRanges)
    {
        $data = [];

        $businessDaysPeriod = $this->getBusinessDaysPeriod($startDate, $endDate);

        foreach ($businessDaysPeriod as $n => $date) {
            $data['schedule'][$n]['day'] = $date->format('Y-m-d');
            $data['schedule'][$n]['timeRanges'] = $timeRanges;
        }

        return $data;
    }

    private function getBusinessDaysPeriod($startDate, $endDate)
    {
        $holidays = $this->googleCalendar->getHolidays();

        return CarbonPeriod::create($startDate, $endDate)
            ->filter(function ($date) use ($holidays) {
                return $date->isWeekday() && !in_array($date, $holidays);
            });
    }
}