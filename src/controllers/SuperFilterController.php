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

class SuperFilterController extends Controller
{

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

        if (empty($settings->entryTemplate)) {
            $settings->entryTemplate = App::DEFAULT_TEMPLATE;
        }

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

    public function actionTest()
    {
        $entries = Entry::find()->orderBy('superFilterReleaseDates ASC')->all();

        foreach ($entries as $entry) {

            echo $entry->title . ' - ' . $entry->superFilterReleaseDates . ' - ' . $entry->superFilterImdbRating . '<br/>';
        }
        return null;
    }

    public function generateSampleData()
    {
        $entry = new Entry();
        $entry->sectionId = 2;
        $entry->typeId = 2;
        $entry->title = 'this is a test 2';
        $entry->setFieldValue('subTitle', 'xvasd');

        $result = Craft::$app->elements->saveElement($entry);

        \Craft::dd($result);
    }

    public function createCategoriesField()
    {
        $config["type"] = "craft\\fields\\Categories";
        $config["groupId"] = 1;
        $config["name"] = "Super Categories";
        $config["handle"] = "superCategories";

        $field = Craft::$app->getFields()->createField($config);

        \Craft::dd($field);
    }

    /**
     * @return string|null
     * @throws \Throwable
     * @throws \craft\errors\CategoryGroupNotFoundException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionInstallSampleData()
    {
        $this->requirePostRequest();

        SuperFilter::$app->sampleData->generateSampleData();

        return Json::encode(['result' => 'temp']);
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
}
