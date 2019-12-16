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
    protected $params;

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

    public function getItemFormat(SetupSearch $setupSearch = null)
    {
        $selected = [];

        if ($setupSearch) {
            $selected = Json::decodeIfJson($setupSearch->items);
        }

        $searchTypes = SuperFilter::$app->searchTypes->getAllSearchTypes();
        // Default element entry
        $items['elements']['selected'] = $setupSearch->elementSearchType ?? 'entry';

        if ($searchTypes) {
            foreach ($searchTypes as $handle => $searchType) {
                $items['elements']['items'][$handle] = SuperFilter::$app->searchTypes->getSearchTypeOptions($searchType, $selected);
            }
        }

        return $items;
    }

    public function getSearchTypeOptions(SearchType $searchType, $selected = [])
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

        $selectedContainer = $selected['container'] ?? null;
        if ($container) {
            $items['container']['items'] = $container;
            $items['container']['selected'] = $selectedContainer;
        }

        if ($sort) {
            $items['sorts'] = (array) $sort;
        }

        if ($field) {
            $items['items'] = (array) $field;
        }

        $selectedSorts = $selected['sorts'] ?? null;
        if ($selectedSorts && $selectedContainer) {
            $items['sorts'][$selectedContainer] = $selectedSorts;
        }

        $selectedItems = $selected['items'] ?? null;
        if ($selectedItems && $selectedContainer) {
            $items['items'][$selectedContainer] = $selectedItems;
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
                $defaultSortOptions[$count]['orderBy']   = $key;
            } else {
                if (in_array($sortOption['orderBy'], ['elements.dateCreated', 'elements.dateUpdated'])) {
                    $defaultSortOptions[$count]['name'] = $sortOption['label'];
                    $defaultSortOptions[$count]['attribute'] = $sortOption['attribute'];
                    $defaultSortOptions[$count]['orderBy'] = $sortOption['orderBy'];
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

    public function getSearchSetup($options)
    {
        $config = [];
        $config['element']     = null;
        $config['options']     = [];
        $config['items']       = [];
        $config['sorts']       = [];

        if (!is_array($options)) {
            $config = $this->getConfigById($options);
        } else {
            $config = array_merge($config, $options);
        }

        $config['currentPage'] = Craft::$app->getRequest()->getPageNum();

        $this->getPaginator($config);

        $this->config = $config;

        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
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
        $searchType->setParams($this->params);

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

    public function getDisplaySortOptions()
    {
        $items = $this->config['items'];

        return $items['sorts']['selected'] ?? null;
    }

    public function setSelectedItems($items)
    {
        $items = Json::decodeIfJson($items);

        $elementHandle = $items['elements']['selected'];
        $element = $items['elements']['items'][$elementHandle];

        $container = $element['container']['selected'];
        $sorts = $element['sorts'][$container];
        $items = $element['items'][$container];

        return [
            'container' => $container,
            'sorts' => $sorts,
            'items' => $items
        ];
    }
}
