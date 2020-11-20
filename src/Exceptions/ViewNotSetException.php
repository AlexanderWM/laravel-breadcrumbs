<?php

namespace AlexanderWM\Crumbs\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

/**
 * Exception that is thrown if the user attempts to render breadcrumbs without setting a view.
 */
class ViewNotSetException extends BaseException implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        $links = [];
        $links['Choosing a breadcrumbs template (view)'] = 'https://github.com/AlexanderWM/laravel-breadcrumbs#3-choose-a-template';
        $links['Laravel Crumbs documentation'] = 'https://github.com/AlexanderWM/laravel-breadcrumbs#laravel-breadcrumbs';

        return BaseSolution::create('Set a view for Laravel Crumbs')
            ->setSolutionDescription("Please check `config/breadcrumbs.php` for a valid `'view'` (e.g. `'breadcrumbs::bootstrap4'`)")
            ->setDocumentationLinks($links);
    }
}
