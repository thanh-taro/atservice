<?php

namespace atservice;

class TicketOption extends Object
{

    public $roundTrip;
    public $fromPlace;
    public $toPlace;
    public $departDate;
    public $returnDate;
    public $currencyType = 'VND';
    public $adult = 0;
    public $child = 0;
    public $infant = 0;
    public $sources;
    public $flightType = 'DirectAndContinue';

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        if (empty($this->returnDate)) {
            $this->returnDate = $this->departDate;
        }
        $this->returnDate .= 'T00:00:00.000';
        $this->departDate .= 'T00:00:00.000';
        $this->adult = intval($this->adult);
        $this->child = intval($this->child);
        $this->infant = intval($this->infant);
        $this->roundTrip = boolval($this->roundTrip);
    }
}
