<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\db\ElementQuery;
use yii\db\QueryInterface;

abstract class SearchField
{
    protected $config;
    protected $value;
    public $initValue = [];
    /**
     * @var $object Field
     */
    protected $object;
    public $custom = false;
    
    public function getKey()
    {
        return static::class;
    }
    
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setObject(FieldInterface $object)
    {
        $this->object = $object;
    }

    /**
     * @return FieldInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    abstract function fieldType();

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    abstract function getHtml();

    public function getQueryParams(QueryInterface $query, $value)
    {
        Craft::configure($query, [
            $this->object->handle => $value
        ]);
    }

    public function getRelated($value)
    {
        return null;
    }

    public function getSearchParams($value)
    {
        return null;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getFieldValue($element)
    {
        return $element->value;
    }
}
