<?php

namespace pdaleramirez\superfilter\plugin;

use pdaleramirez\superfilter\services\Templates;

trait Services
{
    public function getTemplates(): Templates
    {
        return $this->get('templates');
    }
}