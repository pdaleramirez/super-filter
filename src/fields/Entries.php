<?php

namespace pdaleramirez\superfilter\fields;

use craft\base\ElementInterface;
use craft\elements\Entry;
use craft\fields\Entries as CraftEntries;
use pdaleramirez\superfilter\base\ElementSearchField;

class Entries extends ElementSearchField
{
    public function fieldType()
    {
        return CraftEntries::class;
    }

    /**
     * @return string|ElementInterface
     */
    protected function elementType()
    {
        return Entry::class;
    }
}
