<?php

namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\events\RegisterSearchTypeEvent;

class SearchTypes extends Component
{
    const EVENT_REGISTER_SEARCH_TYPES = 'defineSuperFilterSearchTypes';

    /**
     * @return array|SearchType[]
     */
    public function getSearchTypes()
    {
        $event = new RegisterSearchTypeEvent([
            'searchTypes' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_SEARCH_TYPES, $event);

        return $event->searchTypes;
    }
}
