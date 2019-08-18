<?php

namespace pdaleramirez\searchfilter\web\twig\variables;

use Craft;
use craft\db\Paginator;
use craft\helpers\Template;
use craft\web\twig\variables\Paginate;
use pdaleramirez\searchfilter\SearchFilter;
use pdaleramirez\searchfilter\web\assets\VueAsset;
use yii\db\Query;

class SearchFilterVariable
{
    /**
     * @return \Twig\Markup|\Twig_Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\InvalidConfigException
     */
    public function getVueJs()
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $category = Craft::$app->getRequest()->get('category');

        $alias = Craft::getAlias('@searchfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

        $html = Craft::$app->getView()->renderTemplate('entries', [
            'currentPage' => Craft::$app->getRequest()->getPageNum(),
            'category'    => $category
        ]);

        return Template::raw($html);
    }


    public function getLinks()
    {
        $params = [
            'handle' => 'entry',
            'categoryId'  => Craft::$app->getRequest()->getBodyParam('category'),
            'limit'       => Craft::$app->getRequest()->getBodyParam('limit')
        ];

        $filter = SearchFilter::$app->config($params);

//
//        $paginator = new Paginator($filter->query(), [
//            'currentPage' => Craft::$app->getRequest()->getPageNum(),
//            'pageSize' => 1
//        ]);
//
//        if ($filter) {
//            \Craft::dd($paginator->getPageResults());
//        }

        $alias = Craft::getAlias('@searchfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

        $html = Craft::$app->getView()->renderTemplate('pagination', [
            'pageInfo' => $filter->links()
        ]);

        return Template::raw($html);
    }
}