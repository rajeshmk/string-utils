<?php

declare(strict_types=1);

namespace Hatchyu\String\Tests;

use Hatchyu\String\StrTo;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class StrToTest extends TestCase
{
    public function testSnakeConvertsWordsToUnderscores(): void
    {
        self::assertSame('foo_bar', StrTo::snake('Foo Bar'));
    }

    public function testSlugTransliteratesAndLimitsWords(): void
    {
        self::assertSame('uber-cafe', StrTo::slug('Uber Cafe', 20, 'en'));
        self::assertSame('two-words', StrTo::slug('two words here', 10, 'en'));
        self::assertSame('name-at-example-com', StrTo::slug('name@example.com'));
    }

    public function testSlugHandlesMixedCaseIdentifiers(): void
    {
        self::assertSame('oauth2-token', StrTo::slug('OAuth2Token'));
        self::assertSame('ipv6-address', StrTo::slug('IPv6Address'));
    }

    public function testSlugNormalizesNonWordSeparators(): void
    {
        self::assertSame('foo-bar', StrTo::slug('foo__bar'));
        self::assertSame('simple-xml-parser', StrTo::slug('simpleXML_Parser'));
    }

    public function testSlugTransliteratesUnicodeCharacters(): void
    {
        self::assertSame('uber-cafe', StrTo::slug('ÜberCafe'));
    }
}
