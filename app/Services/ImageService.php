<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\HttpException;

class ImageService
{
    private const ALLOWED_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    private const MAX_SIZE_BYTES = 2097152; // 2MB

    public static function upload(array $file, string $subDir = 'products', int $maxWidth = 1600): array
    {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new HttpException('Please upload a valid image.', 422);
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            throw new HttpException('Image must be smaller than 2MB.', 422);
        }

        $mime = mime_content_type($file['tmp_name']);
        if ($mime === false || !isset(self::ALLOWED_TYPES[$mime])) {
            throw new HttpException('Only JPG, PNG, GIF, and WebP images are allowed.', 422);
        }

        $ext = self::ALLOWED_TYPES[$mime];
        $filename = strtolower(bin2hex(random_bytes(8))) . '_' . time() . '.' . $ext;

        $baseDir = PUBLIC_PATH . '/assets/images/' . $subDir;
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0755, true)) {
                throw new HttpException('Could not create upload directory.', 500);
            }
        }

        $target = $baseDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new HttpException('Image upload failed.', 500);
        }

        // Basic resize for very large images to reduce bandwidth
        if (extension_loaded('gd')) {
            self::resizeIfNeeded($target, $maxWidth);
        }

        return [
            'filename' => $filename,
            'path' => 'images/' . $subDir . '/' . $filename,
            'mime' => $mime,
        ];
    }

    private static function resizeIfNeeded(string $path, int $maxWidth): void
    {
        [$width, $height, $type] = @getimagesize($path);
        if ($width === false || $width <= $maxWidth) {
            return;
        }

        $newHeight = (int) round($height * ($maxWidth / $width));

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };

        if (!$src) {
            return;
        }

        $dst = imagecreatetruecolor($maxWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dst, $path, 85),
            IMAGETYPE_PNG => imagepng($dst, $path),
            IMAGETYPE_GIF => imagegif($dst, $path),
            IMAGETYPE_WEBP => imagewebp($dst, $path, 85),
            default => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}
