<?php

namespace pdaleramirez\superfilter\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\db\Paginator;
use craft\elements\Entry;
use craft\helpers\Json;
use craft\web\twig\variables\Paginate;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\contracts\SearchTypeInterface;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\events\RegisterSearchTypeEvent;
use pdaleramirez\superfilter\SuperFilter;

class SearchTypes extends Component
{
    const EVENT_REGISTER_SEARCH_TYPES = 'defineSuperFilterSearchTypes';

    protected $config;
    protected $items;
    protected $links;

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
        $sort = $searchType->getSorts();
        $field = $searchType->getFields();

        $items['label'] = $element->displayName();
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
            $items['items'] = (array) $field;
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
                $defaultSortOptions[$count]['name'] = $sortOption;
                $defaultSortOptions[$count]['attribute'] = $key;
            } else {
                if (in_array($sortOption['orderBy'], ['elements.dateCreated', 'elements.dateUpdated'])) {
                    $defaultSortOptions[$count]['name'] = $sortOption['label'];
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

//    public function getSearchTypeByElement(SetupSearch $setupSearch)
//    {
//        $ref = $setupSearch->elementSearchType;
//
//        if (array_key_exists($ref, $this->_searchTypesByRef)) {
//            return $this->_searchTypesByRef[$ref] ?? null;
//        }
//
//        $class = $this->getSearchTypeByRef($ref);
//
//        $class->setElement($setupSearch);
//
//        $this->_searchTypesByRef[$ref] = $class;
//
//        return $this->_searchTypesByRef[$ref] ?? null;
//    }

    public function getSearchTypeByRef($ref)
    {
        foreach ($this->getAllSearchTypes() as $class) {

            $element = $class->getElement();

            if (
                ($elementRefHandle = $element::refHandle()) !== null &&
                strcasecmp($elementRefHandle, $ref) === 0
            ) {
                return $class;
            }
        }

        return null;
    }

    public function getSearchSetup($params)
    {
        $config = [];
        $config['element']     = null;
        $config['options']     = [];
        $config['items']       = [];
        $config['sorts']       = [];

        if (!is_array($params)) {
            $config = $this->getConfigById($params);
        } else {
            $config = array_merge($config, $params);
        }

        $config['currentPage'] = Craft::$app->getRequest()->getPageNum();

        $this->getPaginator($config);

        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigById($id)
    {
        $config = null;

        if (is_int($id)) {
            $searchSetup = Craft::$app->getElements()->getElementById($id, SetupSearch::class);
        } elseif (is_string($id)) {
            $searchSetup = SetupSearch::find()->where(['handle' => $id])->one();
        }

        /**
         * @var $searchSetup SetupSearch
         */
        if ($searchSetup) {
            $config['element'] = $searchSetup->elementSearchType;
            $config['options'] = $searchSetup->options();
            $config['items']   = $searchSetup->items();
            $config['sorts']   = $searchSetup->sorts();
        }

        return $config;
    }

    public function getPaginator($config)
    {
        $searchTypeRef = $config['element'];

        $searchType = $this->getSearchTypeByRef($searchTypeRef);

        $searchType->setOptions($config['options']);
        $searchType->setItems($config['items']);
        $searchType->setSorts($config['sorts']);

        $paginator = new Paginator($searchType->getQuery(), [
            'currentPage' => $config['currentPage'],
            'pageSize'    => $config['options']['perPage']
        ]);

        $this->links = Paginate::create($paginator);;
        $this->items = $paginator->getPageResults();
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getItems()
    {
        return $this->items;
    }
}
