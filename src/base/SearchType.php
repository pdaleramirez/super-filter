<?php

namespace pdaleramirez\superfilter\base;

use craft\base\Component;
use pdaleramirez\superfilter\contracts\SearchTypeInterface;
use pdaleramirez\superfilter\elements\SetupSearch;

abstract class SearchType extends Component implements SearchTypeInterface
{
    /**
     * @var SetupSearch
     */
    protected $element = null;

    public function setElement(SetupSearch $setupSearch)
    {
        $this->element = $setupSearch;
    }

    public function getContainer()
    {
        return null;
    }
}
