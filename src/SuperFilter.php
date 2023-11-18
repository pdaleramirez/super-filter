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
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use pdaleramirez\superfilter\events\ItemArrayEvent;
use pdaleramirez\superfilter\events\RegisterSearchFieldTypeEvent;
use pdaleramirez\superfilter\events\RegisterSearchTypeEvent;
use pdaleramirez\superfilter\fields\Categories;
use pdaleramirez\superfilter\fields\Dropdown;
use pdaleramirez\superfilter\fields\Entries;
use pdaleramirez\superfilter\fields\Number;
use pdaleramirez\superfilter\fields\PlainText;
use pdaleramirez\superfilter\fields\PriceRange;
use pdaleramirez\superfilter\fields\RadioButtons;
use pdaleramirez\superfilter\fields\Tags;
use pdaleramirez\superfilter\fields\Title;
use pdaleramirez\superfilter\models\Settings;
use pdaleramirez\superfilter\plugin\Services;
use pdaleramirez\superfilter\searchtypes\CategorySearchType;
use pdaleramirez\superfilter\searchtypes\EntrySearchType;
use pdaleramirez\superfilter\searchtypes\ProductSearchType;
use pdaleramirez\superfilter\services\App;
use pdaleramirez\superfilter\services\SearchTypes;
use pdaleramirez\superfilter\services\Templates;
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
    use Services;
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
    public string $schemaVersion = '1.0.0';

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
        Craft::setAlias('@superfilterModule', dirname(__DIR__));

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'pdaleramirez\\superfilter\\console\\controllers';
        } else {
            $this->controllerNamespace = 'pdaleramirez\\superfilter\\controllers';
        }

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('superFilter', SuperFilterVariable::class);
        });

        // Setup Template Roots
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['super-filter'] = $this->getBasePath().DIRECTORY_SEPARATOR.'templates';
        });

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['super-filter/settings/<settingsSectionHandle:.*>'] = 'super-filter/super-filter/settings';
            $event->rules['super-filter/test'] = 'super-filter/super-filter/test';
            $event->rules['super-filter/install-sample-data'] = 'super-filter/super-filter/install-sample-data';
            $event->rules['super-filter/setup-search'] = ['template' => 'super-filter/setupsearch'];
            $event->rules['super-filter/setup-search/edit/<setupId:\d+|new>'] = 'super-filter/setup-search/edit';
            $event->rules['super-filter/setup-search/setup-options'] = 'super-filter/setup-search/setup-options';
        });


        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['super-filter/filter']    = 'super-filter/elements/filter';
            $event->rules['super-filter/fields']    = 'super-filter/elements/get-fields';
            $event->rules['super-filter/entries']    = 'super-filter/elements/entries';
            $event->rules['super-filter/template']    = 'super-filter/elements/get-template-content';
            $event->rules['super-filter/search-fields']    = 'super-filter/elements/get-search-fields';
        });

        Event::on(SearchTypes::class, SearchTypes::EVENT_REGISTER_SEARCH_TYPES, function (RegisterSearchTypeEvent $event) {
            $event->searchTypes['entry']    = new EntrySearchType();
            $event->searchTypes['category'] = new CategorySearchType();
            if (Craft::$app->getPlugins()->isPluginEnabled('commerce')) {
                $event->searchTypes['product']  = new ProductSearchType();
            }
        });
        Event::on(SearchTypes::class, SearchTypes::EVENT_REGISTER_SEARCH_FIELD_TYPES, function (RegisterSearchFieldTypeEvent $event) {
            $event->searchFieldTypes[] = new Title();
            $event->searchFieldTypes[] = new Number();
            $event->searchFieldTypes[] = new PlainText();
            $event->searchFieldTypes[] = new Categories();
            $event->searchFieldTypes[] = new Entries();
            $event->searchFieldTypes[] = new Dropdown();
            $event->searchFieldTypes[] = new Tags();
            $event->searchFieldTypes[] = new RadioButtons();
            $event->searchFieldTypes[] = new PriceRange();
        });

        Event::on(SearchTypes::class, SearchTypes::EVENT_ITEM_ARRAY, function (ItemArrayEvent $event) {
            if (Craft::$app->getPlugins()->isPluginEnabled('commerce') == true
                && $event->searchType instanceof ProductSearchType) {

				if ($this->getSettings()->variants === true) {
					$event->item['variants'] = $event->element->getVariants();
				}
            }
        });
    }

    public function getCpNavItem(): array|null
    {
        $parent = parent::getCpNavItem();

        if (!empty(trim($this->getSettings()->pluginNameNav))) {
            $parent['label'] = $this->getSettings()->pluginNameNav;
        }

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

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    public static function config(): array
    {
        return [
            'components' => [
                'templates' => ['class' => Templates::class]
            ]
        ];
    }
}
