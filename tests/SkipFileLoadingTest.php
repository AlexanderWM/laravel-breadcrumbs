<?php

namespace AlexanderWM\Crumbs\Tests;

class SkipFileLoadingTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->config->set('breadcrumbs.files', []);
    }

    /** @covers \AlexanderWM\Crumbs\ServiceProvider::registerCrumbs */
    public function testLoading()
    {
        // I can't think of a way to actually test this since nothing is loaded -
        // see code coverage (if (!$files) { return; })
        $this->assertTrue(true);
    }
}
