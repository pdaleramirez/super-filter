<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\elements\Category;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\SuperFilter;

class CategorySearchType extends SearchType
{
    public function getElement()
    {
        return Category::class;
    }

    public function getContainer(): array
    {
        $groups = Craft::$app->getCategories()->getAllGroups();

        $containers = [];

        if (!empty($groups)) {
            foreach ($groups as $group) {
                $containers[$group->handle] = $group->name;
            }
        }

        return $containers;
    }

    /**
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getFields()
    {
        $groups = Craft::$app->getCategories()->getAllGroups();

        $fields = [];

        if (!empty($groups)) {
            foreach ($groups as $group) {
                $fieldObjects = $group->getFieldLayout()->getCustomFields();

                $fieldObjects = $this->getSupportedFields($fieldObjects);
                $fields[$group->handle]['label'] = $group->name;
                $fields[$group->handle]['selected'] = [];
                $fields[$group->handle]['options'] = [];
                if (count($fieldObjects) > 0) {
                    foreach ($fieldObjects as $key => $fieldObject) {
                        $fields[$group->handle]['options'][$key]['name'] = $fieldObject->name;
                        $fields[$group->handle]['options'][$key]['id'] = $fieldObject->id;
                    }
                }
            }
        }

        return (array)$fields;
    }

    /**
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getSorts()
    {
        $categoryOptions = SuperFilter::$app->searchTypes->getSortOptions(Category::sortOptions());
        $groups = Craft::$app->getCategories()->getAllGroups();

        $fields = [];

        if (!empty($groups)) {
            foreach ($groups as $group) {
                $fieldObjects = $group->getFieldLayout()->getCustomFields();
                $fields[$group->handle]['label'] = $group->name;
                $fields[$group->handle]['selected'] = [];
                $fields[$group->handle]['options'] = [];

                $sortFields = [];
                if (count($fieldObjects) > 0) {
                    foreach ($fieldObjects as $key => $fieldObject) {
                        
                        if (in_array($fieldObject->handle, $categoryOptions['sortOptions'])) {
                            $sortFields[$key]['name'] = $fieldObject->name;
                            $sortFields['orderBy'] = $fieldObject->handle;
                        }

                        $fields[$group->handle]['options'] = array_merge($categoryOptions['defaultSortOptions'],
                            $sortFields);
                    }
                }
            }
        }
       
        return (array)$fields;
    }

    public function getQuery()
    {
        if ($this->query === null) {
            $this->query = Category::find();

            $filter = $this->items;

            $groupHandle = $filter['container'] ?? null;

            if ($groupHandle) {
                $query = $this->query->group($groupHandle);

                $this->query = $query;
            }
        }

        return $this->query;
    }
}
