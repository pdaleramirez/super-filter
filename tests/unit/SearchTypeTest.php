<?php

use Codeception\Stub;
use Codeception\Test\Unit;
use craft\elements\Entry;
use pdaleramirez\superfilter\searchtypes\EntrySearchType;
use pdaleramirez\superfilter\SuperFilter;

class SearchTypeTest extends Unit
{

    public function testSearchByRef()
    {
        $type = SuperFilter::$app->searchTypes->getSearchTypeByRef('entry');

        $expected = EntrySearchType::class;

        $this->assertEquals($expected, get_class($type));
    }

    public function testSearchTypeOptions()
    {
        $searchType = Stub::make(EntrySearchType::class,
            [
                'getElement' => Entry::class, 'getContainer' => ['shows' => 'Shows', 'movies' => 'Movies'],
                'getSorts' => ['selected' => null, 'options' => ['description', 'rating']],
                'getFields' => ['selected' => null, 'options' => ['description', 'rating']],
            ]
        );

        $items = SuperFilter::$app->searchTypes->getSearchTypeOptions($searchType);

        $expected = [
            'label' => 'Entry',
            'handle' => 'entry',
            'container' => [
                'items' => ['shows' => 'Shows', 'movies' => 'Movies'],
                'selected' => null
            ],
            'sorts' => ['selected' => null, 'options' => ['description', 'rating']],
            'fields' => ['selected' => null, 'options' => ['description', 'rating']]
        ];

        $this->assertEquals($expected, $items);
    }
}
