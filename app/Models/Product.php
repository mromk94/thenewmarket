<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Product
{
    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.slug = :slug AND p.status = 'published' AND p.visibility = 'public'",
            ['slug' => $slug]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::first(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.id = :id AND p.status = 'published' AND p.visibility = 'public'",
            ['id' => $id]
        );
    }

    public static function countPublished(array $filters = []): int
    {
        $where = ["p.status = 'published'", "p.visibility = 'public'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE :search OR p.short_description LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['vendor_id'])) {
            $where[] = 'p.vendor_id = :vendor_id';
            $params['vendor_id'] = $filters['vendor_id'];
        }

        if (!empty($filters['availability'])) {
            $where[] = 'p.inventory_status = :availability';
            $params['availability'] = $filters['availability'];
        }

        $whereSql = implode(' AND ', $where);

        $row = Database::first(
            "SELECT COUNT(*) as c
             FROM products p
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE {$whereSql}",
            $params
        );
        return (int) ($row['c'] ?? 0);
    }

    public static function findPublished(array $filters = []): array
    {
        $where = ["p.status = 'published'", "p.visibility = 'public'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE :search OR p.short_description LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['vendor_id'])) {
            $where[] = 'p.vendor_id = :vendor_id';
            $params['vendor_id'] = $filters['vendor_id'];
        }

        if (!empty($filters['availability'])) {
            $where[] = 'p.inventory_status = :availability';
            $params['availability'] = $filters['availability'];
        }

        $whereSql = implode(' AND ', $where);

        $order = match ($filters['sort'] ?? '') {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'newest' => 'p.created_at DESC',
            default => 'p.featured DESC, p.created_at DESC',
        };

        $sql = "SELECT p.*, c.name as category_name, v.business_name as vendor_name,
                       (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN vendors v ON v.id = p.vendor_id
                WHERE {$whereSql}
                ORDER BY {$order}";

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return Database::select($sql, $params);
    }

    public static function images(int $productId): array
    {
        return Database::select(
            "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_thumbnail DESC, sort_order",
            ['product_id' => $productId]
        );
    }

    public static function thumbnail(int $productId): ?string
    {
        $row = Database::first(
            "SELECT file_path FROM product_images WHERE product_id = :product_id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order",
            ['product_id' => $productId]
        );
        return $row['file_path'] ?? null;
    }

    public static function affiliateEligibleForVendor(int $vendorId): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name,
                    v_user.id as vendor_user_id,
                    (SELECT COALESCE(SUM(oi.quantity), 0)
                     FROM order_items oi
                     JOIN orders o ON o.id = oi.order_id
                     WHERE (oi.product_owner_id = v_user.id OR oi.affiliate_vendor_id = :vendor_id)
                       AND o.payment_status = 'paid'
                    ) as vendor_sales
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN users u ON u.id = p.owner_id
             LEFT JOIN vendors v ON v.id = :vendor_id
             LEFT JOIN users v_user ON v_user.id = v.user_id
             WHERE p.is_affiliate_eligible = 1
               AND p.status = 'published'
               AND p.visibility = 'public'
               AND u.email = 'admin@thenewage.local'
               AND p.id NOT IN (SELECT product_id FROM vendor_affiliate_products WHERE vendor_id = :vendor_id)
             ORDER BY p.name",
            ['vendor_id' => $vendorId]
        );
    }

    public static function findByVendor(int $vendorId): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.vendor_id = :vendor_id
               AND p.status = 'published'
               AND p.visibility = 'public'
             ORDER BY p.created_at DESC",
            ['vendor_id' => $vendorId]
        );
    }

    public static function findAffiliateProducts(int $vendorId): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name
             FROM products p
             JOIN vendor_affiliate_products vap ON vap.product_id = p.id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE vap.vendor_id = :vendor_id
               AND p.status = 'published'
               AND p.visibility = 'public'
             ORDER BY p.name",
            ['vendor_id' => $vendorId]
        );
    }

    public static function findFeatured(int $limit = 6): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name,
                   (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.status = 'published'
               AND p.visibility = 'public'
               AND p.featured = 1
             ORDER BY p.created_at DESC
             LIMIT {$limit}",
            []
        );
    }

    public static function findNewest(int $limit = 6): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name,
                   (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.status = 'published'
               AND p.visibility = 'public'
             ORDER BY p.created_at DESC
             LIMIT {$limit}",
            []
        );
    }

    public static function findByIdAdmin(int $id): ?array
    {
        return Database::first(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.id = :id",
            ['id' => $id]
        );
    }

    public static function findByOwner(int $ownerId): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name,
                   (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.owner_id = :owner_id
             ORDER BY p.created_at DESC",
            ['owner_id' => $ownerId]
        );
    }

    public static function findAll(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['owner_id'])) {
            $where[] = 'p.owner_id = :owner_id';
            $params['owner_id'] = $filters['owner_id'];
        }

        $whereSql = implode(' AND ', $where);

        return Database::select(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name, u.email as owner_email,
                   (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             LEFT JOIN users u ON u.id = p.owner_id
             WHERE {$whereSql}
             ORDER BY p.created_at DESC",
            $params
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

        $insert = [
            'owner_id' => $data['owner_id'],
            'vendor_id' => $data['vendor_id'] ?? null,
            'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'sku' => $data['sku'] ?? null,
            'price' => $data['price'],
            'compare_at_price' => $data['compare_at_price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'currency' => $data['currency'] ?? config('app.currency', 'USD'),
            'stock_qty' => $data['stock_qty'] ?? 0,
            'inventory_status' => $data['inventory_status'] ?? 'in_stock',
            'is_affiliate_eligible' => $data['is_affiliate_eligible'] ?? 0,
            'affiliate_commission_type' => $data['affiliate_commission_type'] ?? 'percentage',
            'affiliate_commission_value' => $data['affiliate_commission_value'] ?? 0,
            'affiliate_require_min_balance' => $data['affiliate_require_min_balance'] ?? 0,
            'affiliate_require_kyc' => $data['affiliate_require_kyc'] ?? 0,
            'affiliate_require_min_sales' => $data['affiliate_require_min_sales'] ?? 0,
            'status' => $data['status'] ?? 'pending',
            'visibility' => $data['visibility'] ?? 'public',
            'featured' => $data['featured'] ?? 0,
        ];

        return Database::insert('products', $insert);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = [
            'name', 'description', 'short_description', 'sku', 'price', 'compare_at_price',
            'sale_price', 'currency', 'stock_qty', 'inventory_status', 'category_id',
            'is_affiliate_eligible', 'affiliate_commission_type', 'affiliate_commission_value',
            'affiliate_require_min_balance', 'affiliate_require_kyc', 'affiliate_require_min_sales',
            'status', 'visibility', 'featured',
        ];

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

        Database::update('products', $update, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM product_images WHERE product_id = :id", ['id' => $id]);
        Database::query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::update('products', ['status' => $status], 'id = ?', [$id]);
    }

    public static function setFeatured(int $id, bool $featured): void
    {
        Database::update('products', ['featured' => $featured ? 1 : 0], 'id = ?', [$id]);
    }

    public static function setVisibility(int $id, string $visibility): void
    {
        Database::update('products', ['visibility' => $visibility], 'id = ?', [$id]);
    }

    public static function setAllImagesNotThumbnail(int $productId): void
    {
        Database::query(
            "UPDATE product_images SET is_thumbnail = 0 WHERE product_id = :product_id",
            ['product_id' => $productId]
        );
    }

    public static function attachImage(int $productId, string $path, bool $isThumbnail = true, int $sortOrder = 1): void
    {
        Database::insert('product_images', [
            'product_id' => $productId,
            'file_path' => $path,
            'is_thumbnail' => $isThumbnail ? 1 : 0,
            'sort_order' => $sortOrder,
        ]);
    }

    public static function findOnSale(int $limit = 8): array
    {
        return Database::select(
            "SELECT p.*, c.name as category_name, v.business_name as vendor_name,
                   (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             WHERE p.status = 'published'
               AND p.visibility = 'public'
               AND ((p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price)
                    OR (p.compare_at_price IS NOT NULL AND p.compare_at_price > p.price))
             ORDER BY p.price ASC
             LIMIT {$limit}",
            []
        );
    }

    private static function slugify(string $text): string
    {
        $text = preg_replace('/[^a-zA-Z0-9-]+/', '-', $text) ?? '';
        $text = trim($text, '-');
        $text = strtolower($text);
        return $text ?: 'product-' . time();
    }
}
