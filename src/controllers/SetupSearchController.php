<?php
namespace pdaleramirez\superfilter\controllers;

use craft\elements\Entry;
use craft\errors\InvalidPluginException;
use craft\helpers\Json;
use craft\web\Controller;
use Craft;
use pdaleramirez\superfilter\models\Settings;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\services\App;
use pdaleramirez\superfilter\SuperFilter;
use pdaleramirez\superfilter\web\assets\FontAwesomeAsset;
use pdaleramirez\superfilter\web\assets\VueCpAsset;

class SetupSearchController extends Controller
{
    /**
     * @param null $setupId
     * @param SetupSearch|null $setupElement
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionEdit($setupId = null, SetupSearch $setupElement = null)
    {
        if ($setupElement == null) {
            if ($setupId == 'new') {
                $setupElement = new SetupSearch();
            } else {
                $setupElement = Craft::$app->getElements()->getElementById($setupId, SetupSearch::class);
            }

            if ($setupElement == null) {
                throw new \Exception("Invalid setup page");
            }
        }

        return $this->renderTemplate('super-filter/setupsearch/_edit', [
            'setupElement' => $setupElement,
            'continueEditingUrl' => 'super-filter/setup-search/edit/{id}'
        ]);
    }

    public function actionSaveSetup()
    {
        $this->requirePostRequest();

        $setupElement = new SetupSearch();

        $setupElement->id = Craft::$app->getRequest()->getBodyParam('setupId');

        if ($setupElement->id) {
            $setupElement = Craft::$app->getElements()->getElementById($setupElement->id, SetupSearch::class);
        }

        $setupElement->title = Craft::$app->getRequest()->getBodyParam('title');

        if (!Craft::$app->getElements()->saveElement($setupElement)) {
            Craft::$app->getSession()->setError(Craft::t('super-filter', 'Unable to save setup.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'setupElement' => $setupElement
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('super-filter', 'Setup saved.'));

        return $this->redirectToPostedUrl($setupElement);
    }
}
