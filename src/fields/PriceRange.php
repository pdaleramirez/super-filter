<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\commerce\elements\db\ProductQuery;
use craft\elements\db\ElementQuery;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;
use stdClass;
use yii\db\QueryInterface;

class PriceRange extends SearchField
{
    public $initValue = '';
    public $object;
    public $custom = true;
    
    public function __construct()
    {
        $this->object = new stdClass;
        $this->object->name   = 'Price Range';
        $this->object->handle = 'range';
    }

    public function getKey()
    {
        return 'range';
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
        /**
         * @var ProductQuery $query
         */
        return $query->andWhere(['between', 'defaultPrice', 30, 80]);
    }

    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/plaintext');

        return Craft::$app->getView()->renderTemplate($template,
            [
               'field' => $this
            ]);
    }
}
