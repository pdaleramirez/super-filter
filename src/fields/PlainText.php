<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;

class PlainText extends SearchField
{
    public function fieldType()
    {
        return PlainTextCraft::class;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        return Craft::$app->getView()->renderTemplate($template . '/fields/plaintext',
            [
               'field' => $this
            ]);
    }
}
