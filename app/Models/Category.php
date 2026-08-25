<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function allVisible(): array
    {
        return Database::select(
            "SELECT * FROM categories WHERE is_visible = 1 ORDER BY sort_order, name",
            []
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT * FROM categories WHERE slug = :slug AND is_visible = 1",
            ['slug' => $slug]
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM categories ORDER BY sort_order, name",
            []
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::first(
            "SELECT * FROM categories WHERE id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        $slug = self::slugify($data['name']);
        $base = $slug;
        $counter = 1;
        while (self::findBySlug($slug)) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return Database::insert('categories', [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'is_visible' => (int) ($data['is_visible'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['name', 'description', 'image', 'is_visible', 'sort_order'];
        $update = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $update[$key] = $data[$key];
            }
        }

        if (isset($update['name'])) {
            $update['slug'] = self::slugify($update['name']);
        }

        if (empty($update)) {
            return;
        }

        Database::update('categories', $update, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    }

    private static function slugify(string $text): string
    {
        $text = preg_replace('/[^a-zA-Z0-9-]+/', '-', $text) ?? '';
        $text = trim($text, '-');
        $text = strtolower($text);
        return $text ?: 'category-' . time();
    }
}
