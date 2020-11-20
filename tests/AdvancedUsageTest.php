<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\Tests\Models\Post;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class AdvancedUsageTest extends TestCase
{
    public function testCrumbsWithNoUrl()
    {
        Crumbs::for('sample', function ($trail) {
            $trail->push('Sample');
        });

        $breadcrumbs = Crumbs::generate('sample');

        $this->assertCount(1, $breadcrumbs);
        $this->assertSame('Sample', $breadcrumbs[0]->title);
        $this->assertNull($breadcrumbs[0]->url);
    }

    public function testCustomData()
    {
        Crumbs::for('home', function ($trail) {
            $trail->push('Home', '/', ['icon' => 'home.png']);
        });

        $breadcrumbs = Crumbs::generate('home');

        $this->assertCount(1, $breadcrumbs);
        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('/', $breadcrumbs[0]->url);
        $this->assertSame('home.png', $breadcrumbs[0]->icon);
    }

    public function testBeforeAndAfterCallbacks()
    {
        Crumbs::before(function ($trail) {
            $trail->push('Before');
        });

        Crumbs::for('home', function ($trail) {
            $trail->push('Home', route('home'));
        });

        Crumbs::after(function ($trail) {
            $page = (int)request('page', 1);
            if ($page > 1) {
                $trail->push("Page $page");
            }
        });

        Route::name('home')->get('/', function () {
            return Crumbs::render('home');
        });

        $html = $this->get('/?page=2')->content();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li>Before</li>
                <li><a href="http://localhost">Home</a></li>
                <li class="current">Page 2</li>
            </ol>
        ', $html);
    }

    public function testCurrentPageBreadcrumb()
    {
        Route::name('home')->get('/', function () {
        });

        Route::name('post')->middleware(SubstituteBindings::class)->get('/post/{post}', function (Post $post) {
            return Crumbs::current()->title;
        });

        Crumbs::for('post', function ($trail, $post) {
            $trail->push('Home', route('home'));
            $trail->push($post->title, route('post', $post));
            $trail->push('Page 2', null, ['current' => false]);
        });

        $html = $this->get('/post/1')->content();

        $this->assertSame('Post 1', $html);
    }

    public function testGenerateCollection()
    {
        Route::name('home')->get('/', function () {
        });
        Route::name('post')->get('/post/{post}', function () {
        });

        Crumbs::for('post', function ($trail, $id) {
            $trail->push('Home', route('home'));
            $trail->push("Post $id", route('post', $id));
            $trail->push('Page 2', null, ['current' => false]);
        });

        $breadcrumbs = Crumbs::generate('post', 1)->where('current', '!==', false);

        $this->assertInstanceOf(Collection::class, $breadcrumbs);
        $this->assertSame('Post 1', $breadcrumbs->last()->title);
    }

    public function testPageTitleMacro()
    {
        Route::name('home')->get('/', function () {
        });

        Route::name('post')->middleware(SubstituteBindings::class)->get('/post/{post}', function (Post $post) {
            return Crumbs::pageTitle();
        });

        Crumbs::for('post', function ($trail, $post) {
            $trail->push('Home', route('home'));
            $trail->push($post->title, route('post', $post));
            $trail->push('Page 2', null, ['current' => false]);
        });

        Crumbs::macro('pageTitle', function () {
            $title = ($breadcrumb = Crumbs::current()) ? "{$breadcrumb->title} – " : '';

            if (($page = (int)request('page')) > 1) {
                $title .= "Page $page – ";
            }

            return $title . 'Demo App';
        });

        $html = $this->get('/post/1?page=2')->content();

        $this->assertSame('Post 1 – Page 2 – Demo App', $html);
    }

    public function testResourceMacro()
    {
        // Routes
        Route::name('home')->get('/', function () {
        });
        Route::name('blog.index')->get('/blog', function () {
        });
        Route::name('blog.create')->get('/blog/create', function () {
        });
        Route::name('blog.store')->post('/blog', function () {
        });
        Route::name('blog.show')->middleware(SubstituteBindings::class)->get('/blog/{post}', function (Post $post) {
        });
        Route::name('blog.edit')->middleware(SubstituteBindings::class)->get('/blog/{post}/edit', function (Post $post) {
        });
        Route::name('blog.update')->middleware(SubstituteBindings::class)->put('/blog/{post}', function (Post $post) {
        });
        Route::name('blog.destroy')->middleware(SubstituteBindings::class)->delete('/blog/{post}', function (Post $post) {
        });

        // Crumbs
        Crumbs::macro('resource', function ($name, $title) {
            // Home > Blog
            Crumbs::for("$name.index", function ($trail) use ($name, $title) {
                $trail->parent('home');
                $trail->push($title, route("$name.index"));
            });

            // Home > Blog > New
            Crumbs::for("$name.create", function ($trail) use ($name) {
                $trail->parent("$name.index");
                $trail->push('New', route("$name.create"));
            });

            // Home > Blog > Post 123
            Crumbs::for("$name.show", function ($trail, $model) use ($name) {
                $trail->parent("$name.index");
                $trail->push($model->title, route("$name.show", $model));
            });

            // Home > Blog > Post 123 > Edit
            Crumbs::for("$name.edit", function ($trail, $model) use ($name) {
                $trail->parent("$name.show", $model);
                $trail->push('Edit', route("$name.edit", $model));
            });
        });

        Crumbs::for('home', function ($trail) {
            $trail->push('Home', route('home'), ['icon' => 'home.png']);
        });

        Crumbs::resource('blog', 'Blog');

        // Index
        $breadcrumbs = Crumbs::generate('blog.index');
        $this->assertInstanceOf(Collection::class, $breadcrumbs);
        $this->assertCount(2, $breadcrumbs);
        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('http://localhost', $breadcrumbs[0]->url);
        $this->assertSame('Blog', $breadcrumbs[1]->title);
        $this->assertSame('http://localhost/blog', $breadcrumbs[1]->url);

        // Create
        $breadcrumbs = Crumbs::generate('blog.create');
        $this->assertInstanceOf(Collection::class, $breadcrumbs);
        $this->assertCount(3, $breadcrumbs);
        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('http://localhost', $breadcrumbs[0]->url);
        $this->assertSame('Blog', $breadcrumbs[1]->title);
        $this->assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        $this->assertSame('New', $breadcrumbs[2]->title);
        $this->assertSame('http://localhost/blog/create', $breadcrumbs[2]->url);

        // Show
        $breadcrumbs = Crumbs::generate('blog.show', new Post(1));
        $this->assertInstanceOf(Collection::class, $breadcrumbs);
        $this->assertCount(3, $breadcrumbs);
        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('http://localhost', $breadcrumbs[0]->url);
        $this->assertSame('Blog', $breadcrumbs[1]->title);
        $this->assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        $this->assertSame('Post 1', $breadcrumbs[2]->title);
        $this->assertSame('http://localhost/blog/1', $breadcrumbs[2]->url);

        // Edit
        $breadcrumbs = Crumbs::generate('blog.edit', new Post(1));
        $this->assertInstanceOf(Collection::class, $breadcrumbs);
        $this->assertCount(4, $breadcrumbs);
        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('http://localhost', $breadcrumbs[0]->url);
        $this->assertSame('Blog', $breadcrumbs[1]->title);
        $this->assertSame('http://localhost/blog', $breadcrumbs[1]->url);
        $this->assertSame('Post 1', $breadcrumbs[2]->title);
        $this->assertSame('http://localhost/blog/1', $breadcrumbs[2]->url);
        $this->assertSame('Edit', $breadcrumbs[3]->title);
        $this->assertSame('http://localhost/blog/1/edit', $breadcrumbs[3]->url);
    }

    public function testSetCurrentRoute()
    {
        Crumbs::for('sample', function ($trail) {
            $trail->push("Sample");
        });

        Crumbs::setCurrentRoute('sample');

        $html = Crumbs::render()->toHtml();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li class="current">Sample</li>
            </ol>
        ', $html);
    }

    public function testSetCurrentRouteWithParams()
    {
        Crumbs::for('sample', function ($trail, $a, $b) {
            $trail->push("Sample $a, $b");
        });

        Crumbs::setCurrentRoute('sample', 1, 2);

        $html = Crumbs::render()->toHtml();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li class="current">Sample 1, 2</li>
            </ol>
        ', $html);
    }

    public function testClearCurrentRoute()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\InvalidBreadcrumbException::class);

        Crumbs::for('sample', function ($trail, $a, $b) {
            $trail->push("Sample $a, $b");
        });

        Crumbs::setCurrentRoute('sample', 1, 2);
        Crumbs::clearCurrentRoute();

        Crumbs::render();
    }
}
