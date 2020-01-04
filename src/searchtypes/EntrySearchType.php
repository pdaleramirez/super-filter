<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\elements\Category;
use craft\elements\Entry;
use craft\helpers\Json;
use pdaleramirez\superfilter\base\ElementSearchField;
use pdaleramirez\superfilter\base\SearchField;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\SuperFilter;

class EntrySearchType extends SearchType
{
    private $_getFields = null;

    public function getElement()
    {
        return Entry::class;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        $sections = Craft::$app->getSections()->getAllSections();

        $containers = [];

        if (!empty($sections)) {
            foreach ($sections as $section) {
                $containers[$section->handle] = $section->name;
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
        $entryOptions = SuperFilter::$app->searchTypes->getSortOptions(Entry::sortOptions());

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
        $sections = Craft::$app->getSections()->getAllSections();

        if (!empty($sections)) {
            foreach ($sections as $section) {

                $entryTypes = $section->getEntryTypes();

                if (count($entryTypes) > 0) {
                    foreach ($entryTypes as $entryType) {
                        $fieldObjects = $entryType->getFieldLayout()->getFields();
                        $this->_getFields[$section->handle]['label'] = $section->name;
                        $this->_getFields[$section->handle]['selected'] = [];
                        $this->_getFields[$section->handle]['fieldObjects'] = $fieldObjects;

                    }
                }
            }
        }

        return $this->_getFields;
    }

    public function getQuery()
    {
        if ($this->query === null) {
            $this->query = Entry::find();

            $filter = $this->items;

            $sectionHandle = $filter['container'] ?? null;

            if ($sectionHandle) {
                // elements.dateCreated
                // superFilterImdbRating
                $query = $this->query->section($sectionHandle);

                $fields = $this->params['fields'] ?? null;

                $related = null;

                if ($fields) {
                    $inc = 0;
                    foreach ($fields as $handle => $value) {
                        $fieldObj = Craft::$app->getFields()->getFieldByHandle($handle);

                        $fieldType = SuperFilter::$app->searchTypes->getSearchFieldByObj($fieldObj);
                        $fieldType->getQueryParams($query, $value);

                        $targetElement = $fieldType->getRelated($value);

                        if ($targetElement) {
                            $related[$inc]['targetElement'] = $targetElement;
                            $related[$inc]['field']         = $handle;
                        }

                        $inc++;
                    }
                }

                if ($related) {
                    $query->relatedTo(array_merge(['and'], $related));
                }
//                Craft::configure($query, [
//                    'exampleNumber' => 2
//                ]);
//                Craft::configure($query, [
////                    'superFilterGenre' => [69]
////                ]);
                //$query->superFilterGenre = [69];
                //$query->search('dropDo:two');
                //$query->search('superFilterGuides:"Violent Programmes"');
                //$query->search('superFilterImdbRating:"7"');

                if ($this->sortParam) {
                    $query->orderBy([$this->sortParam['attribute'] => $this->sortParam['sort']]);
                }

                $this->query = $query;
            }
        }

        return $this->query;
    }
}
