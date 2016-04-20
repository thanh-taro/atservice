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
    public $priceSummary;

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        $this->price = doubleval($this->price);
        $this->totalPrice = doubleval($this->totalPrice);
    }

    public function toArray($atFormat = false)
    {
        $arr = parent::toArray($atFormat);
        if ($atFormat && !empty($this->priceSummary)) {
            $priceSummary = [];
            foreach ($this->priceSummary as $key => $val) {
                $priceSummary[ucfirst($key)] = $val;
            }
            $arr['PriceSummary'] = $priceSummary;
        }
        return $arr;
    }
}
