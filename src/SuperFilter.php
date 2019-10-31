<?php
/**
 * Search Filter plugin for Craft CMS 3.x
 *
 * Search elements with filter using Vue JS
 *
 * @link      https://github.com/pdaleramirez
 * @copyright Copyright (c) 2019 Dale Ramirez
 */

namespace pdaleramirez\superfilter;


use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use pdaleramirez\superfilter\models\Settings;
use pdaleramirez\superfilter\services\App;
use pdaleramirez\superfilter\web\twig\variables\SearchFilterVariable;
use pdaleramirez\superfilter\web\twig\variables\SuperFilterVariable;
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

/**
 * Class SuperFilter
 *
 * @author    Dale Ramirez
 * @package   SuperFilter
 * @since     1.0.0
 *
 */
class SuperFilter extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var App
     */
    public static $app;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'app' => App::class
        ]);

        $this->hasCpSettings = true;
        $this->hasCpSection = true;

        self::$app = $this->get('app');

        Craft::setAlias('@superfilter', $this->getBasePath());
        Craft::setAlias('@superfilterResources', dirname(__DIR__).'/resources/lib');

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('superFilter', SuperFilterVariable::class);
        });

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['super-filter/settings/<settingsSectionHandle:.*>'] = 'super-filter/super-filter/settings';
            $event->rules['super-filter/test'] = 'super-filter/super-filter/test';
            $event->rules['super-filter/install-sample-data'] = 'super-filter/super-filter/install-sample-data';
            $event->rules['super-filter/setup-search'] = ['template' => 'super-filter/setupsearch'];
            $event->rules['super-filter/setup-search/edit/<setupId:\d+|new>'] = 'super-filter/setup-search/edit';
        });

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules["super-filter/show-list"] = 'super-filter/elements/get-elements';
        });

    }

    public function getCpNavItem()
    {
        $parent = parent::getCpNavItem();

        $parent['subnav']['setup-search'] = [
            'label' => Craft::t('super-filter', 'Setup Search'),
            'url' => 'super-filter/setup-search'
        ];

        $parent['subnav']['settings'] = [
            'label' => Craft::t('super-filter', 'Settings'),
            'url' => 'super-filter/settings/general'
        ];

        return $parent;
    }
    protected function createSettingsModel()
    {
        return new Settings();
    }
}
