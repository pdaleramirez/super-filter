<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\Number as NumberCraft;
use pdaleramirez\superfilter\base\SearchField;

class Number extends SearchField
{
    public $initValue = '';
    public function fieldType()
    {
        return NumberCraft::class;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        return Craft::$app->getView()->renderTemplate($template . '/fields/number', [
            'field' => $this
        ]);
    }
}
