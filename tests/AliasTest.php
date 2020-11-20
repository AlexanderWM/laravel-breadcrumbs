<?php

namespace AlexanderWM\Crumbs\Tests;

class AliasTest extends TestCase
{
    public function testCanResolveDeprecatedClasses(): void
    {
        // Verify classes referenced in legacy README resolve for backwards compatibility
        // @see https://github.com/davejamesmiller/laravel-breadcrumbs/blob/master/README.md
        foreach (
            [
                \DaveJamesMiller\Crumbs\CrumbsManager::class,
                \DaveJamesMiller\Crumbs\CrumbsGenerator::class,
                \DaveJamesMiller\Crumbs\CrumbsServiceProvider::class,
                \DaveJamesMiller\Crumbs\Facades\Crumbs::class,
            ]
            as $deprecatedClassReferencedInREADME
        ) {
            $this->assertTrue(\class_exists($deprecatedClassReferencedInREADME));
        }
    }
}
