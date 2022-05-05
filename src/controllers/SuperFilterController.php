<?php
namespace pdaleramirez\superfilter\controllers;


use craft\errors\InvalidPluginException;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use Craft;
use pdaleramirez\superfilter\models\Settings;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\SuperFilter;
use pdaleramirez\superfilter\web\assets\FontAwesomeAsset;
use pdaleramirez\superfilter\web\assets\VueCpAsset;

class SuperFilterController extends Controller
{
    protected array|bool|int $allowAnonymous = ['filter'];
    /**
     * @return \yii\web\Response
     * @throws InvalidPluginException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSettings()
    {
        Craft::$app->getView()->registerAssetBundle(FontAwesomeAsset::class);
        Craft::$app->getView()->registerAssetBundle(VueCpAsset::class, 1);

        $plugin = Craft::$app->plugins->getPlugin('super-filter');

        if (!$plugin) {
            throw new InvalidPluginException($plugin->handle);
        }

        /**
         * @var $settings Settings
         */
        $settings = $plugin->getSettings();

        $selectedSidebarItem = Craft::$app->getRequest()->getSegment(3) ?? 'general';

        $templatePath = 'super-filter/settings/' . $selectedSidebarItem;

        return $this->renderTemplate($templatePath, [
            'settings' => $settings,
            'selectedSidebarItem' => $selectedSidebarItem
        ]);
    }

    /**
     * @return \yii\web\Response|null
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveSettingsConfig()
    {
        $postSettings = Craft::$app->getRequest()->getBodyParam('settings');

        $settings = $this->saveSettings($postSettings);
        if ($settings->hasErrors()) {
            Craft::$app->getSession()->setError(Craft::t('super-filter', 'Couldn’t save settings.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'settings' => $settings
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('super-filter', 'Settings saved.'));

        return $this->redirectToPostedUrl();
    }

    private function saveSettings($settings)
    {
        $plugin = Craft::$app->plugins->getPlugin('super-filter');
        // The existing settings
        $pluginSettings = $plugin->getSettings();

        $settings = $settings['settings'] ?? $settings;

        if (!$pluginSettings->validate()) {
            return $pluginSettings;
        }

        Craft::$app->getPlugins()->savePluginSettings($plugin, $settings);

        return $pluginSettings;
    }

    /**
     * @throws \Throwable
     * @throws \craft\errors\CategoryGroupNotFoundException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionInstallSampleData()
    {
        $this->requirePostRequest();

        SuperFilter::$app->sampleData->generateSampleData();

        return Json::encode(['success' => true]);
    }

    public function actionEdit($setupId = null, SetupSearch $setupElement = null)
    {
        if ($setupId == 'new') {
            $setupElement = new SetupSearch();
        }

        if (!$setupElement) {
            $setupElement = Craft::$app->getElements()->getElementById($setupId, SetupSearch::class);
        }

        return $this->renderTemplate('super-filter/setupsearch/_edit', [
            'setup' => $setupElement
        ]);
    }

    public function actionFilter()
    {
        $bodyParams = Craft::$app->request->getBodyParams();

        if (isset($bodyParams['p'])) {
            unset($bodyParams['p']);
        }

        if (isset($bodyParams['action'])) {
            unset($bodyParams['action']);
        }

        $csrf = Craft::$app->config->getGeneral()->csrfTokenName;

        if (isset($bodyParams[$csrf])) {
            unset($bodyParams[$csrf]);
        }

        $prefixParam = SuperFilter::$app->getSettings()->prefixParam;

        $fields = $bodyParams[$prefixParam] ?? null;

        if ($fields) {
            foreach ($fields as $handle => $field) {
                $fieldValue = is_string($field) ? trim($field) : $field;
                $bodyParams[$prefixParam][$handle] = $fieldValue;

                if (is_string($field) && $fieldValue === '') {
                    unset($bodyParams[$prefixParam][$handle]);
                }
                
                if (is_array($field)) {
                    foreach ($field as $key => $value) {
                        if ($value === '') {
                            unset($bodyParams[$prefixParam][$handle][$key]);
                        }
                    }
                }
            }

            if (isset($bodyParams['reset'])) {
                $bodyParams[$prefixParam] = [];
                $bodyParams['reset'] = null;
            }

            $querySort = Craft::$app->request->getQueryParam('sort');
            if ($querySort) {
                $bodyParams = array_merge(['sort' => $querySort], $bodyParams);
            }
        }
        
        $sort = $bodyParams['sort'] ?? null;
        $queryFields = Craft::$app->request->getQueryParam('fields');
        if ($sort !== null && $queryFields !== null) {
            $bodyParams = array_merge([$prefixParam => $queryFields], $bodyParams);
        }

        $baseUrl = Craft::$app->request->getHostInfo() . '/' . Craft::$app->request->getPathInfo();

        $url = UrlHelper::urlWithParams($baseUrl, $bodyParams);

        return $this->redirect($url);
    }
}
