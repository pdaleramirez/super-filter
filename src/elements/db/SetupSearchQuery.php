<?php

namespace pdaleramirez\superfilter\elements\db;

use craft\elements\db\ElementQuery;

class SetupSearchQuery extends ElementQuery
{
    /**
     * @inheritdoc
     */
    protected array $defaultOrderBy = ['superfilter_setup_search.dateCreated' => SORT_DESC];

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('superfilter_setup_search');

        $this->query->select([
            'superfilter_setup_search.handle',
            'superfilter_setup_search.elementSearchType',
            'superfilter_setup_search.items',
            'superfilter_setup_search.options',
            'superfilter_setup_search.sorts',
            'superfilter_setup_search.dateCreated',
            'superfilter_setup_search.dateUpdated']);

        return parent::beforePrepare();
    }
}
