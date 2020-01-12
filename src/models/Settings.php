<?php

namespace pdaleramirez\superfilter\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    public $name = null;
    public $entryTemplate = null;
    public $prefixParam = 'fields';
    public $operator    = 'and';

    public function getSettingsNavItems(): array
    {
        return [
            'settingsHeading' => [
                'heading' => Craft::t('super-filter', 'Settings'),
            ],
            'general' => [
                'label' => Craft::t('super-filter', 'General'),
                'url' => 'super-filter/settings/general',
                'selected' => 'general',
                'template' => 'super-filter/settings/general'
            ],
            'filter' => [
                'label' => Craft::t('super-filter', 'Filter'),
                'url' => 'super-filter/settings/filter',
                'selected' => 'filter',
                'template' => 'super-filter/settings/filter/index'
            ]
        ];
    }
}
