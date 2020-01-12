<?php

namespace pdaleramirez\superfilter\fields;

use function Arrayy\create;
use Craft;
use craft\elements\db\ElementQuery;
use craft\fields\Dropdown as DropdownCraft;
use pdaleramirez\superfilter\base\SearchField;
use yii\db\QueryInterface;

class Dropdown extends SearchField
{
    public function fieldType()
    {
        return DropdownCraft::class;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        return Craft::$app->getView()->renderTemplate($template . '/fields/dropdown',
            [
               'field' => $this,
               'options' => $this->getOptions()
            ]);
    }

    private function getOptions()
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

    public function getSearchParams($value)
    {
        $handle = $this->object->handle;

        return "$handle::'$value'";
    }
}
