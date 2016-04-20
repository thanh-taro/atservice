<?php

namespace atservice;

class Ticket extends Object
{
    public $flightNumber;
    public $airline;
    public $airlineCode;
    public $departTime;
    public $landingTime;
    public $flightDuration;
    public $description;
    public $fromPlaceId;
    public $fromPlace;
    public $toPlaceId;
    public $toPlace;
    public $source;
    public $sourceGroup;
    public $seatAvailable;
    public $ticketOptions;
    public $priceFrom;

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        $ticketOptions = [];
        if (!empty($this->ticketOptions)) {
            $ticketOptions = [];
            foreach ($this->ticketOptions as $ticketOption) {
                $ticketOptions[] = new TicketOption($ticketOption);
            }
        }
        $ticketOptions[] = new TicketOption($opt);
        usort($ticketOptions, function ($a, $b) {
            return $a->price > $b->price;
        });
        $this->ticketOptions = $ticketOptions;
        $this->priceFrom = $ticketOptions[0]->price;
    }
}
