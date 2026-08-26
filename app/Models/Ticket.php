<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Ticket
{
    public static function findById(int $id): ?array
    {
        return Database::first(
            "SELECT t.*, u.email as user_email, v.business_name as vendor_name
             FROM tickets t
             JOIN users u ON u.id = t.user_id
             LEFT JOIN vendors v ON v.id = t.vendor_id
             WHERE t.id = :id",
            ['id' => $id]
        );
    }

    public static function forUser(int $userId): array
    {
        return Database::select(
            "SELECT t.*, u.email as user_email, v.business_name as vendor_name
             FROM tickets t
             JOIN users u ON u.id = t.user_id
             LEFT JOIN vendors v ON v.id = t.vendor_id
             WHERE t.user_id = :user_id
             ORDER BY t.updated_at DESC, t.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function forVendor(int $vendorId): array
    {
        return Database::select(
            "SELECT t.*, u.email as user_email, v.business_name as vendor_name
             FROM tickets t
             JOIN users u ON u.id = t.user_id
             JOIN vendors v ON v.id = t.vendor_id
             WHERE t.vendor_id = :vendor_id
             ORDER BY t.updated_at DESC, t.created_at DESC",
            ['vendor_id' => $vendorId]
        );
    }

    public static function all(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $where[] = 't.priority = :priority';
            $params['priority'] = $filters['priority'];
        }

        $whereSql = implode(' AND ', $where);

        return Database::select(
            "SELECT t.*, u.email as user_email, v.business_name as vendor_name
             FROM tickets t
             JOIN users u ON u.id = t.user_id
             LEFT JOIN vendors v ON v.id = t.vendor_id
             WHERE {$whereSql}
             ORDER BY t.updated_at DESC, t.created_at DESC",
            $params
        );
    }

    public static function countOpen(): int
    {
        $row = Database::first(
            "SELECT COUNT(*) as c FROM tickets WHERE status IN ('open', 'in_progress')",
            []
        );
        return (int) ($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        return Database::insert('tickets', $data);
    }

    public static function updateStatus(int $id, string $status): void
    {
        Database::update('tickets', ['status' => $status], 'id = ?', [$id]);
    }

    public static function updatePriority(int $id, string $priority): void
    {
        Database::update('tickets', ['priority' => $priority], 'id = ?', [$id]);
    }

    public static function addReply(int $ticketId, int $userId, string $message, bool $isAdmin = false): int
    {
        return Database::insert('ticket_replies', [
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'is_admin' => $isAdmin ? 1 : 0,
            'message' => $message,
        ]);
    }

    public static function replies(int $ticketId): array
    {
        return Database::select(
            "SELECT tr.*, u.email, u.id as user_id
             FROM ticket_replies tr
             JOIN users u ON u.id = tr.user_id
             WHERE tr.ticket_id = :ticket_id
             ORDER BY tr.created_at ASC",
            ['ticket_id' => $ticketId]
        );
    }
}
