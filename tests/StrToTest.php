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
    protected function tearDown(): void
    {
        StrTo::setCompoundWords([]);
    }

    public function testSnakeConvertsWordsToUnderscores(): void
    {
        self::assertSame('foo_bar', StrTo::snake('Foo Bar'));
    }

    public function testHeadlineAliasesWords(): void
    {
        self::assertSame(StrTo::words('dbSettings'), StrTo::headline('dbSettings'));
        self::assertSame('DB Settings', StrTo::headline('dbSettings'));
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

    public function testSlugCanUseConfiguredCompoundWords(): void
    {
        StrTo::setCompoundWords(['B2B', 'OAuth2', 'IPv6']);

        self::assertSame('b2b-lead-api', StrTo::slug('B2BLeadAPI'));
        self::assertSame('oauth2-callback-url', StrTo::slug('OAuth2CallbackURL'));
        self::assertSame('ipv6-address', StrTo::slug('IPv6Address'));
    }

    public function testConfiguredCompoundWordsCanCollideWithLiteralPlaceholderLookingInput(): void
    {
        StrTo::setCompoundWords(['B2B', 'OAuth2', 'IPv6']);

        self::assertSame('b2b-token', StrTo::slug('compoundwordplaceholder0000Token'));
        self::assertSame('oauth2-address', StrTo::slug('compoundwordplaceholder0001Address'));
        self::assertSame('ipv6-lead-api', StrTo::slug('compoundwordplaceholder0002LeadAPI'));
        self::assertSame('b2b-x-token', StrTo::slug('compoundwordplaceholder0000xToken'));
        self::assertSame(
            'prefix-oauth2-suffix',
            StrTo::slug('prefix-compoundwordplaceholder0001-suffix')
        );
    }
}
