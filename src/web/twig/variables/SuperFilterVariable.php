<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\helpers\Template;
use pdaleramirez\superfilter\SuperFilter;
use pdaleramirez\superfilter\web\assets\VueAsset;

class SuperFilterVariable
{
    /**
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\InvalidConfigException
     */
    public function getVueJs()
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $category = Craft::$app->getRequest()->get('category');

        $alias = Craft::getAlias('@superfilter/templates');

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

        $filter = SuperFilter::$app->config($params);

//
//        $paginator = new Paginator($filter->query(), [
//            'currentPage' => Craft::$app->getRequest()->getPageNum(),
//            'pageSize' => 1
//        ]);
//
//        if ($filter) {
//            \Craft::dd($paginator->getPageResults());
//        }

        $alias = Craft::getAlias('@superfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

        $html = Craft::$app->getView()->renderTemplate('pagination', [
            'pageInfo' => $filter->links()
        ]);

        return Template::raw($html);
    }
}