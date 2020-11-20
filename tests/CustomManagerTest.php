<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\Manager;
use Illuminate\Support\Collection;

class CustomManagerTest extends TestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        // Need to inject this early, before the package is loaded, to simulate it being set in the config file
        $app['config']['breadcrumbs.manager-class'] = CustomCrumbs::class;
    }

    public function testCustomManager()
    {
        $breadcrumbs = Crumbs::generate();

        $this->assertSame('custom-manager', $breadcrumbs[0]);
    }
}

class CustomCrumbs extends Manager
{
    public function generate(string $name = null, ...$params): Collection
    {
        return new Collection(['custom-manager']);
    }
}
