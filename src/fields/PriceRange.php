<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\commerce\elements\db\ProductQuery;
use craft\elements\db\ElementQuery;
use craft\helpers\Json;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\SuperFilter;
use stdClass;
use yii\db\QueryInterface;

class PriceRange extends SearchField
{
    public $initValue = ['min' => '', 'max' => ''];
    public $object;
    public $custom = true;
    public const KEY = 'superFilterPriceRange';
    
    public function __construct()
    {
        $this->object = new stdClass;
        $this->object->name = 'Price Range';
        $this->object->handle = static::KEY;
    }

    public function getKey()
    {
        return static::KEY;
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
        $min = $value['min'] ?: null;
        $max = $value['max'] ?: null;
        /**
         * @var ProductQuery $query
         */
        
        if ($min !== null && $max !== null) {
            return $query->andWhere(['between', 'defaultPrice', $min, $max]);
        }
        
        if ($min !== null && $max === null) {
            return $query->andWhere(['>=', 'defaultPrice', $min]);
        }        
        
        if ($min === null && $max !== null) {
            return $query->andWhere(['<=', 'defaultPrice', $max]);
        }
    }

    public function getHtml()
    {
        $template = SuperFilter::$app->searchTypes->getTemplate('fields/pricerange');

        return Craft::$app->getView()->renderTemplate($template,
            [
                'field' => $this
            ]);
    }

    public function getSearchFieldsInfo(): array
    {
        $value['value'] = [
            'min' => null,
            'max' => null,
        ];

        return $value;
    }

    public function getShortName(): string
    {
        return "PriceRange";
    }
}
