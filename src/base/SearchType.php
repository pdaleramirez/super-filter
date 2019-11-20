<?php

namespace pdaleramirez\superfilter\base;

use craft\base\Component;
use pdaleramirez\superfilter\elements\SetupSearch;

abstract class SearchType extends Component
{
    /**
     * @var SetupSearch
     */
    protected $element = null;

    public function setElement($id)
    {
        $this->element = SetupSearch::findOne($id);
    }

    abstract function getElement();

    public function getContainer()
    {
        return null;
    }

    public function getFields($selected)
    {
        return null;
    }
}
