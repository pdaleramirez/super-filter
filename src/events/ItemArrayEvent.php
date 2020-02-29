<?php

namespace pdaleramirez\superfilter\events;

use yii\base\Event;

class ItemArrayEvent extends Event
{
    public $item      = null;
    public $element   = null;
    public $searchType = null;
}
