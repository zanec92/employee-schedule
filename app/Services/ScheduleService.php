<?php
namespace App\Services;

use App\Repositories\ARScheduleRepository;
use Carbon\CarbonPeriod;

class ScheduleService
{
    protected $schedule;

    public function __construct(ARScheduleRepository $schedule)
    {
        $this->schedule = $schedule;
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

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $key => $date) {
            if ($date->isWeekday()) { //отбираем только рабочие дни
                $data['schedule'][$key]['day'] = $date->format('Y-m-d');
                $data['schedule'][$key]['timeRanges'] = $timeRanges;
            }
        }

        return $data;
    }
    
}