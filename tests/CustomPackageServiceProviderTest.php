<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\Generator;
use AlexanderWM\Crumbs\Manager;
use AlexanderWM\Crumbs\ServiceProvider as CrumbsServiceProvider;
use Illuminate\Support\ServiceProvider;

class CustomPackageServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CrumbsServiceProvider::class,
            CustomPackageServiceProvider::class,
        ];
    }

    public function testRender()
    {
        $html = Crumbs::render('home')->toHtml();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li class="current">Home</li>
            </ol>
        ', $html);
    }
}

class CustomPackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Manager $breadcrumbs): void
    {
        $breadcrumbs->for('home', function (Generator $trail) {
            $trail->push('Home', '/');
        });
    }
}
