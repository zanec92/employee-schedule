<?php

namespace App\Repositories;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarEventsRepository
{
    /**
     * Менеджер событий Google Calendar
     *
     * @var Spatie\GoogleCalendar\Event
     */
    protected $event;

    /**
     * Даты событий
     *
     * @var array
     */
    protected $eventsDates = [];

    /**
     * Конструктор класса GoogleCalendarEventsRepository
     *
     * @param Spatie\GoogleCalendar\Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function between($start_date, $end_date)
    {
        $events = $this->event->get(Carbon::create($start_date), Carbon::create($end_date));
        foreach ($events as $n => $event) {
            $this->eventsDates[$n]['start_date'] = $event->startDateTime;
            $this->eventsDates[$n]['end_date'] = $event->endDateTime;
        }
        return $this->eventsDates;
    }
}
