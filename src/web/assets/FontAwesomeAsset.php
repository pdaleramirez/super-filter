<?php

namespace pdaleramirez\superfilter\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class FontAwesomeAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@superfilterResources/font-awesome';

        // define the dependencies
        $this->depends = [
            CpAsset::class
        ];

        $this->css = [
            'css/font-awesome.min.css'
        ];

        parent::init();
    }
}
