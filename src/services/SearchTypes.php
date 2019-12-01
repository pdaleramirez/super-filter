<?php

namespace pdaleramirez\superfilter\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\elements\Entry;
use craft\helpers\Json;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\events\RegisterSearchTypeEvent;

class SearchTypes extends Component
{
    const EVENT_REGISTER_SEARCH_TYPES = 'defineSuperFilterSearchTypes';

    private $_searchTypesByRef = [];

    /**
     * @var $searchSetup SetupSearch
     */
    protected $searchSetup = null;
    protected $searchSetupId = null;

    public function setId($id)
    {
        $this->searchSetupId = $id;
    }

    /**
     * @return array|SearchType[]
     */
    public function getAllSearchTypes()
    {
        $event = new RegisterSearchTypeEvent([
            'searchTypes' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_SEARCH_TYPES, $event);

        return $event->searchTypes;
    }

    public function getSearchTypeOptions(SearchType $searchType)
    {
        /**
         * @var $element Element
         *
         */
        $className = $searchType->getElement();
        $element = new $className;
        $container = $searchType->getContainer();
        $sort      = $searchType->getSorts();
        $field     = $searchType->getFields();

        $items['label']  = $element->displayName();
        $items['handle'] = $element->refHandle();

        $items['container'] = null;

        if ($container) {
            $items['container']['items'] = $container;
            $items['container']['selected'] = null;
        }

        if ($sort) {
            $items['sorts'] = (array) $sort;
        }

        if ($field) {
            $items['fields'] = (array) $field;
        }

        return $items;
    }

    public function getSortOptions($elementSortOptions)
    {
        $defaultSortOptions = [];

        $sortOptions = [];
        $count = 0;
        foreach ($elementSortOptions as $key => $sortOption) {
            if (is_string($key)) {
                $defaultSortOptions[$count]['name']      = $sortOption;
                $defaultSortOptions[$count]['attribute'] = $key;
            } else {
                if (in_array($sortOption['orderBy'], ['elements.dateCreated', 'elements.dateUpdated'])) {
                    $defaultSortOptions[$count]['name']      = $sortOption['label'];
                    $defaultSortOptions[$count]['attribute'] = $sortOption['attribute'];
                }

                $attribute = str_replace('field_', '', $sortOption['orderBy']) ?? null;

                $sortOptions[] = $attribute;
            }

            $count++;
        }

        return [
            'defaultSortOptions' => $defaultSortOptions,
            'sortOptions' => $sortOptions,
        ];
    }

    public function getSearchTypeByRef(string $ref)
    {
        if (array_key_exists($ref, $this->_searchTypesByRef)) {
            return $this->_searchTypesByRef[$ref] ?? null;
        }

        foreach ($this->getAllSearchTypes() as $class) {

            $element = $class->getElement();

            if (
                ($elementRefHandle = $element::refHandle()) !== null &&
                strcasecmp($elementRefHandle, $ref) === 0
            ) {
                return $this->_searchTypesByRef[$ref] = $class;
            }
        }

        return $this->_searchTypesByRef[$ref] ?? null;
    }


    public function getSearchSetup($id)
    {
        $setupSearch = null;

        if (is_int($id)) {
            /**
             * @var $setupSearch SetupSearch
             */
            $this->searchSetup = Craft::$app->getElements()->getElementById($id, SetupSearch::class);
        } elseif (is_string($id)) {
            $this->searchSetup = SetupSearch::find()->where(['handle' => $id])->one();
        }

        return $this;
    }

    /**
     * @return SetupSearch
     */
    public function element()
    {
        return $this->searchSetup;
    }

    public function options()
    {
        return Json::decodeIfJson($this->searchSetup->options);
    }

    public function fields()
    {
        return Json::decodeIfJson($this->searchSetup->fields);
    }

    public function getItems($id = null)
    {
        $setupId = $this->searchSetupId ?? $id;
        $setupSearch = $this->getSearchSetup($setupId);

        $searchTypeRef = $setupSearch->element()->elementSearchType;


    }
}
