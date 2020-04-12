<?php

namespace pdaleramirez\superfilter\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\base\Serializable;
use craft\db\Paginator;
use craft\elements\db\ElementQuery;
use craft\helpers\Json;
use craft\web\twig\variables\Paginate;
use Exception;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\events\ItemArrayEvent;
use pdaleramirez\superfilter\events\RegisterSearchFieldTypeEvent;
use pdaleramirez\superfilter\events\RegisterSearchTypeEvent;
use pdaleramirez\superfilter\fields\Title;
use pdaleramirez\superfilter\SuperFilter;
use yii\base\Arrayable;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;

class SearchTypes extends Component
{
    const EVENT_REGISTER_SEARCH_TYPES = 'defineSuperFilterSearchTypes';
    const EVENT_REGISTER_SEARCH_FIELD_TYPES = 'defineSuperFilterSearchFieldTypes';
    const EVENT_ITEM_ARRAY = 'itemToArray';
    const PAGE_SIZE = 25;

    protected $config;
    protected $items;

    /**
     * @var Paginate $links
     */
    protected $links;
    protected $params;
    /**
     * @var SearchType $searchType
     */
    protected $searchType;
	protected $itemAttributes = [];
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

    /**
     * @return SearchField[]
     */
    public function getAllSearchFieldTypes()
    {
        $event = new RegisterSearchFieldTypeEvent([
            'searchFieldTypes' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_SEARCH_FIELD_TYPES, $event);

        return $event->searchFieldTypes;
    }

    /**
     * @param SetupSearch|null $setupSearch
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
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

    /**
     * @param SearchType $searchType
     * @param array $selected
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getSearchTypeOptions(SearchType $searchType, $selected = [])
    {
        /**
         * @var $element Element
         *
         */
        $className = $searchType->getElement();

        $container = $searchType->getContainer();
        $sort = $searchType->getSorts();
        $field = $searchType->getFields();

        $items['label']  = $className::displayName();
        $items['handle'] = $className::refHandle();

        $items['container'] = null;

        $selectedContainer = $selected['container'] ?? null;

        if ($container) {
            if ($selectedContainer == null && count($container) > 0) {
                $selectedContainer = array_key_first($container);
            }

            $items['container']['items'] = $container;
            $items['container']['selected'] = $selectedContainer;
        }

        if ($sort) {
            $items['sorts'] = (array) $sort;
        }

        if ($field) {
            foreach ($field as $handle => $item) {
                if (isset($field[$handle]['options'])) {
                    foreach ($field[$handle]['options'] as $key => $attribute) {
                        $id = $attribute['id'];

                        $fieldObj = Craft::$app->getFields()->getFieldById($id);

                        if ($this->getSearchFieldByObj($fieldObj) == null) {
                            unset($field[$handle]['options'][$key]);
                        }
                    }

                    array_unshift($field[$handle]['options'], ['name' => 'Title', 'id' => 'title']);
                } else {
                    $field[$handle]['options'][] =  ['name' => 'Title', 'id' => 'title'];
                }
            }

            $items['items'] = (array) $field;
        }

        if (isset($selected['sorts']) && $selectedContainer !== null) {
            $diff = $this->getSelectedOptions('sorts', $items, $selected, 'orderBy');

            $items['sorts'] = $diff;
        }

        if (isset($selected['items']) && $selectedContainer !== null) {

            $diff = $this->getSelectedOptions('items', $items, $selected);

            $items['items'] = $diff;
        }

        return $items;
    }

    private function getSelectedOptions($key, $items, $selected, $diffKey = 'id')
    {
        $selectedItems = $selected[$key] ?? null;

        $entries = $items[$key] ?? null;

        if ($entries) {
            foreach ($entries as $containerHandle => $entry) {
                $options = $entry['options'];

                if ($selectedItems && $containerHandle !== null) {
                    if (count($entry['options']) > 0) {
                        $options = $this->getDiffOptions($entry['options'], $selectedItems, $diffKey);
                    }
                }

                $entries[$containerHandle] = [
                    'selected' => $selectedItems,
                    'options' => $options,
                ];
            }
        }

        return $entries;
    }

    public function getDiffOptions($options, $selects, $key = 'id')
    {
        $selectedIds = [];

        if (count($selects) > 0) {
            foreach ($selects as $selected) {
                $selectedIds[] = $selected[$key];
            }
        }

        $diffOptions = [];

        if (count($options) > 0) {
            foreach ($options as $option) {
                if (!in_array($option[$key], $selectedIds)) {
                    $diffOptions[] = $option;
                }
            }
        }

        return $diffOptions;
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

    public function getSearchFieldType($type)
    {
        foreach ($this->getAllSearchFieldTypes() as $class) {
            if (
                ($fieldType = $class->fieldType()) !== null &&
                strcasecmp($fieldType, $type) === 0
            ) {
                return $class;
            }
        }

        return null;
    }

    /**
     * @param $options
     * @return $this
     * @throws \yii\base\Exception
     */
    public function setSearchSetup($options)
    {
        $config = [];
        $config['element']     = null;
        $config['options']     = [];
        $config['items']       = [];
        $config['sorts']       = [];
        $config['currentPage'] = 1;
        $config['params'] = [];

        $config = array_merge($config, $options);

        $sort = $config['params']['sort'] ?? null;

        if ($sort == null) {

            $initSort = $config['options']['initSort'] ?? null;

            $config['params']['sort'] = $initSort ?? 'dateCreated-desc';
        }

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

    /**
     * @param $id
     * @return |null
     * @throws Exception
     */
    public function getConfigById($id)
    {
        $config = null;
        $searchSetup = null;
        if (is_int($id)) {
            $searchSetup = Craft::$app->getElements()->getElementById($id, SetupSearch::class);
        } elseif (is_string($id)) {
            $searchSetup = SetupSearch::find()->where(['handle' => $id])->one();
        }

        if ($searchSetup == null) {
            throw new \Exception('Invalid id or handle given.');
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

    /**
     * @param $config
     * @throws \yii\base\Exception
     */
    public function getPaginator($config)
    {
        $searchTypeRef = $config['element'];

        $searchType = $this->getSearchTypeByRef($searchTypeRef);

        $searchType->setOptions($config['options']);
        $searchType->setItems($config['items']);
        $searchType->setSorts($config['sorts']);
        $searchType->setParams($config['params']);

        $elementQuery = $this->elementQuery($searchType);

        $siteId = $config['params']['siteId'] ?? null;

        if ($siteId) {
            $elementQuery->siteId($siteId);
        }

        $paginator = new Paginator($elementQuery, [
            'currentPage' => $config['currentPage'],
            'pageSize'    => $config['options']['perPage'] != '' ? $config['options']['perPage'] : static::PAGE_SIZE
        ]);

        $this->links = Paginate::create($paginator);

        if ($config['currentPage'] > $paginator->getTotalPages()) {
            $this->items = [];
        } else {
            $this->items = $paginator->getPageResults();
        }

        $this->searchType = $searchType;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getItems()
    {
        return $this->items;
    }

	public function setItemAttributes(array $itemAttributes = [])
	{
		$this->itemAttributes = $itemAttributes;
	}

	public function serializeValue($value)
	{
		// If the object explicitly defines its savable value, use that
		if ($value instanceof Serializable) {
			return $value->serialize();
		}

		// If it's "arrayable", convert to array
		if ($value instanceof Arrayable) {
			return $value->toArray();
		}

		// Only DateTime objects and ISO-8601 strings should automatically be detected as dates
		if ($value instanceof \DateTime || DateTimeHelper::isIso8601($value)) {
			return Db::prepareDateForDb($value);
		}

		return $value;
	}

	public function getItemToArray()
    {
        $items = [];

        if (count($this->items)> 0) {
			/**
			 * @var Element $item
			 */
            foreach ($this->items as $key => $item) {
				if (count($this->itemAttributes) > 0) {
					foreach ($this->itemAttributes as $attribute) {
						$attributeValue = $item->{$attribute};

						if ($attributeValue instanceof ElementQuery) {
							$elements = $attributeValue->all();
							if (count($elements) > 0) {
								foreach ($elements as $elementKey => $element) {
									$items[$key][$attribute][$elementKey] = $element->toArray();
								}
							} else {
								$items[$key][$attribute] = [];
							}
						} else {
							$items[$key][$attribute] = $this->serializeValue($item->{$attribute});
						}
					}
				} else {
                	$items[$key] = array_merge($item->toArray(), $this->formatItemFields($item));
				}

                $event = new ItemArrayEvent([
                    'element'   => $item,
                    'item'      => null,
                    'searchType' => $this->searchType
                ]);

                $this->trigger(static::EVENT_ITEM_ARRAY, $event);

                if ($event->item !== null) {
                    $items[$key] = array_merge($items[$key], $event->item);
                }
            }
        }

        return $items;
    }

    private function formatItemFields($element)
    {
        $fieldValues = $element->getFieldValues();

        $fields = [];

        if (count($fieldValues) > 0) {
            foreach ($fieldValues as $key => $value) {
                if ($value instanceof ElementQuery) {
                    $elements = $value->all();
                    if (count($elements) > 0) {
                        foreach ($elements as $elementKey => $element) {
                            $fields[$key][$elementKey] = $element->toArray();
                        }
                    } else {
                        $fields[$key] = [];
                    }

                } else {
                    $fields[$key] = $value;
                }
            }
        }


        return $fields;
    }

    public function getDisplaySortOptions()
    {
        $items = $this->config['items'];

        return $items['sorts'] ?? null;
    }

    public function getDisplaySearchFields()
    {
        $items = $this->config['items'];

        return $items['items'] ?? null;
    }

    /**
     * @return array|null
     * @throws \yii\base\Exception
     */
    public function getSearchFieldsObjects()
    {
        $fields = null;
        $searchFields = $this->getDisplaySearchFields();

        if (count($searchFields) > 0) {
            foreach ($searchFields as $field) {;
                $fields[] = $this->getSearchFieldObjectById($field['id']);
            }
        }

        return $fields;
    }


    /**
     * @param $id
     * @param bool $handle
     * @return SearchField|Title|null
     * @throws \yii\base\Exception
     * @throws Exception
     *
     */
    public function getSearchFieldObjectById($id, $handle = false)
    {
        $fieldObj = null;
        if (is_string($id) && $id == 'title') {
            $searchField = new Title();
            $this->setSearchFieldAttributes($searchField, 'title');

            return $searchField;
        }

        if ($handle == true) {
            $fieldObj = Craft::$app->getFields()->getFieldByHandle($id);
        } else {
            $fieldObj = Craft::$app->getFields()->getFieldById($id);
        }

        return  $this->getSearchFieldByObj($fieldObj);
    }

    /**
     * @param FieldInterface|Field $fieldObj
     * @return SearchField|null
     * @throws Exception
     */
    public function getSearchFieldByObj(FieldInterface $fieldObj)
    {
        $type = get_class($fieldObj);

        $searchField = $this->getSearchFieldType($type);

        if ($searchField == null) {
            return null;
        }

        $searchField->setObject($fieldObj);

        $handle = $fieldObj->handle;

        $this->setSearchFieldAttributes($searchField, $handle);

        return $searchField;
    }

    /**
     * @param SearchField $searchField
     * @param $handle
     * @return SearchField
     */
    public function setSearchFieldAttributes(SearchField $searchField, $handle)
    {
        $fields = $this->config['params']['fields'] ?? null;

        if ($fields) {

            $fieldValue = $fields[$handle] ?? null;

            if ($fieldValue) {
                $searchField->setValue($fieldValue);
            }
        }

        return $searchField;
    }

    public function setSelectedItems($items)
    {
        $items = Json::decodeIfJson($items);

        $elementHandle = $items['elements']['selected'];
        $element = $items['elements']['items'][$elementHandle];

        $container = $element['container']['selected'];

        $sorts = null;

        if ($container) {
            $sorts = $element['sorts'][$container]['selected'];
            $items = $element['items'][$container]['selected'];
        }

        return [
            'container' => $container,
            'sorts' => $sorts,
            'items' => $items
        ];
    }


    /**
     * @param string $filename
     * @return |null
     * @throws \yii\base\Exception
     */
    public function getTemplate(string $filename)
    {
        $config  = $this->getConfig();

        $options = $config['options'];

        $template     = $options['template'] ?? null;
        $baseTemplate = $options['baseTemplate'] ?? null;

        if ($template) {

            $siteTemplatesPath = Craft::$app->path->getSiteTemplatesPath();

            Craft::$app->getView()->setTemplatesPath($siteTemplatesPath);

            $siteTemplate = $template . '/' . $filename;
            if (Craft::$app->getView()->doesTemplateExist($siteTemplate)) {
                return $siteTemplate;
            }
        }

        $builtin = Craft::getAlias('@superfilterModule/templates');

        Craft::$app->getView()->setTemplatesPath($builtin);

        return $baseTemplate . '/' . $filename;
    }

    /**
     * @param SearchType $searchType
     * @return \craft\elements\db\ElementQuery
     * @throws \yii\base\Exception
     */
    private function elementQuery(SearchType $searchType)
    {
        $query = $searchType->getQuery();

        $fields = $searchType->params['fields'] ?? null;

        $related = null;
        $searchQuery = null;
        if ($fields) {
            $inc = 0;
            foreach ($fields as $handle => $value) {
                $fieldType = SuperFilter::$app->searchTypes->getSearchFieldObjectById($handle, true);

                $fieldType->getQueryParams($query, $value);

                $searchParams = $fieldType->getSearchParams($value);

                if ($searchParams) {
                    $searchQuery[$inc]= $searchParams;
                }

                $targetElement = $fieldType->getRelated($value);

                if ($targetElement) {
                    $related[$inc]['targetElement'] = $targetElement;
                    $related[$inc]['field']         = $handle;
                }

                $inc++;
            }
        }

        $operator = SuperFilter::$app->getSettings()->operator;
        $operator = strtolower($operator);

        if ($searchQuery) {
            $searchOperator = $operator == 'and' ? ' ' : ' OR ';
            $query->search(implode($searchOperator, $searchQuery));
        }

        if ($related) {
            $query->relatedTo(array_merge([$operator], $related));
        }

        if ($searchType->sortParam) {
            $query->orderBy([$searchType->sortParam['attribute'] => $searchType->sortParam['sort']]);
        }

        return $query;
    }

	public function getInitFields($config)
	{
		$items = $config['items']['items'] ?? null;

		$fields = [];
		if ($items) {
			foreach ($items as $item) {
				$searchField = $this->getSearchFieldObjectById($item['id']);

				$handle = $searchField->getObject()->handle;

				$fields[$handle] = $searchField->initValue;
			}
		}

		return $fields;
	}
}
