<?php

namespace App\Http\Controllers;

use AlexanderWM\Crumbs\Crumbs;
use AlexanderWM\Crumbs\Tests\Models\Post;

class PostController
{
    public function edit(Post $post)
    {
        return Crumbs::render();
    }
}
