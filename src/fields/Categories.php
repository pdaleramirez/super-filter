<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\base\ElementInterface;
use craft\elements\Category;
use craft\fields\Categories as CraftCategories;
use pdaleramirez\superfilter\base\ElementSearchField;

class Categories extends ElementSearchField
{
    public function fieldType()
    {
        return CraftCategories::class;
    }

    /**
     * @return string|ElementInterface
     */
    protected function elementType()
    {
        return Category::class;
    }
}
