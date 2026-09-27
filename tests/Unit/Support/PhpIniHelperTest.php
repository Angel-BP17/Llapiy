<?php

namespace Tests\Unit\Support;

use App\Support\PhpIniHelper;
use PHPUnit\Framework\TestCase;

class PhpIniHelperTest extends TestCase
{
    public function test_parses_ini_sizes_correctly(): void
    {
        $this->assertSame(20 * 1024 * 1024, PhpIniHelper::parseSize('20M'));
        $this->assertSame(20 * 1024 * 1024, PhpIniHelper::parseSize('20MB'));
        $this->assertSame(2 * 1024 * 1024 * 1024, PhpIniHelper::parseSize('2G'));
        $this->assertSame(2 * 1024 * 1024 * 1024, PhpIniHelper::parseSize('2GB'));
        $this->assertSame(512 * 1024, PhpIniHelper::parseSize('512K'));
        $this->assertSame(512 * 1024, PhpIniHelper::parseSize('512KB'));
        $this->assertSame(1048576, PhpIniHelper::parseSize('1048576'));
        $this->assertSame(PHP_INT_MAX, PhpIniHelper::parseSize('-1'));
        $this->assertSame(PHP_INT_MAX, PhpIniHelper::parseSize(null));
        $this->assertSame(PHP_INT_MAX, PhpIniHelper::parseSize(''));
    }

    public function test_formats_bytes_correctly(): void
    {
        $this->assertSame('500 B', PhpIniHelper::formatBytes(500));
        $this->assertSame('1.5 KB', PhpIniHelper::formatBytes((int) (1.5 * 1024)));
        $this->assertSame('20 MB', PhpIniHelper::formatBytes(20 * 1024 * 1024));
        $this->assertSame('2 GB', PhpIniHelper::formatBytes(2 * 1024 * 1024 * 1024));
    }

    public function test_returns_valid_upload_max_size_and_kilobytes(): void
    {
        $bytes = PhpIniHelper::getMaxUploadFileSize();
        $this->assertGreaterThan(0, $bytes);

        $kb = PhpIniHelper::getMaxUploadFileSizeInKilobytes();
        $this->assertSame((int) floor($bytes / 1024), $kb);

        $formatted = PhpIniHelper::getMaxUploadFileSizeFormatted();
        $this->assertNotEmpty($formatted);
    }
}
