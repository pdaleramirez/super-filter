<?php
namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use Craft;
use craft\base\Element;
use craft\elements\Category;
use craft\elements\Entry;
use craft\fields\Categories;
use craft\fields\Checkboxes;
use craft\fields\Dropdown;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Tags;
use craft\helpers\ElementHelper;
use craft\helpers\FileHelper;
use craft\helpers\UrlHelper;
use craft\models\CategoryGroup;
use craft\models\CategoryGroup_SiteSettings;
use craft\models\EntryType;
use craft\models\FieldGroup;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\models\TagGroup;
use craft\records\CategoryGroup as CategoryGroupRecord;
use craft\elements\Tag as TagElement;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\SuperFilter;

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
        $this->createSearchSetup();

        $categoryGroup = $this->getCategoryGroup();

        $tagGroup = $this->getTagGroup();
        $this->tagGroupId  = $tagGroup->id;
        $this->tagGroupUid = $tagGroup->uid;

        $this->categoryGroupId  = $categoryGroup->id;
        $this->categoryGroupUid = $categoryGroup->uid;

        $ids = $this->createFields();

        if ($ids) {
            $section = $this->createSection();

            if ($section) {
                $entryType = $this->saveEntryType($section, $ids);

                if ($entryType) {

                    $this->entryTypeId      = $entryType->id;

                    $this->generateEntries();

                    return $ids;
                }
            }
        }

        return null;
    }

    public function createSearchSetup()
    {
        $handle = 'superFilterShows';

        $setupElement = SetupSearch::find()->where(['handle' => $handle])->one();
        if ($setupElement === null) {
            $setupElement = new SetupSearch();
        }


        $setupElement->title = Craft::t("super-filter", "Super Filter Shows");
        $setupElement->handle = $handle;
        $postItems = '{"elements":{"selected":"entry","items":{"entry":{"label":"Entry","handle":"entry","container":{"items":{"artices":"Artices","news":"News","superFilterShows":"Shows"},"selected":"superFilterShows"},"sorts":{"artices":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[]},"news":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[]},"superFilterShows":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[]}},"items":{"artices":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[]},"news":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[]},"superFilterShows":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[]}}},"category":{"label":"Category","handle":"category","container":{"items":{"superFilterGenre":"Genre"},"selected":"superFilterShows"},"sorts":{"superFilterGenre":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[]}},"items":{"superFilterGenre":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[]}}},"product":{"label":"Product","handle":"product","container":{"items":{"clothing":"Clothing","gear":"Gear","toys":"Toys"},"selected":"superFilterShows"},"sorts":{"clothing":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[{"name":"Promotable?","attribute":"promotable","orderBy":"promotable"},{"name":"Price","attribute":"defaultPrice","orderBy":"defaultPrice"},{"name":"SKU","attribute":"defaultSku","orderBy":"defaultSku"}]},"gear":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[{"name":"Promotable?","attribute":"promotable","orderBy":"promotable"},{"name":"Price","attribute":"defaultPrice","orderBy":"defaultPrice"},{"name":"SKU","attribute":"defaultSku","orderBy":"defaultSku"}]},"toys":{"selected":[{"name":"Title","attribute":"title","orderBy":"title"},{"name":"Date Created","attribute":"dateCreated","orderBy":"elements.dateCreated"},{"name":"Date Updated","attribute":"dateUpdated","orderBy":"elements.dateUpdated"},{"name":"URI","attribute":"uri","orderBy":"uri"},{"name":"Slug","attribute":"slug","orderBy":"slug"}],"options":[{"name":"Promotable?","attribute":"promotable","orderBy":"promotable"},{"name":"Price","attribute":"defaultPrice","orderBy":"defaultPrice"},{"name":"SKU","attribute":"defaultSku","orderBy":"defaultSku"}]}},"items":{"clothing":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[{"name":"Price Range","id":"superFilterPriceRange"}]},"gear":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[{"name":"Price Range","id":"superFilterPriceRange"}]},"toys":{"selected":[{"name":"Title","id":"title"},{"name":"Imdb Rating","id":"6"},{"name":"Show Tags","id":"3"},{"name":"Show Types","id":"4"},{"name":"Description","id":"1"},{"name":"Release Date","id":"5"},{"name":"Genre","id":"2"}],"options":[{"name":"Price Range","id":"superFilterPriceRange"}]}}}}}}';

        $items = SuperFilter::$app->searchTypes->setSelectedItems($postItems);
        $setupElement->items = $items;

        $setupElement->options = [
            'perPage' => 10,
            'baseTemplate' => 'plain',
            'template' => 'superfilter',
            'initSort' => 'elements.dateCreated-asc'
        ];

        $setupElement->elementSearchType = 'entry';

        Craft::$app->getElements()->saveElement($setupElement);
    }

    public function createFiles($templatesPath, $folderName): void
    {
        $slash = DIRECTORY_SEPARATOR;
        $destination = $templatesPath . $slash . $folderName;
        $pathService = Craft::$app->getPath();
        $exampleTemplatesSource = FileHelper::normalizePath(
            $pathService->getVendorPath() . '/pdaleramirez/super-filter/templates/vue'
        );

        $exampleFilePage = FileHelper::normalizePath(
            $pathService->getVendorPath() . '/pdaleramirez/super-filter/templates/example-page.twig');

        FileHelper::copyDirectory($exampleTemplatesSource, $destination, ['recursive' => true, 'copyEmptyDirectories' => true]);

        $examplePageDestination = $templatesPath . $slash . 'example-page.twig';

        $fileContents = file_get_contents($exampleFilePage);
        FileHelper::writeToFile($examplePageDestination, $fileContents);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    private function _getTemplatesPath(): string
    {
        $originalMode = Craft::$app->getView()->getTemplateMode();
        Craft::$app->getView()->setTemplateMode(\craft\web\View::TEMPLATE_MODE_SITE);
        $templatesPath = Craft::$app->getView()->getTemplatesPath();
        Craft::$app->getView()->setTemplateMode($originalMode);
        return $templatesPath;
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

    private function generateEntries()
    {
        $entries = [
            'arrow' => [
                'title' => 'Arrow',
                'fields' => [
                    'Genre' => ['action', 'adventure'],
                    'Description' => 'This is arrow description',
                    'ShowTags' => ['superhero', 'exciting', 'survival'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDate' => 2012,
                    'ImdbRating' => 6
                ]
            ],
            'scorpion' => [
                'title' => 'Scorpion',
                'fields' => [
                    'Genre' => ['action', "Crime"],
                    'Description' => 'This is scorpion description',
                    'ShowTags' => ['hacking'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDate' => 2014,
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
                    'ReleaseDate' => 2017,
                    'ImdbRating' => 8
                ]
            ],
            'deadpool' => [
                'title' => 'Deadpool',
                'fields' => [
                    'Genre' => ['action', 'adventure', 'comedy'],
                    'Description' => 'A wisecracking mercenary gets experimented on and becomes immortal but ugly, and sets out to track down the man who ruined his looks.',
                    'ShowTags' => ['dark', 'violent'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2016,
                    'ImdbRating' => 8
                ]
            ],
            'it' => [
                'title' => 'IT',
                'fields' => [
                    'Genre' => ['Horror'],
                    'Description' => 'In the summer of 1989, a group of bullied kids band together to destroy a shape-shifting monster, which disguises itself as a clown and preys on the children of Derry, their small Maine town.',
                    'ShowTags' => ['dark', 'scary'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2017,
                    'ImdbRating' => 7
                ]
            ],
            'Terrorism Close Calls' => [
                'title' => 'Terrorism Close Calls',
                'fields' => [
                    'Genre' => ['Crime'],
                    'Description' => 'The war on terror is everywhere and anywhere. In this series, we learn about the deadly terrorist attacks that almost happened or were not as deadly as planned.',
                    'ShowTags' => ['Provocative'],
                    'ShowTypes' => 'documentaries',
                    'ReleaseDate' => 2018,
                    'ImdbRating' => 5
                ]
            ],
            'World War Z' => [
                'title' => 'World War Z',
                'fields' => [
                    'Genre' => ['Action', 'Adventure', 'Horror'],
                    'Description' => 'Former United Nations employee Gerry Lane traverses the world in a race against time to stop a zombie pandemic that is toppling armies and governments and threatens to destroy humanity itself.',
                    'ShowTags' => ['Violent', 'Scary'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2013,
                    'ImdbRating' => 7
                ]
            ],
            'Kengan Ashura' => [
                'title' => 'Kengan Ashura',
                'fields' => [
                    'Genre' => ['Action'],
                    'Description' => 'Kazuo Yamashita is an ordinary fifty-six year old man who one day is summoned by the chairman of his company and hired as a manager of a gladiator.',
                    'ShowTags' => ['Violent', 'Exciting'],
                    'ShowTypes' => 'anime',
                    'ReleaseDate' => 2019,
                    'ImdbRating' => 8
                ]
            ],
            'Dirty Money' => [
                'title' => 'Dirty Money',
                'fields' => [
                    'Genre' => ['Social and Cultural'],
                    'Description' => 'A Netflix Original Series documenting various stories about exposing the greed, corruption, and crime spreading through the global economy.',
                    'ShowTags' => ['Provocative', 'Cerebral'],
                    'ShowTypes' => 'documentaries',
                    'ReleaseDate' => 2018,
                    'ImdbRating' => 8
                ]
            ],
            'Conspiracy' => [
                'title' => 'Conspiracy',
                'fields' => [
                    'Genre' => ['Political'],
                    'Description' => 'History presents us with an accepted view of past events but there are often dissenting voices.',
                    'ShowTags' => ['Provocative', 'Scandalous'],
                    'ShowTypes' => 'documentaries',
                    'ReleaseDate' => 2015,
                    'ImdbRating' => 6
                ]
            ],
            'House of cards' => [
                'title' => 'House of cards',
                'fields' => [
                    'Genre' => ['Drama', 'Political'],
                    'Description' => 'A Congressman works with his equally conniving wife to exact revenge on the people who betrayed him.',
                    'ShowTags' => ['Dark', 'Cerebral'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDate' => 2013,
                    'ImdbRating' => 8
                ]
            ],
            'Man of Steel' => [
                'title' => 'Man of Steel',
                'fields' => [
                    'Genre' => ['Action', 'Adventure', 'Sci-Fi'],
                    'Description' => 'An alien child is evacuated from his dying world and sent to Earth to live among humans. His peace is threatened, when survivors of his home planet invade Earth.',
                    'ShowTags' => ['Exciting', 'Superhero'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2013,
                    'ImdbRating' => 7
                ]
            ],
            'Gods of Egypt' => [
                'title' => 'Gods of Egypt',
                'fields' => [
                    'Genre' => ['Action', 'Adventure', 'Fantasy'],
                    'Description' => 'Mortal hero Bek teams with the god Horus in an alliance against Set, the merciless god of darkness, who has usurped Egypt\'s throne, plunging the once peaceful and prosperous empire.',
                    'ShowTags' => ['Exciting'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2016,
                    'ImdbRating' => 5
                ]
            ],
            'Merlin' => [
                'title' => 'Merlin',
                'fields' => [
                    'Genre' => ['Action', 'Adventure', 'Fantasy'],
                    'Description' => 'These are the brand new adventures of Merlin, the legendary sorcerer as a young man, when he was just a servant to young Prince Arthur on the royal court of Camelot, who has soon become his best friend, and turned Arthur into a great king and a legend.',
                    'ShowTags' => ['Exciting', 'Suspenseful'],
                    'ShowTypes' => 'tv-series',
                    'ReleaseDate' => 2012,
                    'ImdbRating' => 7
                ]
            ],
            'Out of Thin Air' => [
                'title' => 'Out of Thin Air',
                'fields' => [
                    'Genre' => ['Crime', 'Suspenseful'],
                    'Description' => 'Iceland, 1976. Six suspects confess to two violent crimes.',
                    'ShowTags' => ['Chilling'],
                    'ShowTypes' => 'documentaries',
                    'ReleaseDate' => 2017,
                    'ImdbRating' => 6
                ]
            ],
            'The Great Gatsby' => [
                'title' => 'The Great Gatsby',
                'fields' => [
                    'Genre' => ['Social and Cultural', 'Drama', 'Romance'],
                    'Description' => 'A writer and wall street trader, Nick, finds himself drawn to the past and lifestyle of his millionaire neighbor, Jay Gatsby.',
                    'ShowTags' => ['Scandalous'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2013,
                    'ImdbRating' => 7
                ]
            ],
            'The Kissing Booth' => [
                'title' => 'The Kissing Booth',
                'fields' => [
                    'Genre' => ['Comedy', 'Romance'],
                    'Description' => 'A high school student is forced to confront her secret crush at a kissing booth.',
                    'ShowTags' => ['quirky'],
                    'ShowTypes' => 'films',
                    'ReleaseDate' => 2018,
                    'ImdbRating' => 6
                ]
            ],
        ];

        foreach ($entries as $slug => $entry) {

            $slug = ElementHelper::createSlug(static::PREFIX . $slug);

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
                        $elementSlug = ElementHelper::createSlug(static::PREFIX . $fieldSlug);

                        $elementObj = $fieldElement['element']::find()->where(['slug' => $elementSlug])->one();
                        if (!$elementObj) {
                            $fieldElementObj          = new $fieldElement['element'];
                            $fieldElementObj->title   = ucwords($fieldSlug);
                            $fieldElementObj->slug    = $elementSlug;
                            $fieldElementObj->groupId = $fieldElement['groupId'];
                            Craft::$app->getElements()->saveElement($fieldElementObj);

                            $fieldIds[] = $fieldElementObj->id;
                        } else {
                            $fieldIds[] = $elementObj->id;
                        }
                    }

                    $element->setFieldValue($filedHandle, $fieldIds);
                } else {
                    $element->setFieldValue($filedHandle, $field);
                }
            }

            Craft::$app->getElements()->saveElement($element);

            if ($element->hasErrors()) {
                Craft::dd($element->getErrors());
            }
        }
    }

    public function isElement($handle)
    {
         if (in_array($handle, [
           'Genre'
            ])) {
            return ['element' => Category::class, 'groupId' => $this->categoryGroupId];
         }

         if (in_array($handle, [
           'ShowTags'
            ])) {
            return ['element' => TagElement::class, 'groupId' => $this->tagGroupId];
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
            "columnType"  => "text",
            "searchable" => true
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
            "searchable" => true,
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
        for ($i=2011; $i<=2025; $i++) {
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
            'settings' => ['options' => $options],
            "searchable" => true
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
            'settings' => ['options' => $options],
            "searchable" => true
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
