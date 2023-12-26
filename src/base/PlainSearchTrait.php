<?php

namespace pdaleramirez\superfilter\base;

trait PlainSearchTrait
{
    public function getSearchParams($value)
    {
        $handle = $this->object->handle;

        return "$handle::'*$value*'";
    }
}