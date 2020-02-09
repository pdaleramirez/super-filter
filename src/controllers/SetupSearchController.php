<?php

namespace pdaleramirez\superfilter\controllers;

use craft\base\Element;
use craft\fields\Number;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use Craft;
use pdaleramirez\superfilter\elements\SetupSearch;
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
        Craft::$app->getView()->registerAssetBundle(FontAwesomeAsset::class);
        Craft::$app->getView()->registerAssetBundle(VueCpAsset::class, 1);

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

        $options = $setupElement->options;
        if ($options !== '') {
            $options = Json::decodeIfJson($options);
        }

        $items = $setupElement->items;

        if ($items !== '') {
            $items = Json::decodeIfJson($items);
        }

        $baseUrl = UrlHelper::actionUrl('/');

        return $this->renderTemplate('super-filter/setupsearch/_edit', [
            'setupElement' => $setupElement,
            'baseUrl' => $baseUrl,
            'options' => $options,
            'items' => $items,
            'continueEditingUrl' => 'super-filter/setup-search/edit/{id}'
        ]);
    }

    /**
     * @return \yii\web\Response|null
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveSetup()
    {
        $this->requirePostRequest();

        $setupElement = new SetupSearch();

        $setupElement->id = Craft::$app->getRequest()->getBodyParam('setupId');

        if ($setupElement->id) {
            $setupElement = Craft::$app->getElements()->getElementById($setupElement->id, SetupSearch::class);
        }

        $setupElement->title = Craft::$app->getRequest()->getBodyParam('title');
        $setupElement->handle = Craft::$app->getRequest()->getBodyParam('handle');
        $postItems = Craft::$app->getRequest()->getBodyParam('items');

        $items = SuperFilter::$app->searchTypes->setSelectedItems($postItems);
        $setupElement->items = $items;

        $setupElement->options = Craft::$app->getRequest()->getBodyParam('options');
        $setupElement->elementSearchType = Craft::$app->getRequest()->getBodyParam('elementSearchType');

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

    /**
     * @return \yii\web\Response|null
     * @throws \Throwable
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDeleteSetup()
    {
        $request = Craft::$app->getRequest();
        $setupId = $request->getRequiredBodyParam('setupId');

        $element = Craft::$app->getElements()->getElementById($setupId, SetupSearch::class);
        if (!Craft::$app->getElements()->deleteElement($element)) {
            if ($request->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }

            Craft::$app->getSession()->setError(Craft::t('super-filter', 'Couldn’t delete setup.'));

            // Send the entry back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'setupElement' => $element
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('super-filter', 'Setup deleted.'));

        return $this->redirectToPostedUrl($element);
    }

    public function actionSetupOptions()
    {
        $id = (int)Craft::$app->getRequest()->getBodyParam('id');

        $setup = null;
        if ($id) {
            $setup = SetupSearch::findOne($id);

            $options = Json::decodeIfJson($setup->options);
        }

        $items = SuperFilter::$app->searchTypes->getItemFormat($setup);
        $templateOptions = SuperFilter::$app->getTemplateOptions();
        return $this->asJson([
            'items' => $items,
            'template' => $options['template'] ?? null,
            'templateOptions' => $templateOptions,
            'initSort' => $options['initSort'] ?? null
        ]);
    }
}
