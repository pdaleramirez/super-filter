<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\Number as NumberCraft;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;

class Number extends SearchField
{
    public $initValue = '';
    public function fieldType()
    {
        return NumberCraft::class;
    }

    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/number');

        return Craft::$app->getView()->renderTemplate($template, [
            'field' => $this
        ]);
    }
}
