<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\Dropdown as DropdownCraft;
use pdaleramirez\superfilter\base\OptionSearchField;

class Dropdown extends OptionSearchField
{
    public $initValue = '';
    public function fieldType()
    {
        return DropdownCraft::class;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        return Craft::$app->getView()->renderTemplate($template . '/fields/dropdown',
            [
               'field' => $this,
               'options' => $this->getOptions()
            ]);
    }
}
