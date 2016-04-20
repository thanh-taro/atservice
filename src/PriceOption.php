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
        Service::updatePriceOption($this->id, $params);
        foreach ($params as $key => $val) {
            $attr = lcfirst($key);
            if (isset($this->$attr)) {
                $this->$attr = $val;
            }
        }
        return true;
    }

    public function toArray($atFormat = false)
    {
        $arr = parent::toArray($atFormat);
        if ($atFormat) {
            $arr['AgentTicketCharge'] = doubleval($arr['AgentTicketCharge']);
            $arr['InternationalTicketCharge'] = doubleval($arr['InternationalTicketCharge']);
            $arr['VATRate'] = doubleval($arr['VATRate']);
            $arr['UsdToVndRate'] = doubleval($arr['UsdToVndRate']);
        } else {
            $arr['agentTicketCharge'] = doubleval($arr['agentTicketCharge']);
            $arr['internationalTicketCharge'] = doubleval($arr['internationalTicketCharge']);
            $arr['vATRate'] = doubleval($arr['vATRate']);
            $arr['usdToVndRate'] = doubleval($arr['usdToVndRate']);
        }
        return $arr;
    }
}
