<?php

namespace pdaleramirez\superfilter\base;

use craft\base\FieldInterface;

abstract class SearchField
{
    protected $config;
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
}
