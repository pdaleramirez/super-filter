<?php

namespace pdaleramirez\superfilter\elements;

use Craft;
use craft\base\Element;
use craft\behaviors\FieldLayoutBehavior;
use craft\elements\actions\Delete;
use craft\elements\Asset;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use Doctrine\ORM\Tools\Setup;
use pdaleramirez\superfilter\elements\db\SetupSearchQuery;
use pdaleramirez\superfilter\records\SetupSearch as SetupSearchRecord;
use yii\base\Exception;

/**
 * Class SetupSearch
 * @package pdaleramirez\superfilter\elements
 */
class SetupSearch extends Element
{

    /**
     * @var array
     */
    public $fields;
    public $options;
    public $sorts;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('super-filter', 'Setup Search');
    }

    /**
     * @inheritdoc
     */
    public function getIsEditable(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasContent(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => Craft::t('super-filter', 'All Setups')
            ]
        ];

        return $sources;
    }

    protected static function defineTableAttributes(): array
    {
        $attributes = [
            'title' => ['label' => Craft::t('super-filter', 'Title')],
            'dateCreated' => ['label' => Craft::t('super-filter', 'Date Created')],
            'edit' => ['label' => ''],
        ];

        return $attributes;
    }

    public function getTableAttributeHtml(string $attribute): string
    {
        if ($attribute === 'edit') {
            $link = UrlHelper::cpUrl(
                'super-filter/setup-search/edit/'.$this->id
            );

            return "<a href='$link'>Edit</a>";
        }


        return parent::getTableAttributeHtml($attribute);
    }

    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        $attributes = [
            'elements.dateCreated' => Craft::t('super-filter', 'Date Created')
        ];

        return $attributes;
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new SetupSearchQuery(static::class);
    }

    /**
     * @param bool $isNew
     *
     * @throws \Exception
     */
    public function afterSave(bool $isNew)
    {
        /**
         * @var $record SetupSearchRecord
         */
        // Get the entry record
        if (!$isNew) {
            $record = SetupSearchRecord::findOne($this->id);

            if (!$record) {
                throw new Exception('Invalid setup ID: '.$this->id);
            }
        } else {
            $record = new SetupSearchRecord();
            $record->id = $this->id;
        }

        $record->dateCreated = $this->dateCreated;
        $record->dateUpdated = $this->dateUpdated;

        $record->save(false);

        // Update the entry's descendants, who may be using this entry's URI in their own URIs
        Craft::$app->getElements()->updateElementSlugAndUri($this);

        parent::afterSave($isNew);
    }

    /**
     * @return \craft\models\FieldLayout|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getFieldLayout()
    {
        $behaviors = $this->getBehaviors();

        /**
         * @var FieldLayoutBehavior $fieldLayout
         */
        $fieldLayout = $behaviors['fieldLayout'];

        return $fieldLayout->getFieldLayout();
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'fieldLayout' => [
                'class' => FieldLayoutBehavior::class,
                'elementType' => self::class
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected static function defineActions(string $source = null): array
    {
        $actions = [];

        // Delete
        $actions[] = Craft::$app->getElements()->createAction([
            'type' => Delete::class,
            'confirmationMessage' => Craft::t('super-filter', 'Are you sure you want to delete the selected setups?'),
            'successMessage' => Craft::t('super-filter', 'Setups deleted.'),
        ]);

        return $actions;
    }
}
