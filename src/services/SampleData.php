<?php
namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use Craft;
use craft\base\Element;
use craft\db\Paginator;
use craft\elements\Category;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\fields\PlainText;
use craft\helpers\Json;
use craft\models\CategoryGroup;
use craft\models\CategoryGroup_SiteSettings;
use craft\models\EntryType;
use craft\models\FieldGroup;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\records\CategoryGroup as CategoryGroupRecord;
use craft\web\twig\variables\Paginate;

class SampleData extends Component
{
    const PREFIX = 'superFilter';
    private $categoryGroupId;
    private $categoryGroupUid;
    private $entryTypeId;
    private $sectionId;

    public function generateSampleData()
    {
        $category = $this->getCategoryGroup();

        $this->categoryGroupId  = $category->id;
        $this->categoryGroupUid = $category->uid;

        $ids = $this->createFields();

        if ($ids) {
            $section = $this->createSection();

            if ($section) {
                $entryType = $this->saveEntryType($section, $ids);

                if ($entryType) {

                    $this->entryTypeId      = $entryType->id;

                    $this->createGenres();

                    $this->generateEntries();

                    return $ids;
                }
            }
        }

        return null;
    }


    /**
     * @param Section $section
     * @param $ids
     * @return bool|EntryType|mixed
     * @throws \Throwable
     * @throws \craft\errors\EntryTypeNotFoundException
     */
    public function saveEntryType(Section $section, $ids)
    {
        $entryTypes = $section->getEntryTypes();

        $entryType  = $entryTypes[0];

        $layout = [
            'Content' => $ids
        ];

        $fieldLayout = Craft::$app->getFields()->assembleLayout($layout);

        // Set the field layout
        $fieldLayout->type = Entry::class;
        $entryType->setFieldLayout($fieldLayout);

        if (!Craft::$app->getSections()->saveEntryType($entryType)) {
            return false;
        }

        $this->entryTypeId = $entryType->id;

        return $entryType;
    }

    public function createSection()
    {
        $handle = static::PREFIX . 'Shows';

        $section = Craft::$app->getSections()->getSectionByHandle($handle);
        if (!$section) {
            $section = new Section();
            $section->name   = "Shows";
            $section->handle = $handle;
            $section->type   = "channel";
            $section->enableVersioning  = true;
            $section->propagationMethod = Section::PROPAGATION_METHOD_ALL;
            $section->previewTargets    = [];

            $sites = Craft::$app->getSites()->getAllSiteIds();

            $siteSettings = [];

            if ($sites) {
                foreach ($sites as $siteId) {
                    $sectionSiteSettings = new Section_SiteSettings();

                    $sectionSiteSettings->siteId = $siteId;
                    $sectionSiteSettings->hasUrls = true;
                    $sectionSiteSettings->uriFormat = 'super-filter-shows/{slug}';

                    $siteSettings[$siteId] = $sectionSiteSettings;
                }
            }

            $section->setSiteSettings($siteSettings);

            if (!Craft::$app->getSections()->saveSection($section)) {
                return false;
            }
        }

        $this->sectionId = $section->id;

        return $section;
    }

    /**
     * @return |null
     * @throws \Throwable
     * @throws \craft\errors\CategoryGroupNotFoundException
     */
    private function createFields()
    {
        $fieldGroup = $this->getFieldGroup();
        $ids = [];
        $handle = static::PREFIX . 'Description';

        $fieldDescription = Craft::$app->getFields()->getFieldByHandle($handle);

        if (!$fieldDescription) {
            $config  = [
                'type'    => PlainText::class,
                "groupId" => $fieldGroup->id,
                'name'    => 'Description',
                'handle'  => $handle,
                'multiline'   => true,
                "initialRows" => 4,
                "columnType"  => "text"
            ];

            $fieldDescription = Craft::$app->getFields()->createField($config);

            Craft::$app->getFields()->saveField($fieldDescription);
        }

        $ids[] = $fieldDescription->id;

        $handle = static::PREFIX . "Genre";

        $fieldGenre = Craft::$app->getFields()->getFieldByHandle($handle);

        if (!$fieldGenre) {
            $config = [
                "type"    => "craft\\fields\\Categories",
                "groupId" => $fieldGroup->id,
                "source" => 'group:' . $this->categoryGroupUid,
                "name"    => "Genre",
                "handle"  => $handle
            ];

            $fieldGenre = Craft::$app->getFields()->createField($config);

            Craft::$app->getFields()->saveField($fieldGenre);
        }

        $ids[] = $fieldGenre->id;

        return $ids;
    }

    private function getFieldGroup()
    {
        $name = 'Super Filter';

        $group = new FieldGroup();

        $record = \craft\records\FieldGroup::find()->where([
            'name'      => $name
        ])->one();

        if ($record) {
            $group->setAttributes($record->getAttributes(), false);

            return $group;
        }

        $group->name = $name;

        Craft::$app->getFields()->saveGroup($group);

        return $group;
    }

    /**
     * @return bool|CategoryGroup
     * @throws \Throwable
     * @throws \craft\errors\CategoryGroupNotFoundException
     */
    private function getCategoryGroup()
    {
        $categoryGroup = new CategoryGroup();
        $handle = static::PREFIX . 'Genre';

        $categoryGroupRecord = CategoryGroupRecord::find()
            ->where([
                'dateDeleted' => null,
                'handle'      => $handle
            ])->one();

        if ($categoryGroupRecord) {

            $categoryGroup->setAttributes($categoryGroupRecord->getAttributes(), false);

            return $categoryGroup;
        }

        $categoryGroup->name   = 'Genre';
        $categoryGroup->handle = $handle;

        $siteSettings = [];

        $sites = Craft::$app->getSites()->getAllSiteIds();

        if ($sites) {
            foreach ($sites as $siteId) {
                $categorySiteSettings = new CategoryGroup_SiteSettings();

                $categorySiteSettings->siteId = $siteId;

                $siteSettings[$siteId] = $categorySiteSettings;
            }
        }

        $categoryGroup->setSiteSettings($siteSettings);

        Craft::$app->getCategories()->saveGroup($categoryGroup);

        return $categoryGroup;
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function createGenres()
    {
        $slugs = ['Action', 'Adventure'];

        $ids = [];

        foreach ($slugs as $title) {
            $slug = static::PREFIX . $title;

            $element = Category::find()->where(['slug' => $slug])->one() ?? new Category();

            if ($element->id == null) {
                $element->title   = $title;
                $element->slug    = $slug;
                $element->groupId = $this->categoryGroupId;

                Craft::$app->getElements()->saveElement($element);
            }

            $ids[] = $element->id;
        }

        return $ids;
    }

    private function generateEntries()
    {
        $entries = [
            'arrow' => [
                'title' => 'Arrow',
                'fields' => [
                    'Genre' => ['action', 'adventure'],
                    'Description' => 'This is arrow description'
                ]
            ],
            'scorpion' => [
                'title' => 'Scorpion',
                'fields' => [
                    'Genre' => ['action'],
                    'Description' => 'This is scorpion description'
                ]
            ]
        ];

        foreach ($entries as $slug => $entry) {

            $slug = static::PREFIX . $slug;

            $element = Entry::find()->where(['slug' => $slug])->one() ?? new Entry();

            $element->title       = $entry['title'];
            $element->slug        = $slug;
            $element->sectionId   = $this->sectionId;
            $element->typeId      = $this->entryTypeId;

            $fields = $entry['fields'];

            foreach ($fields as $handle => $field) {
                $filedHandle = static::PREFIX . $handle;

                $fieldElement = $this->isElement($handle);

                if ($fieldElement) {
                    $fieldIds = [];
                    foreach ($field as $fieldSlug) {
                        $elementSlug = strtolower(static::PREFIX . $fieldSlug);

                        $elementObj = $fieldElement::find()->where(['slug' => $elementSlug])->one();
                        if ($elementObj) {
                            $fieldIds[] = $elementObj->id;
                        }
                    }

                    $element->setFieldValue($filedHandle, $fieldIds);
                } else {
                    $element->setFieldValue($filedHandle, $field);
                }
            }

            Craft::$app->getElements()->saveElement($element);

        }
    }

    public function isElement($handle)
    {
         if (in_array($handle, [
           'Genre'
            ])) {
            return Category::class;
         }

         return false;
    }
}
