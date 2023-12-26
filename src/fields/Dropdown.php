<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\Dropdown as DropdownCraft;
use pdaleramirez\superfilter\base\OptionSearchField;
use pdaleramirez\superfilter\SuperFilter;

class Dropdown extends OptionSearchField
{
    public $initValue = '';
    public function fieldType()
    {
        return DropdownCraft::class;
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/dropdown');

        return Craft::$app->getView()->renderTemplate($template,
            [
               'field' => $this,
               'options' => $this->getOptions()
            ]);
    }
}
