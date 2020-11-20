<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\ServiceProvider;

class CustomChildServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CustomChildServiceProvider::class,
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

class CustomChildServiceProvider extends ServiceProvider
{
    public function registerCrumbs(): void
    {
        Crumbs::for('home', function ($trail) {
            $trail->push('Home', '/');
        });
    }
}
