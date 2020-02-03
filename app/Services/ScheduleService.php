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
     * @var \App\Repositories\AREmployeeTimeRepository
     */
    protected $employeeTimeRepo;

    /**
     * Репозиторий праздников
     *
     * @var \App\Repositories\GoogleCalendarHolidaysRepository
     */
    protected $holidayRepo;

    /**
     * Репозиторий отпусков
     *
     * @var \App\Repositories\ARVacationRepository
     */
    protected $vacationRepo;

    /**
     * Репозиторий событий: отгулы, корпоративы и прочее
     *
     * @var \App\Repositories\GoogleCalendarEventsRepository
     */
    protected $eventsRepo;


    /**
     * Конструктор класса ScheduleService
     *
     * @param \App\Repositories\ARVacationRepository $vacationRepo
     * @param \App\Repositories\AREmployeeTimeRepository $employeeTimeRepo
     * @param \App\Repositories\GoogleCalendarHolidaysRepository $holidayRepo
     * @param \App\Repositories\GoogleCalendarEventsRepository $eventsRepo
     *
     */
    public function __construct(
        ARVacationRepository $vacationRepo,
        AREmployeeTimeRepository $employeeTimeRepo,
        GoogleCalendarHolidaysRepository $holidayRepo,
        GoogleCalendarEventsRepository $eventsRepo
    )
    {
        $this->vacationRepo = $vacationRepo;
        $this->employeeTimeRepo = $employeeTimeRepo;
        $this->holidayRepo = $holidayRepo;
        $this->eventsRepo = $eventsRepo;
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
    public function getEmployeeTimetable(int $id, string $startDate, string $endDate): array
    {
        $timeRanges = $this->employeeTimeRepo->find($id);
        $businessDaysPeriod = $this->getBusinessDaysPeriod($id, $startDate, $endDate);
        $events = $this->eventsRepo->between($startDate, $endDate);
        $workingTimeIntervals = $this->getWorkingTimeIntervals($businessDaysPeriod, $timeRanges);
        $workingTimeIntervalsWithoutEvents = $this->removeEventsFromWorkingTime($workingTimeIntervals, $events);

        return $this->generateOutputData($workingTimeIntervalsWithoutEvents);
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
    private function getBusinessDaysPeriod(int $id, string $startDate, string $endDate): CarbonPeriod
    {
        $holidays = $this->holidayRepo->all();
        $vacations = $this->vacationRepo->find($id);

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
     * @param \Carbon\Carbon $date
     *
     * @return bool
     */
    private function isVacationDay(array $vacations, Carbon $date): bool
    {
        foreach ($vacations as $vacation) {
            if ($vacation['vacation_from'] <= $date && $vacation['vacation_to'] >= $date) return true;
        }
        return false;
    }

    /**
     * Проверка является ли день праздником
     *
     * @param array $holidays
     * @param \Carbon\Carbon $date
     *
     * @return bool
     */
    private function isHoliday(array $holidays, Carbon $date): bool
    {
        return in_array($date, $holidays);
    }

    /**
     * Получить интервалы рабочего времени
     *
     * @param \Carbon\CarbonPeriod $businessDaysPeriod
     * @param array $timeRanges
     * @return array
     */
    private function getWorkingTimeIntervals(CarbonPeriod $businessDaysPeriod, array $timeRanges): array
    {
        foreach ($businessDaysPeriod as $date) {
            foreach ($timeRanges as $time) {
                $workingTimeIntervals[] = [
                    'start' => Carbon::create($date->format('Y-m-d') . ' ' . $time['start']),
                    'end' => Carbon::create($date->format('Y-m-d') . ' ' . $time['end'])
                ];
            }
        }

        return $workingTimeIntervals;
    }

    /**
     * Удалить время событий из рабочего графика
     *
     * @param array $workingTimeIntervals
     * @param array $events
     * @return array
     */
    private function removeEventsFromWorkingTime(array $workingTimeIntervals, array $events): array
    {
        foreach ($workingTimeIntervals as $n => $interval) {
            foreach ($events as $event) {
                $period = CarbonPeriod::create($interval['start'], $interval['end']);
                if ($period->overlaps($event['start_date'], $event['end_date'])) {
                    if ($interval['start'] <= $event['start_date'] && $interval['end'] <= $event['end_date']) {
                        $workingTimeIntervals[$n]['end'] = $event['start_date'];
                    } elseif ($interval['start'] >= $event['start_date'] && $interval['end'] >= $event['end_date']) {
                        $workingTimeIntervals[$n]['start'] = $event['end_date'];
                    } elseif ($interval['start'] <= $event['start_date'] && $interval['end'] >= $event['end_date']) {
                        $workingTimeIntervals[$n]['start'] = $event['start_date'];
                        $workingTimeIntervals[$n]['end'] = $event['end_date'];
                    } else {
                        unset($workingTimeIntervals[$n]);
                    }
                }
            }
        }

        return $workingTimeIntervals;
    }

    /**
     * Сгенерировать массив для JSON ответа
     *
     * @param array $workingTimeIntervals
     * @return array
     */
    private function generateOutputData(array $workingTimeIntervals): array
    {
        foreach ($workingTimeIntervals as $interval) {
            $data['schedule'][$interval['start']->format('Y-m-d')]['day'] = $interval['start']->format('Y-m-d');
            $data['schedule'][$interval['start']->format('Y-m-d')]['timeRange'][] = [
                'start' => $interval['start']->format('H:i'),
                'end' => $interval['end']->format('H:i')
            ];
        }
        $data['schedule'] = array_values($data['schedule']);

        return $data;
    }
}
