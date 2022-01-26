<?php

namespace Sylius\Bundle\GridBundle\Tests\Functional\Maker;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class MakerTestCase extends KernelTestCase
{
    /**
     * @before
     */
    public static function cleanupTmpDir(): void
    {
        (new Filesystem())->remove(self::tempDir());
    }

    protected static function tempDir(): string
    {
        return __DIR__.'/../../../test/tmp';
    }

    protected static function tempFile(string $path): string
    {
        return \sprintf('%s/%s', self::tempDir(), $path);
    }
}
