<?php

namespace pdaleramirez\superfilter\records;

use Craft;
use craft\records\Element;
use craft\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;

/**
 * @property $id
 * @property $handle
 * @property $elementSearchType
 * @property $options
 * @property $sorts
 * @property $items
 * Class SetupSearch
 * @package pdaleramirez\superfilter\records
 */
class SetupSearch extends ActiveRecord
{

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        $query = parent::find()
            ->innerJoinWith(['element element']);

        // todo: remove schema version condition after next beakpoint
        $schemaVersion = Craft::$app->getInstalledSchemaVersion();
        if (version_compare($schemaVersion, '3.1.19', '>=')) {
            $query->where(['element.dateDeleted' => null]);
        }

        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public static function findWithTrashed(): ActiveQuery
    {
        return static::find()->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public static function findTrashed(): ActiveQuery
    {
        return static::find()->where(['not', ['element.dateDeleted' => null]]);
    }
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%superfilter_setup_search}}';
    }

    /**
     * Returns the entry’s element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }
}
