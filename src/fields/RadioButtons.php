<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\RadioButtons as RadioButtonsField;
use pdaleramirez\superfilter\base\OptionSearchField;

class RadioButtons extends OptionSearchField
{
    public function fieldType()
    {
        return RadioButtonsField::class;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        return Craft::$app->getView()->renderTemplate($template . '/fields/radiobuttons',
            [
                'field' => $this,
                'options' => $this->getOptions()
            ]);
    }
}
