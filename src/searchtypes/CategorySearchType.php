<?php

namespace pdaleramirez\superfilter\searchtypes;

use craft\elements\Category;
use pdaleramirez\superfilter\base\SearchType;

class CategorySearchType extends SearchType
{
    public function getElement()
    {
        return Category::class;
    }
}
