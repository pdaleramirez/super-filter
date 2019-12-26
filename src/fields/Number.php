<?php

namespace pdaleramirez\superfilter\fields;

use craft\fields\Number as NumberCraft;
use pdaleramirez\superfilter\base\SearchField;

class Number extends SearchField
{
    public function fieldType()
    {
        return NumberCraft::class;
    }
}
