<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\elements\Entry;
use pdaleramirez\superfilter\base\SearchType;
use pdaleramirez\superfilter\SuperFilter;

/**
 * Class EntrySearchType
 * @package pdaleramirez\superfilter\searchtypes
 */
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
		if ($fieldObjects !== null) {
			foreach ($fieldObjects as $sectionHandle => $item) {
				$fields[$sectionHandle]['label'] = $item['label'];
				$fields[$sectionHandle]['selected'] = [];
				$itemObjects = $item['fieldObjects'];

				if (count($itemObjects) > 0) {
					foreach ($itemObjects as $key => $fieldObject) {
						$fields[$sectionHandle]['options'][$key]['name'] = $fieldObject->name;
						$fields[$sectionHandle]['options'][$key]['id'] = $fieldObject->id;
					}
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

		if ($fieldObjects !== null) {

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

    /**
     * @return \craft\elements\db\ElementQuery|\craft\elements\db\ElementQueryInterface|\craft\elements\db\EntryQuery
     * @throws \yii\base\Exception
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $this->query = Entry::find();

            $filter = $this->items;

            $sectionHandle = $filter['container'] ?? null;

            if ($sectionHandle) {
                $query = $this->query->section($sectionHandle);

                $this->query = $query;
            }
        }

        return $this->query;
    }
}
