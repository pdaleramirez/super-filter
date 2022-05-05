<?php

namespace pdaleramirez\superfilter\elements;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\behaviors\FieldLayoutBehavior;
use craft\elements\actions\Delete;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;
use pdaleramirez\superfilter\elements\db\SetupSearchQuery;
use pdaleramirez\superfilter\records\SetupSearch as SetupSearchRecord;
use pdaleramirez\superfilter\SuperFilter;
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
    public $handle;
    public $sorts;
    public $elementSearchType;
    public $options;
    public $items;
    public $baseTemplate;

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
            'dateCreated' => ['label' => Craft::t('super-filter', 'Date Created')]
        ];

        return $attributes;
    }

    public function getCpEditUrl(): null|string
    {
        return UrlHelper::cpUrl(
            'super-filter/setup-search/edit/'.$this->id
        );
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
    public function afterSave(bool $isNew): void
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

        $this->options['template'] = rtrim($this->options['template'], '/');

        $record->handle = $this->handle;
        $record->elementSearchType = $this->elementSearchType;
        $record->items       = $this->items;
        $record->options     = $this->options;
        $record->dateCreated = $this->dateCreated;
        $record->dateUpdated = $this->dateUpdated;

        $record->save(false);

        // Update the entry's descendants, who may be using this entry's URI in their own URIs
        Craft::$app->getElements()->updateElementSlugAndUri($this);

        parent::afterSave($isNew);
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

    /**
     * @inheritdoc
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['elementSearchType', 'handle', 'options'], 'required'];
        $rules[] = ['options', 'validateOptions'];
        $rules[] = [
            ['handle'],
            HandleValidator::class
        ];

        $rules[] = [
            ['handle'],
            UniqueValidator::class,
            'targetClass' => SetupSearchRecord::class,
            'targetAttribute' => ['handle']
        ];


        return $rules;
    }

    public function validateOptions()
    {
        if (trim($this->options['perPage']) === 0 || trim($this->options['perPage']) === '') {
            $this->addError('options.perPage', 'Per Page input is required.');
        } else {
            $perPage = (int) $this->options['perPage'];

            if (!is_int($perPage) || $perPage <= 0) {
                $this->addError('options.perPage', 'Items per page should be a valid number.');
            }
        }

        if (trim($this->options['template']) === '') {
            $this->addError('options.template', 'Template override path is required.');
        }
    }

    public function options()
    {
        return Json::decodeIfJson($this->options);
    }

    public function items()
    {
        return Json::decodeIfJson($this->items);
    }

    public function sorts()
    {
        return Json::decodeIfJson($this->sorts) ?? [];
    }
}
