<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
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
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function setup($handle)
    {
        Craft::$app->getView()->registerAssetBundle(VueAsset::class);

        $config = SuperFilter::$app->searchTypes->getConfigById($handle);
        $config['currentPage'] = Craft::$app->getRequest()->getPageNum();
        $config['params'] = Craft::$app->getRequest()->getQueryParams();

        $this->searchSetupService = SuperFilter::$app->searchTypes->setSearchSetup($config);

        $prevPath = Craft::$app->getView()->getTemplatesPath();

        $alias = Craft::getAlias('@superfilter/templates');

        Craft::$app->getView()->setTemplatesPath($alias);

        $entryHtml = Craft::$app->getView()->renderTemplate('setup', [
            'handle' => $handle
        ]);

        Craft::$app->getView()->setTemplatesPath($prevPath);

        return Template::raw($entryHtml);
    }

    public function getTemplate()
    {
        if ($this->template == null) {
            $this->template = $this->searchSetupService->getTemplate();
        }

        return $this->template;
    }

    /**
     * @return \Twig\Markup
     */
    public function getItems()
    {
        $items   = $this->searchSetupService->getItems();

        return $this->renderTemplate('items', [
            'items' => $items
        ]);
    }

    /**
     * @return \Twig\Markup
     * @throws Exception
     */
    public function getPaginateLinks()
    {
        if ($this->searchSetupService === null) {
            throw new Exception('Need to call craft.superFilter.setup(\'handle\') to get results.');
        }

        return $this->renderTemplate('pagination', [
            'pageInfo' => $this->searchSetupService->getLinks()
        ]);
    }

    public function displaySortOptions()
    {
        $sorts = $this->searchSetupService->getDisplaySortOptions();
        $params = Craft::$app->getRequest()->getQueryParams();
        $selected = $params['sort'] ?? null;

        return $this->renderTemplate('sorts', [
            'sorts'    => $sorts,
            'selected' => $selected
        ]);
    }

    /**
     * @return \Twig\Markup
     * @throws \yii\base\Exception
     */
    public function displaySearchFields()
    {
        return $this->renderTemplate('fields', [
            'fields'   => $this->searchSetupService->getSearchFieldsObjects()
        ]);
    }

    public function getSearchField($handle)
    {
        $fieldObj = Craft::$app->getFields()->getFieldByHandle($handle);

        $searchField = $this->searchSetupService->getSearchFieldByObj($fieldObj);

        return Template::raw($searchField->getHtml());
    }

    public function getSettings()
    {
        return SuperFilter::$app->getSettings();
    }

    private function renderTemplate($file, $variables = [])
    {
        $template = $this->getTemplate();

        $config  = $this->searchSetupService->getConfig();

        $options = $config['options'];

        $html = Craft::$app->getView()->renderTemplate($template . '/' . $file, array_merge($variables, [
            'options'  => $options
        ]));

        return Template::raw($html);
    }
}
