<?php

namespace pdaleramirez\superfilter\events;

use yii\base\Event;

class RegisterSearchFieldTypeEvent extends Event
{
    public $searchFieldTypes = [];
}
