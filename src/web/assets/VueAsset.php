<?php

namespace pdaleramirez\superfilter\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class VueAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@superfilter/web/assets/app';

        $this->js = [
            'app.js'
        ];

        $this->css = [
            'app.css'
        ];

        parent::init();
    }
}