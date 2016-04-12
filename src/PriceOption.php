<?php

namespace atservice;

class PriceOption extends Object
{

    public $id;
    public $userId;
    public $airlineCode;
    public $agentTicketCharge;
    public $internationalTicketCharge;
    public $vATRate;
    public $usdToVndRate;

    public function update($params)
    {
        Service::updatePriceOption($params);
        foreach ($params as $key => $val) {
            $attr = lcfirst($key);
            if (isset($this->$attr)) {
                $this->$attr = $val;
            }
        }
        return true;
    }

    public function toArray()
    {
        $arr = parent::toArray();
        $arr['agentTicketCharge'] = doubleval($arr['agentTicketCharge']);
        $arr['internationalTicketCharge'] = doubleval($arr['internationalTicketCharge']);
        $arr['vATRate'] = doubleval($arr['vATRate']);
        $arr['usdToVndRate'] = doubleval($arr['usdToVndRate']);
        return $arr;
    }

}
