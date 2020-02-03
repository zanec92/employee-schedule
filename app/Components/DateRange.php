<?php

namespace App\Components;

use Carbon\CarbonPeriod;

class DateRange
{
    public $start;

    public $end;

    protected $interval;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
        $this->interval = CarbonPeriod::create($start, $end);
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function isOverlap($interval)
    {
        return $this->interval->overlaps($interval->start, $interval->end);
    }

    public function startsBefore($date)
    {
        return $this->start <= $date->start;
    }
    
    public function startsAfter($date)
    {
        return $this->start >= $date->start;
    }

    public function endsBefore($date)
    {
        return $this->end <= $date->end;
    }

    public function endsAfter($date)
    {
        return $this->end >= $date->end;
    }

    public function toArray()
    {
        return [
            'start' => $this->start,
            'end' => $this->end
        ];
    }
}