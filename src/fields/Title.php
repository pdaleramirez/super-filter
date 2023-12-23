<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\elements\db\ElementQuery;
use craft\fields\PlainText as PlainTextCraft;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;
use phpDocumentor\Reflection\Types\Static_;
use stdClass;
use yii\db\QueryInterface;

class Title extends SearchField
{
    public $initValue = '';
    public $object;
    public $custom = true;
    
    public function __construct()
    {
        $this->object = new stdClass;
        $this->object->name   = 'Title';
        $this->object->handle = 'title';
    }

    public function getKey()
    {
        return 'title';
    }

    public function fieldType()
    {
        return static::class;
    }

    /**
     * @param QueryInterface|ElementQuery $query
     * @param $value
     * @return ElementQuery
     */
    public function getQueryParams(QueryInterface $query, $value)
    {
        return $query->search("title:*$value*");
    }

    public function getSearchParams($value)
    {
        $handle = $this->object->handle;

        return "$handle::*$value*";
    }

    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/plaintext');

        return Craft::$app->getView()->renderTemplate($template,
            [
               'field' => $this
            ]);
    }

    public function getSearchFieldsInfo(): array
    {
        return [
            'value' => ""
        ];
    }

    public function getShortName(): string
    {
        return "Title";
    }
}
