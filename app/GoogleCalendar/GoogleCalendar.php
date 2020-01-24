<?php

namespace App\GoogleCalendar;

use Carbon\Carbon;
use GuzzleHttp\Client;

class GoogleCalendar
{
    /**
     * Ключ к Google Calendar API
     *
     * @var string
     *
     */
    protected $API_KEY;

    /**
     * Http-клиент Guzzle
     *
     * @var GuzzleHttp\Client
     *
     */
    protected $httpClient;

    /**
     * Конструктор класса ARScheduleRepository
     *
     * @return void
     *
     */
    public function __construct(Client $httpClient)
    {
        $this->API_KEY = env('GOOGLE_CALENDAR_API_KEY');
        $this->httpClient = $httpClient;
    }

    /**
     * Получение списка праздников из Google Calendar
     *
     * @return array
     *
     */
    public function getHolidays()
    {
        $response = $this->httpClient->get(
                'https://www.googleapis.com/calendar/v3/calendars/ru.russian%23holiday%40group.v.calendar.google.com/events?key=' . $this->API_KEY
            )
            ->getBody()
            ->getContents();

        $holidays = json_decode($response)->items;
        foreach ($holidays as $day) {
            $holidaysDate[] = Carbon::create($day->start->date);
        }

        return $holidaysDate;
    }
}
