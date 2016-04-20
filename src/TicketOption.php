<?php

namespace atservice;

class TicketOption extends Object
{

    public $id;
    public $ticketType;
    public $fareBasis;
    public $price;
    public $totalPrice;
    public $stops;
    public $priceSummaries;

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        $this->price = doubleval($this->price);
        $this->totalPrice = doubleval($this->totalPrice);
    }

}
