<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\helpers\Json;
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

        $alias = Craft::getAlias('@superfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

       // $siteTemplatePath = Craft::$app->path->getSiteTemplatesPath();

        //Craft::$app->getView()->setTemplatesPath($siteTemplatePath);

        $params = [
            'handle'      => 'entry',
            //'section'     => 'blog',
            'currentPage' => Craft::$app->getRequest()->getPageNum() ?? 1,
            'category'    =>  Craft::$app->getRequest()->get('category'),
            'limit'       => SuperFilter::$app->getPageSize()
        ];

        SuperFilter::$app->setParams($params);
        //\Craft::dd(SuperFilter::$app->getParams());
        $html = Craft::$app->getView()->renderTemplate('entries', ['config' => Json::encode($params)]);


        return Template::raw($html);
    }


    public function getLinks()
    {
        $filter = SuperFilter::$app->config(SuperFilter::$app->getParams());

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
