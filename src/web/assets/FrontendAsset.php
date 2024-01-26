<?php

namespace pdaleramirez\superfilter\web\assets;

use craft\web\AssetBundle;
use pdaleramirez\superfilter\SuperFilter;

class FrontendAsset extends AssetBundle
{
    public function init()
    {
        $source = SuperFilter::getInstance()->getTemplates()->getFrontendAssets();
        $this->sourcePath = $source['dist'];

        $this->js = [
            $source['js']
        ];

        $this->css = [
            $source['css']
        ];

        parent::init();
    }
}
