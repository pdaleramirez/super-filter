<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\Component;
use craft\elements\db\ElementQuery;
use pdaleramirez\superfilter\contracts\SearchTypeInterface;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\SuperFilter;

/**
 * Class SearchType
 * @package pdaleramirez\superfilter\base
 * @property array container
 * @property array fieldTypes
 */
abstract class SearchType extends Component implements SearchTypeInterface
{
    /**
     * @var SetupSearch
     */
    protected $element = null;
    protected $options = null;
    protected $items = null;
    protected $sorts = null;
    public $sortParam = null;
    public $params = null;
    /**
     * @var $query ElementQuery
     */
    protected $query;

    public function setElement(SetupSearch $setupSearch)
    {
        $this->element = $setupSearch;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    public function setParams($params)
    {
        $this->params = $params;

        $sort = $params['sort'] ?? null;

        if ($sort) {
            $sort = explode('-', $sort);

            $this->sortParam['attribute'] = $sort[0];
            $this->sortParam['sort'] = $sort[1] === 'desc' ? SORT_DESC : SORT_ASC;
        }
    }

    /**
     * @return |null
     */
    public function getContainer()
    {
        return null;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getSorts()
    {
        $element = $this->getElement();
        $entryOptions = SuperFilter::$app->searchTypes->getSortOptions($element::sortOptions());

        $fields = [];

        $fieldObjects = $this->_getFields ?? $this->getFieldObjects();

        foreach ($fieldObjects as $sectionHandle => $item) {
            $fields[$sectionHandle]['label']    = $item['label'];
            $fields[$sectionHandle]['selected'] = [];
            $itemObjects = $item['fieldObjects'];

            $sortFields = [];
            if (count($itemObjects) > 0) {
                foreach ($itemObjects as $key => $fieldObject) {
                    if (in_array($fieldObject->handle, $entryOptions['sortOptions'])) {
                        $sortFields[$key]['name'] = $fieldObject->name;
                        $sortFields[$key]['orderBy'] = $fieldObject->handle;
                    }
                }
            }

            $fields[$sectionHandle]['options'] = array_merge($entryOptions['defaultSortOptions'],
                $sortFields);
        }

        return $fields;
    }

    /**
     * @return |null
     * @throws \Exception
     */
    protected function getFieldTypes()
    {
        $fields = $this->params['fields'] ?? null;

        $fieldTypes = null;

        if ($fields) {
            foreach ($fields as $handle => $value) {
                $fieldObj = Craft::$app->getFields()->getFieldByHandle($handle);

                $fieldTypes[$handle]['type']  = SuperFilter::$app->searchTypes->getSearchFieldByObj($fieldObj);
                $fieldTypes[$handle]['value'] = $value;
            }
        }

        return $fieldTypes;
    }

    /**
     * @param $fieldObjects
     * @return array
     */
    public function getSupportedFields($fieldObjects)
    {
        $fieldClasses = SuperFilter::$app->searchTypes->getAllSearchFieldTypeClasses();
        
        $fields = [];
        foreach ($fieldObjects as $object) {
            if (in_array(get_class($object), $fieldClasses)) {
                $fields[] = $object;
            }
        }

        return $fields;
    }
}
