<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;
use yii\db\QueryInterface;

abstract class SearchField
{
    protected $config;

    /**
     * @var $object Field
     */
    protected $object;

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
}
