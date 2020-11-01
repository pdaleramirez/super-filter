<?php

namespace pdaleramirez\superfilter\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Category;
use craft\fields\Categories as CraftCategories;
use craft\helpers\ElementHelper;
use craft\helpers\Template;
use pdaleramirez\superfilter\base\ElementSearchField;
use pdaleramirez\superfilter\SuperFilter;

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

    public function setObject(FieldInterface $object)
    {
        parent::setObject($object);

        $limit = $object->branchLimit;

        if ($limit == 1) {
            $this->initValue = '';
        }
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function getHtml()
    {
        $elements = $this->getElements();

        $limit = $this->object->branchLimit;

        $inputTemplate = 'categorycheckbox';

        if ($limit == 1) {
            $inputTemplate = 'elementdropdown';
        }

        $categories = $this->getElementQuery()->level(1)->all();

        $html = '';
        $categoryHtml = $this->getCategoriesHtml($categories, $html);

        $template = SuperFilter::$app->searchTypes->getTemplate('fields/' . $inputTemplate);

        return Craft::$app->getView()->renderTemplate($template, [
            'field' => $this,
            'elements' => $elements,
            'categories' => $categories,
            'value' => $this->value,
            'object' => $this->object,
            'categoryHtml' => Template::raw($categoryHtml)
        ]);
    }

    /**
     * @param Category[]|ElementInterface[] $categories
     * @param $html
     * @return string
     */
    public function getCategoriesHtml(array $categories, &$html)
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
            $html.= '<li> <input v-model="config.params.fields.' . $this->object->handle . '" type="checkbox" ' . $checked . '  
                                 name="fields[' . $this->object->handle . '][]" 
                                   value="' . $category->id . '" /> ' . $category->title;
            $children = $category->getChildren();

            if ($children) {
                $this->getCategoriesHtml($children->all(), $html);
            }
            $html.= '</li>';
        }
        $html.= '</ul>';

        return $html;
    }

	public function getRelated($value)
	{
		$elementType = static::elementType();

		$elements = $elementType::findAll($value);

        $operator = SuperFilter::$app->getSettings()->categoryOperator;
        $operator = strtolower($operator);

		return array_merge([$operator], $elements);
	}
}
