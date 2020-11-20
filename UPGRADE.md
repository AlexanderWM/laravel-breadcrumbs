# Upgrade Guide

## Upgrading To 6.0 From 5.x

These upgrade notes serve to bring you off of the latest version of [`DaveJamesMiller\Crumbs`](https://github.com/davejamesmiller/laravel-breadcrumbs)
to this repository. Note that this library requires at least Laravel 6.

Begin by swapping libraries via Composer:

```shell script
composer remove davejamesmiller/laravel-breadcrumbs
composer require AlexanderWM/laravel-breadcrumbs
```

Next, you'll need to update the following references. While we've made most classes backwards-compatible and your project
should work right away, it's a good idea to update these sooner than later as they'll be removed in a future version.

| `davejamesmiller/laravel-breadcrumbs`                     | `AlexanderWM/laravel-breadcrumbs`       |
| --------------------------------------------------------- | ------------------------------------- |
| DaveJamesMiller\Crumbs\CrumbsManager            | AlexanderWM\Crumbs\Manager         |
| DaveJamesMiller\Crumbs\CrumbsGenerator          | AlexanderWM\Crumbs\Generator       |
| DaveJamesMiller\Crumbs\CrumbsServiceProvider    | AlexanderWM\Crumbs\ServiceProvider |
| DaveJamesMiller\Crumbs\Facades\Crumbs           | AlexanderWM\Crumbs\Crumbs     |

Once you're done, double-check your work by searching for `DaveJamesMiller\Crumbs` within your application and
making any necessary replacements. Note class name changes, like `CrumbsManager` to `Manager`. If you've never
gone off script, you should be all set.

If you ran into trouble following this upgrade guide, please file an issue. Happy coding!
