<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\elements\db\ElementQuery;
use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;
use phpDocumentor\Reflection\Types\Static_;
use stdClass;
use yii\db\QueryInterface;

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

    /**
     * @param QueryInterface|ElementQuery $query
     * @param $value
     */
    public function getQueryParams(QueryInterface $query, $value)
    {
        $query->search("title:*$value*");
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
