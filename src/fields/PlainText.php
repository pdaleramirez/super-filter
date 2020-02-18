<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;

class PlainText extends SearchField
{
    public $initValue = '';
    public function fieldType()
    {
        return PlainTextCraft::class;
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
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/plaintext');

        return Craft::$app->getView()->renderTemplate($template,
            [
               'field' => $this
            ]);
    }
}
