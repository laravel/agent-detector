<?php

declare(strict_types=1);

namespace Tests\Laravel;

use Illuminate\Foundation\Application;
use Laravel\AgentDetector\Laravel\AgentDetectorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [AgentDetectorServiceProvider::class];
    }
}
