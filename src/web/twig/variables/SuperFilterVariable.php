<?php

namespace pdaleramirez\superfilter\web\twig\variables;

use Craft;
use craft\elements\db\CategoryQuery;
use craft\elements\db\ElementQuery;
use craft\elements\Entry;
use craft\fields\data\MultiOptionsFieldData;
use craft\helpers\ArrayHelper;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\web\View;
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
    private $prevTemplate;

    /**
     * @param $handle
     * @param array $options
     * @return \Twig\Markup
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setup($handle, array $options = [])
    {
        $this->prevTemplate = Craft::$app->view->getTemplatesPath();

        $config = SuperFilter::$app->searchTypes->getConfigById($handle);
        $config['currentPage'] = Craft::$app->getRequest()->getPageNum();
        $config['params'] = Craft::$app->getRequest()->getQueryParams();

        $fieldParam = Craft::$app->getRequest()->getQueryParam(SuperFilter::$app->getSettings()->prefixParam);

        if ($fieldParam) {
            $config['params']['fields'] = $fieldParam;
        }

        if (count($options) > 0) {
        	$keys = array_keys($options);

        	if (!in_array('filter', $keys) && !in_array('attributes', $keys)) {
                $message = "Parameter options format should have filter or attribute keys
        		E.g.
				{
				  filter: { superFilterImdbRating: 6 },
				  attributes: ['title', 'superFilterGenre', 'dateCreated']
				}
    ";
        		throw new \yii\base\Exception($message);
			}
		}

		$preFilter = $options['filter'] ?? [];
        if (count($preFilter) > 0) {
            if (isset($config['params']['fields'])) {
                $config['params']['fields'] = array_merge($config['params']['fields'], $preFilter);
            } else {
                $config['params']['fields'] = $preFilter;
            }

			$config['params']['preFilter'] = $preFilter;
        }

        $currentSite = Craft::$app->sites->getCurrentSite();
        $primarySite = Craft::$app->sites->getPrimarySite();
        if ($currentSite !== null && $currentSite->id !== $primarySite->id) {
            $config['params']['siteId'] = $currentSite->id;
        }

        $optionSiteId = $options['siteId'] ?? null;
        if ($optionSiteId !== null) {
			$config['params']['siteId'] = $optionSiteId;
		}

        $this->searchSetupService = SuperFilter::$app->searchTypes->setSearchSetup($config);

        return $this->renderTemplate('setup', [
            'handle' => $handle,
            'params' => $config['params'],
            'options' => $config['options'],
			'itemAttributes' => $options['attributes'] ?? []
        ]);
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

        if ($selected == null) {
            $config  = $this->searchSetupService->getConfig();

            $initSort = $config['options']['initSort'] ?? null;

            if ($initSort) {
                $selected = $initSort;
            }
        }

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
        $template = $this->searchSetupService->getTemplate($file);

        $config  = $this->searchSetupService->getConfig();

        $options = $config['options'];

        $html = Craft::$app->getView()->renderTemplate($template, array_merge($variables, [
            'options'  => $options
        ]));

        return Template::raw($html);
    }

    public function close()
    {
        Craft::$app->view->setTemplatesPath($this->prevTemplate);
    }

    public function getFrontendAssets()
    {
        return SuperFilter::getInstance()->getTemplates()->getFrontendAssets();
    }

    /**
     * @param $handle
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function render($handle): string
    {
        Craft::$app->getView()->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html =  Craft::$app->getView()->renderTemplate('super-filter/twig/render', [
            'handle' => $handle
        ]);

        Craft::$app->getView()->setTemplateMode(View::TEMPLATE_MODE_SITE);

        return Template::raw($html);
    }
}
