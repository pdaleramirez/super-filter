<?php

namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use Craft;
use craft\base\Element;
use craft\db\Paginator;
use craft\elements\Category;
use craft\elements\db\EntryQuery;
use craft\web\twig\variables\Paginate;
use pdaleramirez\superfilter\models\Settings;

class App extends Component
{
    /**
     * @var $sampleData SampleData
     */
    public $sampleData;

    /**
     * @var $searchTypes SearchTypes
     */
    public $searchTypes;

    public function init(): void
    {
        $this->sampleData = new SampleData();
        $this->searchTypes = new SearchTypes();
    }

    /**
     * @return \craft\base\Model|null|Settings
     */
    public function getSettings()
    {
        $plugin = Craft::$app->plugins->getPlugin('super-filter');

        return $plugin->getSettings();
    }

    public function getBaseTemplates()
    {
        return [
            'vue' => Craft::t('super-filter', 'Vue'),
            'plain' => Craft::t('super-filter', 'Plain')
        ];
    }

    public function getBaseTemplateOptions()
    {
        $options = [];
        $i = 0;
        foreach ($this->getBaseTemplates() as $key => $label) {
            $options[$i]['label'] = $label;
            $options[$i]['value'] = $key;

            $i++;
        }

        return $options;
    }

    public function isTemplateIn($value)
    {
        $keys = array_keys($this->getBaseTemplates());

        return in_array($value, $keys);
    }

    public static function buildQuery(array $params): string
    {
        if (empty($params)) {
            return '';
        }
        // build the query string
        $query = http_build_query($params);
        if ($query === '') {
            return '';
        }
        // Decode the param names and a few select chars in param values
        $params = [];
        foreach (explode('&', $query) as $param) {
            list($n, $v) = array_pad(explode('=', $param, 2), 2, '');
            $n = urldecode($n);
            $v = str_replace(['%2F', '%7B', '%7D'], ['/', '{', '}'], $v);
            $params[] = "$n=$v";
        }
        return implode('&', $params);
    }
}
