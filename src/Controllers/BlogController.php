<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Post;

final class BlogController extends Controller
{
    public function index(Request $request): string
    {
        $page = max(1, $request->int('sayfa', 1));
        $perPage = 9;
        return $this->view('blog/index', [
            'title'      => 'Blog',
            'posts'      => Post::published($perPage, ($page - 1) * $perPage),
            'page'       => $page,
            'totalPages' => max(1, (int) ceil(Post::publishedCount() / $perPage)),
        ]);
    }

    public function show(Request $request, string $slug): string
    {
        $post = Post::findBySlug($slug);
        if ($post === null || !$post->published) {
            return $this->notFound();
        }
        Post::incrementViews($post->id);
        return $this->view('blog/show', [
            'title'  => $post->title,
            'post'   => $post,
            'recent' => Post::published(5),
        ]);
    }
}
