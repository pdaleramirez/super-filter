<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\elements\Category;
use pdaleramirez\superfilter\base\SearchType;

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
                $fieldObjects = $group->getFieldLayout()->getFields();
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
        $groups = Craft::$app->getCategories()->getAllGroups();

        $fields = [];

        if (!empty($groups)) {
            foreach ($groups as $group) {
                $fieldObjects = $group->getFieldLayout()->getFields();
                $fields[$group->handle]['label'] = $group->name;
                $fields[$group->handle]['selected'] = [];
                $fields[$group->handle]['options'] = [];
                if (count($fieldObjects) > 0) {
                    foreach ($fieldObjects as $key => $fieldObject) {
                        $fields[$group->handle]['options'][$key]['name'] = $fieldObject->name;
                        $fields[$group->handle]['options'][$key]['orderBy'] = $fieldObject->handle;
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

            $groupHandle = $filter['container']['selected'] ?? null;

            if ($groupHandle) {
                $query = $this->query->group($groupHandle);

                $this->query = $query;
            }
        }

        return $this->query;
    }
}
