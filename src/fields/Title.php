<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;
use phpDocumentor\Reflection\Types\Static_;
use stdClass;

class Title extends SearchField
{
    public $object;
    public function __construct()
    {
        $this->object = new stdClass;
        $this->object->name   = 'Title';
        $this->object->handle = 'title';
    }

    public function fieldType()
    {
        return static::class;
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
