<?php

namespace atservice;

class FlightDetail extends Object
{
    public $flightNumber;
    public $from;
    public $to;
    public $airline;
    public $airlineCode;
    public $flightDuration;
    public $departTime;
    public $landingTime;

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        $arr = explode(':', $this->flightDuration);
        if (count($arr) > 1) {
            $flightDuration = [];
            $this->flightDuration = '';
            if (!empty(intval($arr[0]))) {
                $flightDuration[] = "{$arr[0]} giờ";
            }
            if (!empty(intval($arr[1]))) {
                $flightDuration[] = "{$arr[0]} phút";
            }
            $this->flightDuration = implode(' ', $flightDuration);
        }
    }
}
