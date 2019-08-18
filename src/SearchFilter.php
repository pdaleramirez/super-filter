<?php
/**
 * Search Filter plugin for Craft CMS 3.x
 *
 * Search elements with filter using Vue JS
 *
 * @link      https://github.com/pdaleramirez
 * @copyright Copyright (c) 2019 Dale Ramirez
 */

namespace pdaleramirez\searchfilter;


use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use pdaleramirez\searchfilter\services\App;
use pdaleramirez\searchfilter\web\twig\variables\SearchFilterVariable;
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

/**
 * Class SearchFilter
 *
 * @author    Dale Ramirez
 * @package   SearchFilter
 * @since     1.0.0
 *
 */
class SearchFilter extends Plugin
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

        Craft::setAlias('@searchfilter', $this->getBasePath());

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('searchFilter', SearchFilterVariable::class);
        });

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules["show-list"] = 'search-filter/elements/get-elements';
        });

    }

    // Protected Methods
    // =========================================================================

}
