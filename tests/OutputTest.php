<?php

namespace AlexanderWM\Crumbs\Tests;

use AlexanderWM\Crumbs\Crumbs;

class OutputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Home (Normal link)
        Crumbs::for('home', function ($trail) {
            $trail->push('Home', url('/'));
        });

        // Home > Blog (Not a link)
        Crumbs::for('blog', function ($trail) {
            $trail->parent('home');
            $trail->push('Blog');
        });

        // Home > Blog > [Category] (Active page)
        Crumbs::for('category', function ($trail, $category) {
            $trail->parent('blog');
            $trail->push($category->title, url("blog/category/{$category->id}"));
        });

        $this->category = (object)[
            'id' => 456,
            'title' => 'Sample Category',
        ];

        $this->expectedHtml = '
            <nav>
                <ol>
                    <li><a href="http://localhost">Home</a></li>
                    <li>Blog</li>
                    <li class="current">Sample Category</li>
                </ol>
            </nav>
        ';
    }

    public function testBladeRender()
    {
        // {{ Crumbs::render('category', $category) }}
        $html = view('view-blade')->with('category', $this->category)->render();

        $this->assertXmlStringEqualsXmlString($this->expectedHtml, $html);
    }

    public function testBladeSection()
    {
        // @section('breadcrumbs', Crumbs::render('category', $category))
        $html = view('view-section')->with('category', $this->category)->render();

        $this->assertXmlStringEqualsXmlString($this->expectedHtml, $html);
    }

    public function testPhpRender()
    {
        /* <?= Crumbs::render('category', $category) ?> */
        $html = view('view-php')->with('category', $this->category)->render();

        $this->assertXmlStringEqualsXmlString($this->expectedHtml, $html);
    }
}
