<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\helpers\Json;
use craft\helpers\Template;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\services\App;
use pdaleramirez\superfilter\SuperFilter;
use pdaleramirez\superfilter\web\assets\VueAsset;

class SuperFilterVariable
{

    public function getVueJs($id = null)
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $alias = Craft::getAlias('@superfilter/templates');

        $setupSearch = SuperFilter::$app->searchTypes->getSearchSetup($id);

        $options = $setupSearch->element()->options();
        $items  = $setupSearch->element()->items();

        $settings = SuperFilter::$app->getSettings();

        $entryTemplate = $options['entryTemplate'] ?? null;

        if ($entryTemplate == null) {
            $settings->entryTemplate = App::DEFAULT_TEMPLATE;
        }

        if (!SuperFilter::$app->isEntryTemplateIn($entryTemplate)) {
            $siteTemplatesPath = Craft::$app->path->getSiteTemplatesPath();

            Craft::$app->getView()->setTemplatesPath($siteTemplatesPath);

        } else {
            $entryTemplate = 'entry/' . $entryTemplate;

            Craft::$app->getView()->setTemplatesPath($alias);
        }

        $entryHtml = Craft::$app->getView()->renderTemplate($entryTemplate);

        $entryHtml = Template::raw($entryHtml);;

        $params = [
            'handle'      => 'entry',
            //'section'     => 'blog',
            'currentPage' => Craft::$app->getRequest()->getPageNum() ?? 1,
            'category'    => Craft::$app->getRequest()->get('category'),
            'limit'       => SuperFilter::$app->getPageSize()
        ];

        // To get all config on all variable method.
        SuperFilter::$app->setParams($params);

        Craft::$app->getView()->setTemplatesPath($alias);

        $html = Craft::$app->getView()->renderTemplate('entries', [
            'config'     => Json::encode($params),
            'entryHtml' => $entryHtml
        ]);


        return Template::raw($html);
    }

    public function getItems($id)
    {
        $searchSetup = SuperFilter::$app->searchTypes->getSearchSetup($id);

        $element = $searchSetup->element();

        $items = SuperFilter::$app->searchTypes->getItems();

        $options = $element->options();
        \Craft::dd();
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

    public function pagination()
    {
        $html = Craft::$app->getView()->renderTemplate('vue/pagination');

        return Template::raw($html);
    }
}
