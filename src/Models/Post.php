<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Blog / news post.
 */
final class Post
{
    public int $id = 0;
    public string $title = '';
    public string $slug = '';
    public ?string $excerpt = null;
    public string $body = '';
    public ?string $image = null;
    public string $author = '';
    public int $views = 0;
    public int $comments = 0;
    public bool $published = true;
    public string $created_at = '';

    public static function fromRow(array $row): self
    {
        $p = new self();
        $p->id = (int) $row['id'];
        $p->title = (string) $row['title'];
        $p->slug = (string) $row['slug'];
        $p->excerpt = $row['excerpt'] ?? null;
        $p->body = (string) ($row['body'] ?? '');
        $p->image = $row['image'] ?? null;
        $p->author = (string) ($row['author'] ?? '');
        $p->views = (int) ($row['views'] ?? 0);
        $p->comments = (int) ($row['comments'] ?? 0);
        $p->published = (bool) ($row['published'] ?? 1);
        $p->created_at = (string) ($row['created_at'] ?? '');
        return $p;
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : asset('uploads/' . $this->image);
        }
        return '';
    }

    /** @return list<self> */
    public static function published(int $limit = 10, int $offset = 0): array
    {
        $rows = Database::select(
            'SELECT * FROM posts WHERE published = 1 ORDER BY id DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset)
        );
        return array_map([self::class, 'fromRow'], $rows);
    }

    public static function publishedCount(): int
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM posts WHERE published = 1');
    }

    /** @return list<self> */
    public static function all(): array
    {
        return array_map([self::class, 'fromRow'], Database::select('SELECT * FROM posts ORDER BY id DESC'));
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM posts WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = Database::selectOne('SELECT * FROM posts WHERE slug = ?', [$slug]);
        return $row ? self::fromRow($row) : null;
    }

    public static function incrementViews(int $id): void
    {
        Database::run('UPDATE posts SET views = views + 1 WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO posts (title, slug, excerpt, body, image, author, published, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['title'], $data['slug'], $data['excerpt'] ?? null, $data['body'] ?? '',
                $data['image'] ?? null, $data['author'] ?? '', $data['published'] ?? 1,
                date('Y-m-d H:i:s'),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE posts SET title = ?, slug = ?, excerpt = ?, body = ?, image = ?, published = ? WHERE id = ?',
            [$data['title'], $data['slug'], $data['excerpt'] ?? null, $data['body'] ?? '', $data['image'] ?? null, $data['published'] ?? 1, $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM posts WHERE id = ?', [$id]);
    }
}
