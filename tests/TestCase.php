<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\ServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends TestbenchTestCase
{
    use MatchesSnapshots;

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Crumbs' => Crumbs::class,
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app->config->set('view.paths', [__DIR__ . '/resources/views']);
        $app->config->set('breadcrumbs.view', 'breadcrumbs');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }
}
