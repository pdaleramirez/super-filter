<?php

namespace pdaleramirez\superfilter\services;

use craft\base\Component;
use craft\helpers\Json;

class Templates extends Component
{
    public function getFrontendAssets()
    {
        $assets = \Craft::getAlias('@superfilter/web/assets/frontend/dist');

        $manifests = $assets . '/manifest.json';

        $content = Json::decodeIfJson(file_get_contents($manifests));

        $js = $content['index.html']['file'];
        $css = $content['index.css']['file'];

        return [
            'dist' => $assets,
            'js' => $js,
            'css' => $css,
        ];
    }
}