<?php

namespace App\GoogleCalendar;

use Carbon\Carbon;
use GuzzleHttp\Client;

class GoogleCalendar
{

    protected $API_KEY;

    protected $httpClient;

    public function __construct()
    {
        $this->API_KEY = env('GOOGLE_CALENDAR_API_KEY');
        $this->httpClient = new Client();
    }

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