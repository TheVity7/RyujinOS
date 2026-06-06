<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Post;

final class PostController extends Controller
{
    public function index(): string
    {
        return $this->view('admin/blog/index', [
            'title' => 'Blog',
            'posts' => Post::all(),
        ]);
    }

    public function create(): string
    {
        return $this->view('admin/blog/form', ['title' => 'Yeni Yazı', 'post' => null]);
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);
        $data = $this->validated($request);
        if ($data === null) {
            $this->back();
        }
        $data['author'] = Auth::user()->username;
        Post::create($data);
        Flash::success('Yazı yayınlandı.');
        redirect('yonetim/blog');
    }

    public function edit(Request $request, string $id): string
    {
        $post = Post::find((int) $id);
        if ($post === null) {
            return $this->notFound();
        }
        return $this->view('admin/blog/form', ['title' => 'Yazıyı Düzenle', 'post' => $post]);
    }

    public function update(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $post = Post::find((int) $id);
        if ($post === null) {
            Flash::error('Yazı bulunamadı.');
            redirect('yonetim/blog');
        }
        $data = $this->validated($request);
        if ($data === null) {
            $this->back();
        }
        Post::update($post->id, $data);
        Flash::success('Yazı güncellendi.');
        redirect('yonetim/blog');
    }

    public function destroy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        Post::delete((int) $id);
        Flash::success('Yazı silindi.');
        redirect('yonetim/blog');
    }

    /** @return array<string,mixed>|null */
    private function validated(Request $request): ?array
    {
        $title = $request->string('title');
        $body = $request->string('body');
        if ($title === '' || $body === '') {
            Flash::error('Başlık ve içerik zorunludur.');
            return null;
        }
        return [
            'title'     => $title,
            'slug'      => slugify($request->string('slug') ?: $title),
            'excerpt'   => $request->string('excerpt') ?: mb_substr(strip_tags($body), 0, 160),
            'body'      => $body,
            'image'     => $request->string('image') ?: null,
            'published' => $request->bool('published') ? 1 : 0,
        ];
    }
}
