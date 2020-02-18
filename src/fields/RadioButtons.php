<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\RadioButtons as RadioButtonsField;
use pdaleramirez\superfilter\base\OptionSearchField;
use pdaleramirez\superfilter\SuperFilter;

class RadioButtons extends OptionSearchField
{
    public function fieldType()
    {
        return RadioButtonsField::class;
    }

    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/radiobuttons');

        return Craft::$app->getView()->renderTemplate($template,
            [
                'field' => $this,
                'options' => $this->getOptions()
            ]);
    }
}
