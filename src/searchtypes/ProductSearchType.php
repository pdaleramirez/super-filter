<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\commerce\elements\Product;
use craft\commerce\Plugin;
use craft\elements\Entry;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\SuperFilter;

/**
 * Class ProductSearchType
 * @package pdaleramirez\superfilter\searchtypes
 * @property array fields
 * @property array fieldObjects
 */
class ProductSearchType extends SearchType
{
    private $_getFields = null;

    public function getElement()
    {
        return Product::class;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        $productTypes = Plugin::getInstance()->getProductTypes()->getAllProductTypes();

        $containers = [];

        if (!empty($productTypes)) {
            foreach ($productTypes as $productType) {
                $containers[$productType->handle] = $productType->name;
            }
        }

        return $containers;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFields()
    {
        $fields = [];

        $fieldObjects = $this->_getFields ?? $this->getFieldObjects();

        foreach ($fieldObjects as $sectionHandle => $item) {
            $fields[$sectionHandle]['label']    = $item['label'];
            $fields[$sectionHandle]['selected'] = [];
            $itemObjects = $item['fieldObjects'];

            if (count($itemObjects) > 0) {
                foreach ($itemObjects as $key => $fieldObject) {
                    $fields[$sectionHandle]['options'][$key]['name'] = $fieldObject->name;
                    $fields[$sectionHandle]['options'][$key]['id']   = $fieldObject->id;
                }
            }
        }

        return $fields;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getSorts()
    {
        $entryOptions = SuperFilter::$app->searchTypes->getSortOptions(Product::sortOptions());

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
     * @throws \yii\base\InvalidConfigException
     */
    private function getFieldObjects()
    {
        $productTypes = Plugin::getInstance()->getProductTypes()->getAllProductTypes();

        if (!empty($productTypes)) {
            foreach ($productTypes as $productType) {

                $fieldObjects = $productType->getProductFieldLayout()->getFields();
                $this->_getFields[$productType->handle]['label'] = $productType->name;
                $this->_getFields[$productType->handle]['selected'] = [];
                $this->_getFields[$productType->handle]['fieldObjects'] = $fieldObjects;
            }
        }

        return $this->_getFields;
    }

    /**
     * @return \craft\elements\db\ElementQuery|\craft\elements\db\ElementQueryInterface|\craft\elements\db\EntryQuery
     * @throws \yii\base\Exception
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $this->query = Product::find();

            $filter = $this->items;

            $sectionHandle = $filter['container'] ?? null;

            if ($sectionHandle) {
                $query = $this->query->type($sectionHandle)->with('variants');

                $this->query = $query;
            }
        }

        return $this->query;
    }
}
