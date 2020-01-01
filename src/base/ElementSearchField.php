<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\ElementInterface;
use craft\db\mysql\QueryBuilder;
use craft\elements\Category;
use craft\elements\db\ElementQuery;
use craft\fields\Categories as CraftCategories;
use craft\helpers\ElementHelper;
use function GuzzleHttp\debug_resource;
use yii\db\QueryInterface;

abstract class ElementSearchField extends SearchField
{
    /**
     * @return string|ElementInterface
     */
    abstract protected function elementType();

    /**
     * @param QueryInterface $query
     * @param $value
     * @return void|QueryInterface
     */
    public function getQueryParams(QueryInterface $query, $value)
    {
        return $query;
    }


    public function getRelated($value)
    {
        $elementType = static::elementType();

        $elements = $elementType::findAll($value);

        return $elements;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        /**
         * @var $object CraftCategories
         */
        $object = $this->object;
        $source = $object->source;

        $elementType = static::elementType();
        $find = ElementHelper::findSource($elementType, $source);
        $query = $elementType::find();

        $criteria = $find['criteria'] ?? null;
        if ($criteria) {
            Craft::configure($query, $criteria);
        }

        $elements = $query->all();

        return Craft::$app->getView()->renderTemplate($template . '/fields/elementcheckbox', [
            'field' => $this,
            'elements' => $elements
        ]);
    }
}
