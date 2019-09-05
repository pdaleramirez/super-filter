<?php

use Codeception\Test\Unit;
use pdaleramirez\superfilter\SuperFilter;

class AppTest extends Unit
{

    public function testName()
    {
        //$wamba = \pdaleramirez\superfilter\services\App::wamba();

       // echo '<pre>' . print_r(Craft::$app->getComponents(), true) . '</pre>'; exit;
        $test = SuperFilter::$app->wamba();
        $this->assertEquals($test, 'wa');
    }

}
