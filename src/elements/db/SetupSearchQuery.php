<?php

namespace pdaleramirez\superfilter\elements\db;

use craft\elements\db\ElementQuery;

class SetupSearchQuery extends ElementQuery
{
    /**
     * @inheritdoc
     */
    protected $defaultOrderBy = ['superfilter_setup_search.dateCreated' => SORT_DESC];

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('superfilter_setup_search');

        $this->query->select(['superfilter_setup_search.*']);

        return parent::beforePrepare();
    }
}
