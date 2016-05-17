<?php

namespace atservice;

class Ticket extends Object
{

    public $id;
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
    public $flightDetails;
    public $flightNumbers;
    public $ticketOptions;
    public $priceFrom;

    public function __construct($opt = [])
    {
        parent::__construct($opt);
        $priceSummary = [];
        foreach ($opt['PriceSummaries'] as $val) {
            $item = [
                'description' => $val['Description'],
                'price' => intval($val['Price']),
                'quantity' => intval($val['Quantity']),
                'total' => intval($val['Total']),
            ];
            if ($val['Code'] === 'NET') {
                $type = 'net';
            } else {
                $type = 'tax';
            }
            $personType = 'adult';
            switch ($val['PassengerType']) {
                case 'ADT':
                    $personType = 'adult';
                break;
                case 'CHD':
                    $personType = 'child';
                break;
                case 'INF':
                    $personType = 'infant';
                break;
            }
            $priceSummary[$type][$personType] = $item;
        }
        $defaultTicketOption = new TicketOption($opt);
        $defaultTicketOption->priceSummary = $priceSummary;
        $ticketOptions = [$defaultTicketOption];
        if (!empty($this->ticketOptions)) {
            foreach ($this->ticketOptions as $ticketOptionOpt) {
                $ticketOption = new TicketOption($ticketOptionOpt);
                $priceSummary = [];
                if (!empty($defaultTicketOption->priceSummary['net']['adult'])) {
                    $adultPrice = intval($ticketOptionOpt['Price']);
                    $priceSummary['net']['adult'] = [
                        'description' => $defaultTicketOption->priceSummary['net']['adult']['description'],
                        'price' => $adultPrice,
                        'quantity' => $defaultTicketOption->priceSummary['net']['adult']['quantity'],
                        'total' => $adultPrice * $defaultTicketOption->priceSummary['net']['adult']['quantity'],
                    ];
                    if (!empty($defaultTicketOption->priceSummary['net']['child'])) {
                        // Ceil to thoundsand
                        $childPrice = ceil(ceil($priceSummary['net']['adult']['price'] * 75 / 100) / 1000) * 1000;
                        $priceSummary['net']['child'] = [
                            'description' => $defaultTicketOption->priceSummary['net']['child']['description'],
                            'price' => $childPrice,
                            'quantity' => $defaultTicketOption->priceSummary['net']['child']['quantity'],
                            'total' => $childPrice * $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                        ];
                    }
                    if (!empty($defaultTicketOption->priceSummary['net']['infant'])) {
                        // Ceil to thoundsand
                        $infantPrice = ceil(ceil($priceSummary['net']['adult']['price'] / 10) / 1000) * 1000;
                        $priceSummary['net']['infant'] = [
                            'description' => $defaultTicketOption->priceSummary['net']['infant']['description'],
                            'price' => $infantPrice,
                            'quantity' => $defaultTicketOption->priceSummary['net']['infant']['quantity'],
                            'total' => $infantPrice * $defaultTicketOption->priceSummary['tax']['infant']['quantity'],
                        ];
                    }
                }
                if (!empty($defaultTicketOption->priceSummary['tax']['adult'])) {
                    $adultTax = floor(($defaultTicketOption->priceSummary['tax']['adult']['price'] - $defaultTicketOption->price / 10 + intval($ticketOptionOpt['Price']) / 10) / 1000) * 1000;
                    $priceSummary['tax']['adult'] = [
                        'description' => $defaultTicketOption->priceSummary['tax']['adult']['description'],
                        'price' => $adultTax,
                        'quantity' => $defaultTicketOption->priceSummary['tax']['adult']['quantity'],
                        'total' => $adultTax * $defaultTicketOption->priceSummary['tax']['adult']['quantity'],
                    ];
                    if (!empty($defaultTicketOption->priceSummary['tax']['child'])) {
                        $childTax = floor(($defaultTicketOption->priceSummary['tax']['child']['price'] - $defaultTicketOption->priceSummary['net']['child']['price'] / 10 + $priceSummary['net']['child']['price'] / 10) / 1000) * 1000;
                        $priceSummary['tax']['child'] = [
                            'description' => $defaultTicketOption->priceSummary['tax']['child']['description'],
                            'price' => $childTax,
                            'quantity' => $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                            'total' => $childTax * $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                        ];
                    }
                    if (!empty($defaultTicketOption->priceSummary['tax']['infant'])) {
                        $infantTax = floor(($defaultTicketOption->priceSummary['tax']['infant']['price'] - $defaultTicketOption->priceSummary['net']['infant']['price'] / 10 + $priceSummary['net']['infant']['price'] / 10) / 1000) * 1000;
                        $priceSummary['tax']['infant'] = [
                            'description' => $defaultTicketOption->priceSummary['tax']['infant']['description'],
                            'price' => $infantTax,
                            'quantity' => $defaultTicketOption->priceSummary['tax']['infant']['quantity'],
                            'total' => $infantTax * $defaultTicketOption->priceSummary['tax']['infant']['quantity'],
                        ];
                    }
                }
                $ticketOption->priceSummary = $priceSummary;
                $ticketOptions[] = $ticketOption;
            }
        }
        usort($ticketOptions, function ($a, $b) {
            return $a->price > $b->price;
        });
        $this->ticketOptions = $ticketOptions;
        $this->priceFrom = $ticketOptions[0]->price;
        $this->flightNumbers = [];
        $this->flightDetails = [];
        if (!empty($opt['Details'])) {
            foreach ($opt['Details'] as $flightDetailOpt) {
                $flightDetail = new FlightDetail($flightDetailOpt);
                $this->flightDetails[] = $flightDetail;
                $this->flightNumbers[] = $flightDetail->flightNumber;
            }
        }
    }

    public function toArray($atFormat = false)
    {
        $arr = parent::toArray($atFormat);
        if ($atFormat) {
            foreach ($arr['TicketOptions'] as $key => $ticketOption) {
                $arr['TicketOptions'][$key] = $ticketOption->toArray($atFormat);
            }
            foreach ($arr['FlightDetails'] as $key => $flightDetail) {
                $arr['FlightDetails'][$key] = $flightDetail->toArray($atFormat);
            }
        } else {
            foreach ($arr['ticketOptions'] as $key => $ticketOption) {
                $arr['ticketOptions'][$key] = $ticketOption->toArray($atFormat);
            }
            foreach ($arr['flightDetails'] as $key => $flightDetail) {
                $arr['flightDetails'][$key] = $flightDetail->toArray($atFormat);
            }
        }
        return $arr;
    }
}
