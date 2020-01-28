<?php
namespace App\Services;

use App\Repositories\AREmployeeTimeRepository;
use App\Repositories\ARVacationRepository;
use App\Repositories\GoogleCalendarEventsRepository;
use App\Repositories\GoogleCalendarHolidaysRepository;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Репозиторий расписания рабочего времени сотрудника
     *
     * @var App\Repositories\AREmployeeTimeRepository
     */
    protected $employeeTime;

    /**
     * Репозиторий праздников
     *
     * @var App\Repositories\GoogleCalendarHolidaysRepository
     */
    protected $holiday;

    /**
     * Репозиторий отпусков
     *
     * @var App\Repositories\ARVacationRepository
     */
    protected $vacation;

    /**
     * Репозиторий событий: отгулы, корпоративы и прочее
     *
     * @var App\Repositories\GoogleCalendarEventsRepository
     */
    protected $events;

    /**
     * Массив выходных данных с рабочим расписание
     *
     * @var array
     */
    protected $data = [];

    /**
     * Конструктор класса ScheduleService
     *
     * @param App\Repositories\ARVacationRepository $vacation
     * @param App\Repositories\AREmployeeTimeRepository $employeeTime
     * @param App\Repositories\GoogleCalendarHolidaysRepository $holiday
     * @param App\Repositories\GoogleCalendarEventsRepository $events
     *
     */
    public function __construct(
        ARVacationRepository $vacation,
        AREmployeeTimeRepository $employeeTime,
        GoogleCalendarHolidaysRepository $holiday,
        GoogleCalendarEventsRepository $events
    )
    {
        $this->vacation = $vacation;
        $this->employeeTime = $employeeTime;
        $this->holiday = $holiday;
        $this->events = $events;
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
     */
    private function generateTimetable($id, $startDate, $endDate)
    {
        $timeRanges = $this->employeeTime->find($id);

        $businessDaysPeriod = $this->getBusinessDaysPeriod($startDate, $endDate, $id);

        $events = $this->events->between($startDate, $endDate);

        /*foreach ($businessDaysPeriod as $n => $date) {
            $this->data['schedule'][$n]['day'] = $date->format('Y-m-d');
            $this->data['schedule'][$n]['timeRanges'] = $timeRanges;
        }*/

        foreach ($businessDaysPeriod as $date) {
            foreach ($timeRanges as $time) {
                $range['start'] = Carbon::create($date->format('Y-m-d') . ' ' . $time['start']);
                $range['end'] = Carbon::create($date->format('Y-m-d') . ' ' . $time['end']);
                array_push($this->data, $range);
            }
        }

        foreach ($this->data as &$interval) {
            foreach ($events as $event) {
                $period = CarbonPeriod::create($interval['start'], $interval['end']);
                if ($period->overlaps($event['start_date'], $event['end_date'])) {
                    if ($interval['start'] <= $event['start_date'] && $interval['end'] <= $event['end_date']) {
                        $interval['end'] = $event['start_date'];
                    } elseif ($interval['start'] >= $event['start_date'] && $interval['end'] >= $event['end_date']) {
                        $interval['start'] = $event['end_date'];
                    } else {
                        $interval['start'] = $event['start_date'];
                        $interval['end'] = $event['end_date'];
                    }
                }
            }
        }

        dd($this->data);
        return $this->data;
    }

    /**
     * Получение рабочих дней сотрудника
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $id
     *
     * @return \Carbon\CarbonPeriod
     */
    private function getBusinessDaysPeriod($startDate, $endDate, $id)
    {
        $holidays = $this->holiday->all();

        $vacations = $this->vacation->find($id);

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
     */
    private function isHoliday($holidays, $date)
    {
        return in_array($date, $holidays);
    }
}
