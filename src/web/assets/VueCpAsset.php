<?php

namespace pdaleramirez\superfilter\web\assets;

use craft\web\AssetBundle;

class VueCpAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@superfilter/web/assets/cp';

        $this->js = [
            'cp.js'
        ];

        $this->css = [
            'cp.css'
        ];

        parent::init();
    }
}
