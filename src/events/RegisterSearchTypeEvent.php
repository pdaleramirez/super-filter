<?php

namespace pdaleramirez\superfilter\events;

use yii\base\Event;

class RegisterSearchTypeEvent extends Event
{
    public $searchTypes = [];
}
