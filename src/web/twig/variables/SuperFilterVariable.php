<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\helpers\Template;
use Exception;
use pdaleramirez\superfilter\services\SearchTypes;
use pdaleramirez\superfilter\SuperFilter;
use pdaleramirez\superfilter\web\assets\VueAsset;

class SuperFilterVariable
{
    /**
     * @var $searchSetupService SearchTypes
     */
    private $searchSetupService;

    public function setup($handle)
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $this->searchSetupService = SuperFilter::$app->searchTypes->getSearchSetup($handle);
    }

    public function getItems()
    {
        $items   = $this->searchSetupService->getItems();

        $config  = $this->searchSetupService->getConfig();

        $options = $config['options'];

        $template = $options['template'] ?? null;

        if ($template) {
            $alias = Craft::getAlias('@superfilter/templates');

            if (!SuperFilter::$app->isEntryTemplateIn($template)) {
                $siteTemplatesPath = Craft::$app->path->getSiteTemplatesPath();

                Craft::$app->getView()->setTemplatesPath($siteTemplatesPath);

            } else {
                Craft::$app->getView()->setTemplatesPath($alias);
            }

            $entryHtml = Craft::$app->getView()->renderTemplate('style/' . $template, [
                'items' => $items
            ]);

            return Template::raw($entryHtml);
        }

        return null;
    }

    public function getPaginateLinks()
    {
        if ($this->searchSetupService === null) {
            throw new Exception('Need to call craft.superFilter.setup(\'handle\') to get results.');
        }

        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $alias = Craft::getAlias('@superfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

        $html = Craft::$app->getView()->renderTemplate('pagination', [
            'pageInfo' => $this->searchSetupService->getLinks()
        ]);

        return Template::raw($html);

    }
}
