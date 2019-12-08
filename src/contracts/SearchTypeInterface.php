<?php

namespace pdaleramirez\superfilter\contracts;

use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;

interface SearchTypeInterface
{
    /**
     * @return ElementInterface
     */
    public function getElement();
    public function getFields();
    public function getSorts();

    /**
     * @return ElementQuery
     */
    public function getQuery();
}
