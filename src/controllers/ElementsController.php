<?php
namespace pdaleramirez\superfilter\controllers;

use craft\db\Paginator;
use craft\elements\Entry;
use craft\helpers\Json;
use craft\web\Controller;
use Craft;
use pdaleramirez\superfilter\SuperFilter;

class ElementsController extends Controller
{
    protected $allowAnonymous = ['get-fields', 'filter'];

    public function actionGetFields()
    {
        $handle = Craft::$app->getRequest()->getBodyParam('handle');

        $searchSetupService = SuperFilter::$app->searchTypes;

        $config = $searchSetupService->getConfigById($handle);

        $searchSetupService->setSearchSetup($config);

        $config  = $searchSetupService->getConfig();

        $items = $config['items']['items'] ?? null;

        $fields = [];
        if ($items) {
            foreach ($items as $item) {
                $searchField = $searchSetupService->getSearchFieldObjectById($item['id']);

                $handle = $searchField->getObject()->handle;

                $fields[$handle] = $searchField->initValue;
            }
        }

       $config['params']['fields'] = $fields;

        return Json::encode([
          'config' => $config,
          'links'  => $searchSetupService->getLinks(),
          'items'  => $searchSetupService->getItemToArray()
        ]);
    }

    public function actionFilter()
    {
        $handle = Craft::$app->getRequest()->getBodyParam('handle');

        $searchSetupService = SuperFilter::$app->searchTypes;

        $config = $searchSetupService->getConfigById($handle);

       //$config['params'] = Craft::$app->getRequest()->getBodyParam('params');

        $requestConfig = Craft::$app->getRequest()->getBodyParam('config');

        $config = array_merge($config, $requestConfig);

        $fields = $config['params']['fields'] ?? null;

        if ($fields) {
            foreach ($fields as $handle => $field) {
                $fieldValue = is_string($field) ? trim($field) : $field;

                if (is_string($field) && $fieldValue === '') {
                   unset($config['params']['fields'][$handle]);
                }
            }
        }

        $searchSetupService->setSearchSetup($config);

        return Json::encode([
            'items'  => $searchSetupService->getItems(),
            'links'  => $searchSetupService->getLinks()
        ]);
    }
}
