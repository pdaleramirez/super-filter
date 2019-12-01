<?php

namespace pdaleramirez\superfilter\records;

use craft\base\Element;
use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

/**
 * @property $id
 * @property $handle
 * @property $elementSearchType
 * @property $options
 * @property $sorts
 * @property $fields
 * Class SetupSearch
 * @package pdaleramirez\superfilter\records
 */
class SetupSearch extends ActiveRecord
{
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
