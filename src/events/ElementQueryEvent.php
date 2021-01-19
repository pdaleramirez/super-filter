<?php

namespace pdaleramirez\superfilter\events;

use yii\base\Event;

class ElementQueryEvent extends Event
{
    public $query = null;
	public $searchType = null;
}
