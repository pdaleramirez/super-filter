<?php

namespace pdaleramirez\superfilter\base;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\fields\Categories as CraftCategories;
use craft\fields\Dropdown as DropdownCraft;
use craft\helpers\ElementHelper;
use yii\db\QueryInterface;

abstract class OptionSearchField extends SearchField
{
    use PlainSearchTrait;
    public function getOptions()
    {
        /**
         * @var $object DropdownCraft
         */
        $object = $this->object;

        if (count($object->options)> 0) {
            foreach ($object->options as $key => $option) {
                $selected = false;
                $value = $this->value ?? null;
                if ($value && isset($option['value']) && $option['value'] == $value) {
                    $selected = true;
                }

                $object->options[$key]['selected'] = $selected;
            }
        }

        return $object->options;
    }

    public function getQueryParams(QueryInterface $query, $value)
    {
        return $query;
    }

    public function getInitValue()
    {
        return '';
    }

    public function getSearchFieldsInfo(): array
    {
        return [
            'options' => $this->getOptions(),
            'value' => ""
        ];
    }
}
