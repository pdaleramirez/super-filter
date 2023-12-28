<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\elements\db\ElementQuery;
use pdaleramirez\superfilter\base\PlainSearchTrait;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;
use stdClass;
use yii\db\QueryInterface;

class Title extends SearchField
{
    use PlainSearchTrait;

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
            'value' => "",
        ];
    }

    public function getShortName(): string
    {
        return "PlainText";
    }
}
