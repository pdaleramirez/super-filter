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
    private $template;

    /**
     * @param $handle
     * @throws \yii\base\InvalidConfigException
     */
    public function setup($handle)
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class, 1);

        $params = Craft::$app->getRequest()->getQueryParams();

        SuperFilter::$app->searchTypes->setParams($params);
        $this->searchSetupService = SuperFilter::$app->searchTypes->getSearchSetup($handle);
    }

    public function getTemplate()
    {
        if ($this->template == null) {
            $this->template = $this->searchSetupService->getTemplate();
        }

        return $this->template;
    }

    /**
     * @return \Twig\Markup|null
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function getItems()
    {
        $items   = $this->searchSetupService->getItems();

        $template = $this->getTemplate();

        $entryHtml = Craft::$app->getView()->renderTemplate($template . '/items', [
            'items' => $items
        ]);

        return Template::raw($entryHtml);
    }

    /**
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
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

    public function displaySortOptions()
    {
        $sorts = $this->searchSetupService->getDisplaySortOptions();

        $template = $this->getTemplate();

        $params = Craft::$app->getRequest()->getQueryParams();
        $selected = $params['sort'] ?? null;

        $entryHtml = Craft::$app->getView()->renderTemplate($template . '/sorts', [
            'sorts'    => $sorts,
            'selected' => $selected
        ]);

        return Template::raw($entryHtml);
    }

    public function displaySearchFields()
    {
        $template = $this->getTemplate();

        $params = Craft::$app->getRequest()->getQueryParams();
        $selected = $params['fields'] ?? null;

        $entryHtml = Craft::$app->getView()->renderTemplate($template . '/fields', [
            'fields'   => $this->searchSetupService->getSearchFieldsHtml(),
            'selected' => $selected
        ]);

        return Template::raw($entryHtml);
    }

    public function getSearchField($handle)
    {
        $fieldObj = Craft::$app->getFields()->getFieldByHandle($handle);
        $type = get_class($fieldObj);

        $searchField = $this->searchSetupService->getSearchFieldType($type);

        $template = $this->searchSetupService->getTemplate();

        $searchField->setConfig(['template' => $template]);

        return Template::raw($searchField->getHtml());
    }
}
