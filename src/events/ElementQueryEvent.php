<?php

namespace pdaleramirez\superfilter\events;

use pdaleramirez\superfilter\base\SearchType;
use yii\base\Event;

class ElementQueryEvent extends Event
{
    public $query = null;
    /** @var SearchType $searchType */
	public $searchType = null;
}
