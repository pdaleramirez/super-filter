<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\fields\Categories as CraftCategories;
use craft\helpers\ElementHelper;
use pdaleramirez\superfilter\SuperFilter;
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

    public function getElementQuery()
    {
        /**
         * @var $object CraftCategories
         */
        $object = $this->object;
        $sources = $object->sources;

        $elementType = static::elementType();
        $query = $elementType::find();

        if (is_string($sources) && $sources === '*') {
            $sources = [$sources];
        }

        if (is_array($sources)) {
            foreach ($sources as $source) {
                $find = ElementHelper::findSource($elementType, $source);

                $criteria = $find['criteria'] ?? null;
                if ($criteria) {
                    Craft::configure($query, $criteria);
                }
            }
        }

        return $query;
    }

    public function getElements()
    {
        /**
         * @var $elements Element[]
         */
        $elements = $this->getElementQuery()->all();

        $values = [];

        if ($elements) {
            foreach ($elements as $key => $element) {
                $values[$key]['title']   = $element->title;
                $values[$key]['handle']  = $element->slug;
                $values[$key]['id']      = $element->id;

                $selected = false;

                if (is_array($this->value)) {
                    if (in_array($element->id, $this->value)) {
                        $selected = true;
                    }
                } elseif ($this->value === $element->id) {
                    $selected = true;
                }

                $values[$key]['selected'] = $selected;
            }
        }

        return $values;
    }

    public function getHtml()
    {
        $elements = $this->getElements();

        $template = SuperFilter::$app->searchTypes->getTemplate('fields/elementcheckbox');

        return Craft::$app->getView()->renderTemplate($template, [
            'field' => $this,
            'elements' => $elements
        ]);
    }

    public function getFieldValue($element)
    {
        return $element->all();
    }
}
