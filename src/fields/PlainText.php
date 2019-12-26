<?php

namespace pdaleramirez\superfilter\fields;

use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;

class PlainText extends SearchField
{
    public function fieldType()
    {
        return PlainTextCraft::class;
    }
}
