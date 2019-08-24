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

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('superFilter', SuperFilterVariable::class);
        });

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules["show-list"] = 'super-filter/elements/get-elements';
        });

    }

    // Protected Methods
    // =========================================================================

}
