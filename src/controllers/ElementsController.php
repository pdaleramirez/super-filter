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
      protected $allowAnonymous = ['get-elements'];

    public function actionGetElements()
    {

        $params = Craft::$app->getRequest()->getBodyParams();

        $items = SuperFilter::$app->config($params)->items();

        $response = [
            'items'  => $items,
            'params' => SuperFilter::$app->getParams()
        ];

        return Json::encode($response);
    }
}
