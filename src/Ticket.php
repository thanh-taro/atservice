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
        $priceSummary = [];
        foreach ($opt['PriceSummaries'] as $val) {
            $item = [
                'price' => intval($val['Price']),
                'quantity' => intval($val('Quantity')),
                'total' => intval($val['Total']),
            ];
            if ($val['Code'] === 'NET') {
                $type = 'net';
            } else {
                $type = 'tax';
            }
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
                        'price' => $adultPrice,
                        'quantities' => $defaultTicketOption->priceSummary['net']['adult']['quantity'],
                        'total' => $adultPrice * $defaultTicketOption->priceSummary['net']['adult']['quantity'],
                    ];
                    if (!empty($defaultTicketOption->priceSummary['net']['child'])) {
                        $childPrice = ceil($priceSummary['net']['adult']['price'] * 75/100);
                        $priceSummary['net']['child'] = [
                            'price' => $childPrice,
                            'quantities' => $defaultTicketOption->priceSummary['net']['child']['quantity'],
                            'total' => $childPrice * $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                        ];
                    }
                    if (!empty($defaultTicketOption->priceSummary['net']['infant'])) {
                        $infantPrice = ceil($priceSummary['net']['adult']['price'] / 10);
                        $priceSummary['net']['infant'] = [
                            'price' => $infantPrice,
                            'quantities' => $defaultTicketOption->priceSummary['net']['infant']['quantity'],
                            'total' => $infantPrice * $defaultTicketOption->priceSummary['tax']['infant']['quantity'],
                        ];
                    }
                }
                if (!empty($defaultTicketOption->priceSummary['tax']['adult']) && !empty($priceSummary['net']['child'])) {
                    $adultTax = $defaultTicketOption->priceSummary['tax']['adult']['price'] - $defaultTicketOption->price/10 + intval($ticketOptionOpt['Price'])/10;
                    $priceSummary['tax']['adult'] = [
                        'price' => $adultTax,
                        'quantities' => $defaultTicketOption->priceSummary['tax']['adult']['quantity'],
                        'total' => $adultTax * $defaultTicketOption->priceSummary['tax']['adult']['quantity'],
                    ];
                    if (!empty($defaultTicketOption->priceSummary['tax']['child'])) {
                        $childTax = $defaultTicketOption->priceSummary['tax']['child']['price'] - $defaultTicketOption->priceSummary['net']['child']['price']/10 + $priceSummary['net']['child']['price'];
                        $priceSummary['tax']['child'] = [
                            'price' => $childTax,
                            'quantities' => $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                            'total' => $childTax * $defaultTicketOption->priceSummary['tax']['child']['quantity'],
                        ];
                    }
                    if (!empty($defaultTicketOption->priceSummary['tax']['infant'])) {
                        $infantTax = $defaultTicketOption->priceSummary['tax']['infant']['price'] - $defaultTicketOption->priceSummary['net']['infant']['price']/10 + $priceSummary['net']['infant']['price'];
                        $priceSummary['tax']['infant'] = [
                            'price' => $infantTax,
                            'quantities' => $defaultTicketOption->priceSummary['tax']['infant']['quantity'],
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
    }
}
