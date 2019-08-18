<?php

namespace pdaleramirez\searchfilter\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class VueAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@searchfilter/web/assets/app';

        $this->js = [
            'app.js'
        ];

        parent::init();
    }
}