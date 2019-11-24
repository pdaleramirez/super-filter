<?php

namespace pdaleramirez\superfilter\searchtypes;

use Craft;
use craft\elements\Entry;
use craft\helpers\Json;
use pdaleramirez\superfilter\base\SearchType;

class EntrySearchType extends SearchType
{
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
     * @param array $sorts
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getFields()
    {
        $sections = Craft::$app->getSections()->getAllSections();

        $fields = [];

        if (!empty($sections)) {
            foreach ($sections as $section) {

                $entryTypes = $section->getEntryTypes();

                if (count($entryTypes) > 0) {
                    foreach ($entryTypes as $entryType) {
                        $fieldObjects = $entryType->getFieldLayout()->getFields();
                        $fields[$section->handle]['label'] = $section->name;
                        $fields[$section->handle]['selected'] = [];
                        if (count($fieldObjects) > 0) {
                            foreach ($fieldObjects as $key => $fieldObject) {
                                $fields[$section->handle]['options'][$key]['name'] = $fieldObject->name;
                                $fields[$section->handle]['options'][$key]['id']   = $fieldObject->id;
                            }
                        }
                    }
                }
            }
        }

        return (array) $fields;
    }

    /**
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getSorts()
    {
        return $this->getFields();
    }
}
