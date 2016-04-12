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

    public function toArray()
    {
        $attrs = get_object_vars($this);
        return empty($attrs) ? [] : $attrs;
    }
}
