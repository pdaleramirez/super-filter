<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\elements\Category;
use craft\fields\Categories as CraftCategories;
use craft\helpers\ElementHelper;
use craft\helpers\Template;
use pdaleramirez\superfilter\base\ElementSearchField;

class Categories extends ElementSearchField
{
    public function fieldType()
    {
        return CraftCategories::class;
    }

    /**
     * @return string|ElementInterface
     */
    protected function elementType()
    {
        return Category::class;
    }

    public function getElementQuery()
    {
        /**
         * @var $object CraftCategories
         */
        $object = $this->object;
        $source = $object->source;

        $elementType = static::elementType();
        $find = ElementHelper::findSource($elementType, $source);
        $query = $elementType::find();

        $criteria = $find['criteria'] ?? null;
        if ($criteria) {
            Craft::configure($query, $criteria);
        }

        return $query;
    }

    public function getHtml()
    {
        $template = $this->config['template'];

        $elements = $this->getElements();

        $limit = $this->object->branchLimit;

        $inputTemplate = 'categorycheckbox';

        if ($limit == 1) {
            $inputTemplate = 'elementdropdown';
        }

        $categories = $this->getElementQuery()->level(1)->all();

        $html = '';
        $categoryHtml = $this->getCategoriesHtml($categories, $html);

        return Craft::$app->getView()->renderTemplate($template . '/fields/' . $inputTemplate, [
            'field' => $this,
            'elements' => $elements,
            'categoryHtml' => Template::raw($categoryHtml)
        ]);
    }

    /**
     * @param Category[]|ElementInterface[] $categories
     * @param $html
     * @return string
     */
    public function getCategoriesHtml($categories, &$html)
    {
        $html.= '<ul>';
        foreach ($categories as $category) {

            $selected = false;

            if (is_array($this->value)) {
                if (in_array($category->id, $this->value)) {
                    $selected = true;
                }
            } elseif ($this->value === $category->id) {
                $selected = true;
            }
            $checked = ($selected)? 'checked' : '';
            $html.= '<li> <input type="checkbox" ' . $checked . '  
                                 name="fields[' . $this->object->handle . '][]" 
                                   value="' . $category->id . '" /> ' . $category->title;
            $children = $category->getChildren();

            if ($children) {
                $this->getCategoriesHtml($children, $html);
            }
            $html.= '</li>';
        }
        $html.= '</ul>';

        return $html;
    }
}
