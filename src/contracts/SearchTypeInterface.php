<?php

namespace pdaleramirez\superfilter\contracts;

use craft\base\ElementInterface;

interface SearchTypeInterface
{
    /**
     * @return ElementInterface
     */
    public function getElement();
    public function getFields();
    public function getSorts();
}
