<?php

namespace Nicodevs\NovaStripe\Tests;

use Nicodevs\NovaStripe\ToolServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
