<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $cfg = config('database');
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $cfg['host'],
                $cfg['port'],
                $cfg['database'],
                $cfg['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $options);
            } catch (PDOException $e) {
                Logger::error('Database connection failed: ' . $e->getMessage());
                throw new \RuntimeException('Database connection failed.');
            }
        }

        return self::$pdo;
    }

    public static function pdo(): PDO
    {
        return self::$pdo ?? self::connect();
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function first(string $sql, array $params = []): ?array
    {
        $sql = rtrim($sql, '; ');
        if (!preg_match('/\sLIMIT\s+\d+(\s*,\s*\d+)?\s*$/i', $sql)) {
            $sql .= ' LIMIT 1';
        }
        $rows = self::select($sql, $params);
        return $rows[0] ?? null;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $fields = implode('`, `', $cols);
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));

        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        self::query($sql, array_values($data));

        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = [];
        foreach (array_keys($data) as $col) {
            $set[] = "`{$col}` = ?";
        }
        $setSql = implode(', ', $set);

        $sql = "UPDATE `{$table}` SET {$setSql} WHERE {$where}";
        self::query($sql, array_merge(array_values($data), $whereParams));

        return self::query("SELECT ROW_COUNT()", [])->fetchColumn();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        self::query("DELETE FROM `{$table}` WHERE {$where}", $params);
        return self::query("SELECT ROW_COUNT()", [])->fetchColumn();
    }

    public static function inTransaction(): bool
    {
        return self::pdo()->inTransaction();
    }

    public static function beginTransaction(): bool
    {
        if (self::inTransaction()) {
            return true;
        }
        return self::pdo()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::pdo()->commit();
    }

    public static function rollBack(): bool
    {
        return self::pdo()->rollBack();
    }

    public static function lastInsertId(): string
    {
        return self::pdo()->lastInsertId();
    }

    public static function exists(string $sql, array $params = []): bool
    {
        return (bool) self::first($sql, $params);
    }
}
