<?php

namespace pdaleramirez\superfilter\base;

use craft\base\Component;
use craft\elements\db\ElementQuery;
use pdaleramirez\superfilter\contracts\SearchTypeInterface;
use pdaleramirez\superfilter\elements\SetupSearch;

abstract class SearchType extends Component implements SearchTypeInterface
{
    /**
     * @var SetupSearch
     */
    protected $element = null;
    protected $options = null;
    protected $items = null;
    protected $sorts = null;
    protected $sortParam = null;
    protected $params = null;
    /**
     * @var $query ElementQuery
     */
    protected $query;

    public function setElement(SetupSearch $setupSearch)
    {
        $this->element = $setupSearch;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    public function setParams($params)
    {
        $this->params = $params;

        $sort = $params['sort'] ?? null;

        if ($sort) {
            $sort = explode('-', $sort);

            $this->sortParam['attribute'] = $sort[0];
            $this->sortParam['sort'] = $sort[1] === 'desc' ? SORT_DESC : SORT_ASC;
        }

    }

    public function getContainer()
    {
        return null;
    }
}
