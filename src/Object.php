<?php

namespace atservice;

class Object
{
    public function __construct($opt = [])
    {
        foreach ($opt as $key => $val) {
            $attr = lcfirst($key);
            if (property_exists($this, $attr)) {
                $this->$attr = $val;
            }
        }
    }

    public function toArray($atFormat = false)
    {
        $attrs = get_object_vars($this);
        if ($atFormat) {
            $atRes = [];
            foreach ($attrs as $key => $val) {
                $atRes[ucfirst($key)] = $val;
            }
            $attrs = $atRes;
        }
        return empty($attrs) ? [] : $attrs;
    }
}
