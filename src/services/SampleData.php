<?php
namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use Craft;
use craft\elements\Category;
use craft\elements\Entry;
use craft\fields\Categories;
use craft\fields\Checkboxes;
use craft\fields\Dropdown;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Tags;
use craft\models\CategoryGroup;
use craft\models\CategoryGroup_SiteSettings;
use craft\models\EntryType;
use craft\models\FieldGroup;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\models\TagGroup;
use craft\records\CategoryGroup as CategoryGroupRecord;
use craft\elements\Tag as TagElement;

class SampleData extends Component
{
    const PREFIX = 'superFilter';
    private $fieldGroupId;
    private $categoryGroupId;
    private $categoryGroupUid;
    private $entryTypeId;
    private $sectionId;
    private $tagGroupId;
    private $tagGroupUid;

    public function generateSampleData()
    {
        $categoryGroup = $this->getCategoryGroup();

        $tagGroup = $this->getTagGroup();
        $this->tagGroupId  = $tagGroup->id;
        $this->tagGroupUid = $tagGroup->uid;

        $this->createTags($tagGroup);

        $this->categoryGroupId  = $categoryGroup->id;
        $this->categoryGroupUid = $categoryGroup->uid;

        $ids = $this->createFields();

        if ($ids) {
            $section = $this->createSection();

            if ($section) {
                $entryType = $this->saveEntryType($section, $ids);

                if ($entryType) {

                    $this->entryTypeId      = $entryType->id;

                    $ids = $this->createGenres();

                    $this->generateEntries();

                    return $ids;
                }
            }
        }

        return null;
    }

    private function createTags()
    {
        $tags = ['Funny', 'Sexy', 'Dark', 'Apocalypse', 'Teen', 'Superhero', 'Hacking', 'Crime', 'Exciting', 'Survival'];

        $ids = [];
        foreach ($tags as $title) {
            $slug = strtolower(static::PREFIX . $title);

            $tagModel = TagElement::find()->where(['slug' => $slug])->one() ?? new TagElement();
            $tagModel->title = $title;
            $tagModel->slug  = $slug;
            $tagModel->groupId = $this->tagGroupId;

            Craft::$app->getElements()->saveElement($tagModel);
            $ids[] = $tagModel->id;
        }

        return $ids;
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

        $this->fieldGroupId = $fieldGroup->id;

        $ids = [];

        $ids[] = $this->getFieldDescription();
        $ids[] = $this->getFieldGenre();
        $ids[] = $this->getFieldShowTags();
        $ids[] = $this->getFieldShowTypes();
        $ids[] = $this->getFieldReleaseDate();
        $ids[] = $this->getFieldGuides();
        $ids[] = $this->getFieldImdbRating();

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

    private function getTagGroup()
    {
        $tagGroup = new TagGroup();

        $tagHandle = static::PREFIX . 'showTags';

        $tagGroupRecord = \craft\records\TagGroup::find()
            ->where([
                'dateDeleted' => null,
                'handle'      => $tagHandle
            ])->one();

        if ($tagGroupRecord) {
            $tagGroup->setAttributes($tagGroupRecord->getAttributes(), false);

            return $tagGroup;
        }

        $tagGroup->name   = 'Show Tags';
        $tagGroup->handle = $tagHandle;

        if (!Craft::$app->getTags()->saveTagGroup($tagGroup)) {
            return false;
        }

        return $tagGroup;
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function createGenres()
    {
        $slugs = ['Action', 'Adventure', 'Fantasy', 'Sci-Fi'];

        $ids = [];

        foreach ($slugs as $title) {
            $slug = strtolower(static::PREFIX . $title);

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
                    'Description' => 'This is arrow description',
                    'ShowTags' => ['superhero', 'crime', 'exciting', 'survival'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDates' => 2012,
                    'Guides' => ['Comic Book', 'Police Detective'],
                    'ImdbRating' => 6
                ]
            ],
            'scorpion' => [
                'title' => 'Scorpion',
                'fields' => [
                    'Genre' => ['action'],
                    'Description' => 'This is scorpion description',
                    'ShowTags' => ['crime', 'hacking'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDates' => 2014,
                    'Guides' => ['Exciting US Programmes'],
                    'ImdbRating' => 7
                ]
            ],
            'attack-on-titan' => [
                'title' => 'Attack on Titan',
                'fields' => [
                    'Genre' => ['action'],
                    'Description' => 'This is attack on titan description',
                    'ShowTags' => ['dark', 'violent'],
                    'ShowTypes' => 'anime',
                    'ReleaseDates' => 2017,
                    'Guides' => ['Anime Action', 'Binge Worthy'],
                    'ImdbRating' => 8
                ]
            ],
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

         if (in_array($handle, [
           'ShowTags'
            ])) {
            return TagElement::class;
         }

         return false;
    }

    private function getFieldDescription()
    {
        $handle = static::PREFIX . 'Description';

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $config  = [
            'type'    => PlainText::class,
            "id"      => $fieldByHandle->id ?? null,
            "groupId" => $this->fieldGroupId,
            'name'    => 'Description',
            'handle'  => $handle,
            'multiline'   => true,
            "initialRows" => 4,
            "columnType"  => "text"
        ];

        return $this->saveField($config);
    }

    private function getFieldGenre()
    {
        $handle = static::PREFIX . "Genre";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $config = [
            "type"    => Categories::class,
            "id"      => $fieldByHandle->id ?? null,
            "groupId" => $this->fieldGroupId,
            "source" => 'group:' . $this->categoryGroupUid,
            "name"    => "Genre",
            "handle"  => $handle
        ];

        return $this->saveField($config);
    }

    private function getFieldShowTags()
    {

        $handle = static::PREFIX . "ShowTags";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $config = [
            "type"    => Tags::class,
            "id"      => $fieldByHandle->id ?? null,
            "groupId" => $this->fieldGroupId,
            "source" => 'taggroup:' . $this->tagGroupUid,
            "name"    => "Show Tags",
            "handle"  => $handle
        ];

        return $this->saveField($config);
    }

    private function getFieldShowTypes()
    {
        $handle = static::PREFIX . "ShowTypes";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $config = [
            "type"    => Dropdown::class,
            "id"      => $fieldByHandle->id ?? null,
            "name"    => "Show Types",
            "handle"  => $handle,
            "groupId" => $this->fieldGroupId,
            'optgroups' => true,
            'settings' => [
                    'options' => [
                    [
                        'label' => 'Films',
                        'value' => 'films'
                    ],
                    [
                        'optgroup' => 'Series'
                    ],
                    [
                        'label' => 'TV Series',
                        'value' => 'tv-series'
                    ],
                    [
                        'label' => 'Documentaries',
                        'value' => 'documentaries'
                    ],
                    [
                        'label' => 'Anime',
                        'value' => 'anime'
                    ]
                ]
            ]
        ];

        return $this->saveField($config);
    }

    private function getFieldReleaseDate()
    {
        $handle = static::PREFIX . "ReleaseDate";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $options = [];

        // @bug if value is not type cast string it throws an error
        for ($i=2011; $i<=2019; $i++) {
            $options[] = [
                'label' => $i,
                'value' => (string) $i
            ];
        }

        $config = [
            "type"    => Dropdown::class,
            "id"      => $fieldByHandle->id ?? null,
            "name"    => "Release Date",
            "handle"  => $handle,
            "groupId" => $this->fieldGroupId,
            'settings' => ['options' => $options]
        ];

        return $this->saveField($config);
    }

    private function getFieldGuides()
    {
        $handle = static::PREFIX . "Guides";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $guides = [ 'Violent Programmes', 'Exciting US Programmes', 'Netflix Originals',
            'Binge Worthy', 'Critically Acclaimed', 'Comic Book', 'Police Detective', 'Anime Action' ];

        $options = [];
        foreach ($guides as $guide) {
            $options[] = [
                'label' => $guide,
                'value' => $guide
            ];
        }

        $config = [
            "type"    => Checkboxes::class,
            "id"      => $fieldByHandle->id ?? null,
            "name"    => "Guides",
            "handle"  => $handle,
            "groupId" => $this->fieldGroupId,
            'settings' => ['options' => $options]
        ];

        return $this->saveField($config);
    }

    private function getFieldImdbRating()
    {
        $handle = static::PREFIX . "ImdbRating";

        $fieldByHandle = Craft::$app->getFields()->getFieldByHandle($handle);

        $options = [];

        for ($i=4; $i<=9; $i++) {
            $options[] = [
                'label' => $i,
                'value' => (string) $i
            ];
        }

        $config = [
            "type"    => RadioButtons::class,
            "id"      => $fieldByHandle->id ?? null,
            "name"    => "Imdb Rating",
            "handle"  => $handle,
            "groupId" => $this->fieldGroupId,
            'settings' => ['options' => $options]
        ];

        return $this->saveField($config);
    }

    /**
     * @param array $config
     * @return mixed
     * @throws \Throwable
     */
    private function saveField(array $config)
    {
        $fieldByHandle = Craft::$app->getFields()->createField($config);

        Craft::$app->getFields()->saveField($fieldByHandle);

        return $fieldByHandle->id;
    }
}
