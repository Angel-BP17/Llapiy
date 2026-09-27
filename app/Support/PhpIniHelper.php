<?php

namespace App\Support;

class PhpIniHelper
{
    /**
     * Parse an ini file size string (e.g. '20M', '2G', '512K', '20MB') to bytes.
     */
    public static function parseSize(?string $size): int
    {
        if ($size === null || $size === '' || $size === '-1') {
            return PHP_INT_MAX;
        }

        $size = trim($size);

        if (strlen($size) > 1 && str_ends_with(strtoupper($size), 'B') && ! is_numeric(substr($size, -2, 1))) {
            $size = substr($size, 0, -1);
        }

        $unit = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        return match ($unit) {
            'G' => $value * 1024 * 1024 * 1024,
            'M' => $value * 1024 * 1024,
            'K' => $value * 1024,
            default => (int) $size,
        };
    }

    /**
     * Get the maximum allowed upload file size in bytes based on php.ini directives.
     */
    public static function getMaxUploadFileSize(): int
    {
        $uploadMax = static::parseSize(ini_get('upload_max_filesize') ?: null);
        $postMax = static::parseSize(ini_get('post_max_size') ?: null);

        return min($uploadMax, $postMax);
    }

    /**
     * Get maximum allowed upload file size in kilobytes (for Laravel max validation).
     */
    public static function getMaxUploadFileSizeInKilobytes(): int
    {
        return (int) floor(static::getMaxUploadFileSize() / 1024);
    }

    /**
     * Format bytes into a human-readable string (e.g. '20 MB', '2 GB').
     */
    public static function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024 * 1024), $precision).' GB';
        }

        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), $precision).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, $precision).' KB';
        }

        return $bytes.' B';
    }

    /**
     * Get the formatted maximum upload size string.
     */
    public static function getMaxUploadFileSizeFormatted(): string
    {
        return static::formatBytes(static::getMaxUploadFileSize());
    }
}
