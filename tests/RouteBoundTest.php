<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\Tests\Models\Post;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class RouteBoundTest extends TestCase
{
    public function testRender()
    {
        // Home
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($trail) {
            $trail->push('Home', route('home'));
        });

        // Home > [Post]
        Route::name('post')->get('/post/{id}', function () {
            return Crumbs::render();
        });

        Crumbs::for('post', function ($trail, $id) {
            $post = Post::findOrFail($id);
            $trail->parent('home');
            $trail->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li><a href="http://localhost">Home</a></li>
                <li class="current">Post 1</li>
            </ol>
        ', $html);
    }

    public function testGenerate()
    {
        // Home
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($trail) {
            $trail->push('Home', route('home'));
        });

        // Home > [Post]
        Route::name('post')->get('/post/{id}', function () use (&$breadcrumbs) {
            $breadcrumbs = Crumbs::generate();
        });

        Crumbs::for('post', function ($trail, $id) {
            $post = Post::findOrFail($id);
            $trail->parent('home');
            $trail->push($post->title, route('post', $post));
        });

        $this->get('/post/1');

        $this->assertCount(2, $breadcrumbs);

        $this->assertSame('Home', $breadcrumbs[0]->title);
        $this->assertSame('http://localhost', $breadcrumbs[0]->url);

        $this->assertSame('Post 1', $breadcrumbs[1]->title);
        $this->assertSame('http://localhost/post/1', $breadcrumbs[1]->url);
    }

    public function testView()
    {
        // Home
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($breadcrumbs) {
            $breadcrumbs->push('Home', route('home'));
        });

        // Home > [Post]
        Route::name('post')->get('/post/{id}', function () {
            return Crumbs::view('breadcrumbs2');
        });

        Crumbs::for('post', function ($breadcrumbs, $id) {
            $post = Post::findOrFail($id);
            $breadcrumbs->parent('home');
            $breadcrumbs->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertXmlStringEqualsXmlString('
            <ul>
                <li><a href="http://localhost">Home</a></li>
                <li class="current">Post 1</li>
            </ul>
        ', $html);
    }

    public function testExists()
    {
        // Exists
        Crumbs::for('exists', function () {
        });

        Route::name('exists')->get('/exists', function () use (&$exists1) {
            $exists1 = Crumbs::exists();
        });

        $this->get('/exists');
        $this->assertTrue($exists1);

        // Doesn't exist
        Route::name('doesnt-exist')->get('/doesnt-exist', function () use (&$exists2) {
            $exists2 = Crumbs::exists();
        });

        $this->get('/doesnt-exist');
        $this->assertFalse($exists2);

        // Unnamed
        Route::get('/unnamed', function () use (&$exists3) {
            $exists3 = Crumbs::exists();
        });

        $this->get('/unnamed');
        $this->assertFalse($exists3);
    }

    public function testError404()
    {
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($breadcrumbs) {
            $breadcrumbs->push('Home', route('home'));
        });

        Crumbs::for('errors.404', function ($breadcrumbs) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push('Not Found');
        });

        $html = $this->withExceptionHandling()->get('/this-does-not-exist')->content();

        $this->assertXmlStringEqualsXmlString('
            <nav>
                <ol>
                    <li><a href="http://localhost">Home</a></li>
                    <li class="current">Not Found</li>
                </ol>
            </nav>
        ', $html);
    }

    public function testMissingBreadcrumbException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\InvalidBreadcrumbException::class);
        $this->expectExceptionMessage('Breadcrumb not found with name "home"');

        Route::name('home')->get('/', function () {
            return Crumbs::render();
        });

        $this->get('/');
    }

    public function testMissingBreadcrumbExceptionDisabled()
    {
        Config::set('breadcrumbs.missing-route-bound-breadcrumb-exception', false);

        Route::name('home')->get('/', function () {
            return Crumbs::render();
        });

        $html = $this->get('/')->content();

        $this->assertXmlStringEqualsXmlString('
            <p>No breadcrumbs</p>
        ', $html);
    }

    public function testUnnamedRouteException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\UnnamedRouteException::class);
        $this->expectExceptionMessage('The current route (GET /blog) is not named');

        Route::get('/blog', function () {
            return Crumbs::render();
        });

        $this->get('/blog');
    }

    public function testUnnamedHomeRouteException()
    {
        $this->expectException(\AlexanderWM\Crumbs\Exceptions\UnnamedRouteException::class);
        $this->expectExceptionMessage('The current route (GET /) is not named');

        // Make sure the message is "GET /" not "GET //"
        Route::get('/', function () {
            return Crumbs::render();
        });

        $this->get('/');
    }

    public function testUnnamedRouteExceptionDisabled()
    {
        Config::set('breadcrumbs.unnamed-route-exception', false);

        Route::get('/', function () {
            return Crumbs::render();
        });

        $html = $this->get('/')->content();

        $this->assertXmlStringEqualsXmlString('
            <p>No breadcrumbs</p>
        ', $html);
    }

    public function testExplicitModelBinding()
    {
        // Home
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($breadcrumbs) {
            $breadcrumbs->push('Home', route('home'));
        });

        // Home > [Post]
        Route::model('post', Post::class);

        Route::name('post')->middleware(SubstituteBindings::class)->get('/post/{post}', function ($post) {
            return Crumbs::render();
        });

        Crumbs::for('post', function ($breadcrumbs, $post) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li><a href="http://localhost">Home</a></li>
                <li class="current">Post 1</li>
            </ol>
        ', $html);
    }

    public function testImplicitModelBinding()
    {
        // Home
        Route::name('home')->get('/', function () {
        });

        Crumbs::for('home', function ($breadcrumbs) {
            $breadcrumbs->push('Home', route('home'));
        });

        // Home > [Post]
        Route::name('post')->middleware(SubstituteBindings::class)->get('/post/{post}', function (Post $post) {
            return Crumbs::render();
        });

        Crumbs::for('post', function ($breadcrumbs, $post) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push($post->title, route('post', $post));
        });

        $html = $this->get('/post/1')->content();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li><a href="http://localhost">Home</a></li>
                <li class="current">Post 1</li>
            </ol>
        ', $html);
    }

    public function testResourcefulControllers()
    {
        Route::middleware(SubstituteBindings::class)->resource('post', 'App\Http\Controllers\PostController');

        // Posts
        Crumbs::for('post.index', function ($breadcrumbs) {
            $breadcrumbs->push('Posts', route('post.index'));
        });

        // Posts > Upload Post
        Crumbs::for('post.create', function ($breadcrumbs) {
            $breadcrumbs->parent('post.index');
            $breadcrumbs->push('New Post', route('post.create'));
        });

        // Posts > [Post Name]
        Crumbs::for('post.show', function ($breadcrumbs, Post $post) {
            $breadcrumbs->parent('post.index');
            $breadcrumbs->push($post->title, route('post.show', $post->id));
        });

        // Posts > [Post Name] > Edit Post
        Crumbs::for('post.edit', function ($breadcrumbs, Post $post) {
            $breadcrumbs->parent('post.show', $post);
            $breadcrumbs->push('Edit Post', route('post.edit', $post->id));
        });

        $html = $this->get('/post/1/edit')->content();

        $this->assertXmlStringEqualsXmlString('
            <ol>
                <li><a href="http://localhost/post">Posts</a></li>
                <li><a href="http://localhost/post/1">Post 1</a></li>
                <li class="current">Edit Post</li>
            </ol>
        ', $html);
    }
}
