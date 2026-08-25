<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Page
{
    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM pages ORDER BY title",
            []
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT * FROM pages WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::first(
            "SELECT * FROM pages WHERE id = :id",
            ['id' => $id]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::update('pages', [
            'title' => $data['title'],
            'content' => $data['content'],
            'meta_description' => $data['meta_description'] ?? null,
            'is_active' => (int) ($data['is_active'] ?? 1),
        ], 'id = ?', [$id]);
    }
}
