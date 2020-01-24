<?php

namespace App\Repositories;

use Carbon\Carbon;
use GuzzleHttp\Client;

class GoogleCalendarHolidaysRepository
{
    /**
     * Ключ к Google Calendar API
     *
     * @var string
     */
    protected $API_KEY;

    /**
     * Http-клиент Guzzle
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Список праздников
     *
     * @var array
     */
    protected $holidays = [];

    /**
     * Конструктор класса GoogleCalendarHolidaysRepository
     *
     * @param GuzzleHttp\Client $httpClient
     *
     * @return void
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
     */
    public function all(): array
    {
        $response = $this->httpClient->get(
                'https://www.googleapis.com/calendar/v3/calendars/ru.russian%23holiday%40group.v.calendar.google.com/events?key=' . $this->API_KEY
            )
            ->getBody()
            ->getContents();

        $holidaysList = json_decode($response)->items;
        foreach ($holidaysList as $day) {
            $this->holidays[] = Carbon::create($day->start->date);
        }

        return $this->holidays;
    }
}
