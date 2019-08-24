<?php
namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use Craft;
use craft\base\Element;
use craft\db\Paginator;
use craft\elements\Category;
use craft\web\twig\variables\Paginate;

class App extends Component
{
    static $pageSize = 5;
    protected $elements = [];
    protected $links;
    protected $elementQuery;

	public function config($params)
	{
        $handle = $params['handle'];

        $categoryId   = $params['categoryId'];

        $limit = $params['limit'] ?? static::$pageSize;

        $elementsService = Craft::$app->getElements();

        /**
         * @var $elementClass Element
         */
        $elementClass = $elementsService->getElementTypeByRefHandle($handle);

        $category = null;
        $elementQuery = $elementClass::find();

        if ($categoryId) {
            $category = Category::findOne($categoryId);
        }

        if ($category == null && $categoryId != null) {
            $category = Category::findOne(['slug' => $categoryId]);
        }

        if ($category) {
            $elementQuery = $elementQuery->relatedTo($category);
        }

        $this->elementQuery = $elementQuery;

        if ($category == null && $categoryId != null) {
           return [];
        }

        $currentPage = $params['currentPage'] ?? Craft::$app->getRequest()->getPageNum();

        $paginatorParams =  [
            'currentPage' => $currentPage
        ];

        if ($limit && $limit != "*") {
            $paginatorParams['pageSize'] = $limit;
        }

        $paginator = new Paginator($elementQuery, $paginatorParams);

        $this->links = Paginate::create($paginator);

        $items = $paginator->getPageResults();

        if ($items) {
            /**
             * @var $item Element
             */
            foreach ($items as $item) {
                $fieldValues = $item->getFieldValues();
                $fieldValues = array_merge($fieldValues, ['title' => $item->title]);
                $this->elements[] = $fieldValues;
            }
        }

        return $this;
	}

	public function items()
    {
        return $this->elements;
    }

    /**
     * @return Paginate
     */
    public function links()
    {
        return $this->links;
    }

    public function query()
    {
        return $this->elementQuery;
    }
}