<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use Illuminate\Support\Facades\Config;

class ExceptionsTest extends TestCase
{
    // Also see RouteBoundTest which tests the route binding-related exceptions
    // and IgnitionTest which tests the Laravel Ignition integration (solutions)

    public function testDuplicateBreadcrumbException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\DuplicateBreadcrumbException::class);
        $this->expectExceptionMessage('Breadcrumb name "duplicate" has already been registered');

        Crumbs::for('duplicate', function () {
        });
        Crumbs::for('duplicate', function () {
        });
    }

    public function testInvalidBreadcrumbException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\InvalidBreadcrumbException::class);
        $this->expectExceptionMessage('Breadcrumb not found with name "invalid"');

        Crumbs::render('invalid');
    }

    public function testInvalidBreadcrumbExceptionDisabled()
    {
        Config::set('breadcrumbs.invalid-named-breadcrumb-exception', false);

        $html = Crumbs::render('invalid')->toHtml();

        $this->assertXmlStringEqualsXmlString('
            <p>No breadcrumbs</p>
        ', $html);
    }

    public function testViewNotSetException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\ViewNotSetException::class);
        $this->expectExceptionMessage('Crumbs view not specified (check config/breadcrumbs.php)');

        Config::set('breadcrumbs.view', '');

        Crumbs::for('home', function ($trail) {
            $trail->push('Home', url('/'));
        });

        Crumbs::render('home');
    }
}
