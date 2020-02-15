<?php

namespace pdaleramirez\superfilter\fields;

use craft\base\ElementInterface;
use craft\elements\Tag;
use craft\fields\Tags as TagsField;
use pdaleramirez\superfilter\base\ElementSearchField;

class Tags extends ElementSearchField
{
    public function fieldType()
    {
        return TagsField::class;
    }

    /**
     * @return string|ElementInterface
     */
    protected function elementType()
    {
        return Tag::class;
    }
}
