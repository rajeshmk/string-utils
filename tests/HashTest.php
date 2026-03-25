<?php

declare(strict_types=1);

namespace Hatchyu\String\Tests;

use Hatchyu\String\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 *
 * @coversNothing
 */
class HashTest extends TestCase
{
    #[Test]
    public function itGeneratesTheRequestedLength(): void
    {
        self::assertSame(24, strlen(Hash::uniqueHash(24)));
        self::assertSame(40, strlen(Hash::uniqueHash()));
    }

    #[Test]
    public function itRejectsLengthsBelowTheMinimum(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Minimum length must be 24 characters.');

        Hash::uniqueHash(23);
    }
}
