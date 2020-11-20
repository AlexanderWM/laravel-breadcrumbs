<?php

namespace AlexanderWM\Crumbs;

use AlexanderWM\Crumbs\Exceptions\BaseException;
use AlexanderWM\Crumbs\Exceptions\DuplicateBreadcrumbException;
use AlexanderWM\Crumbs\Exceptions\InvalidBreadcrumbException;
use AlexanderWM\Crumbs\Exceptions\UnnamedRouteException;
use AlexanderWM\Crumbs\Exceptions\ViewNotSetException;

\class_alias(BaseException::class, \DaveJamesMiller\Crumbs\CrumbsException::class);
\class_alias(Crumbs::class, \DaveJamesMiller\Crumbs\Facades\Crumbs::class);
\class_alias(DuplicateBreadcrumbException::class, \DaveJamesMiller\Crumbs\Exceptions\DuplicateBreadcrumbException::class);
\class_alias(Generator::class, \DaveJamesMiller\Crumbs\CrumbsGenerator::class);
\class_alias(InvalidBreadcrumbException::class, \DaveJamesMiller\Crumbs\Exceptions\InvalidBreadcrumbException::class);
\class_alias(Manager::class, \DaveJamesMiller\Crumbs\CrumbsManager::class);
\class_alias(ServiceProvider::class, \DaveJamesMiller\Crumbs\CrumbsServiceProvider::class);
\class_alias(UnnamedRouteException::class, \DaveJamesMiller\Crumbs\Exceptions\UnnamedRouteException::class);
\class_alias(ViewNotSetException::class, \DaveJamesMiller\Crumbs\Exceptions\ViewNotSetException::class);

//

namespace DaveJamesMiller\Crumbs;

if (!\class_exists(CrumbsException::class)) {
    /** @deprecated */
    class CrumbsException {}
}
if (!\class_exists(CrumbsGenerator::class)) {
    /** @deprecated */
    class CrumbsGenerator {}
}
if (!\class_exists(CrumbsManager::class)) {
    /** @deprecated */
    class CrumbsManager {}
}
if (!\class_exists(CrumbsServiceProvider::class)) {
    /** @deprecated */
    class CrumbsServiceProvider {}
}

//

namespace DaveJamesMiller\Crumbs\Facades;

if (!\class_exists(Crumbs::class)) {
    /** @deprecated */
    class Crumbs {}
}

//

namespace DaveJamesMiller\Crumbs\Exceptions;

if (!\class_exists(DuplicateBreadcrumbException::class)) {
    /** @deprecated */
    class DuplicateBreadcrumbException {}
}
if (!\class_exists(InvalidBreadcrumbException::class)) {
    /** @deprecated */
    class InvalidBreadcrumbException {}
}
if (!\class_exists(UnnamedRouteException::class)) {
    /** @deprecated */
    class UnnamedRouteException {}
}
if (!\class_exists(ViewNotSetException::class)) {
    /** @deprecated */
    class ViewNotSetException {}
}
