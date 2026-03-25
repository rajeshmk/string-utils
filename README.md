# Hatchyu String Utils

A powerful PHP string manipulation library for smarter slugging, casing, and monotonic unique hash generation.

The main goal of this package is to improve code-like and identifier-like strings that Laravel's default `Str::slug()` tends to flatten too aggressively. It handles camelCase, StudlyCase, acronym boundaries, number-to-word transitions, Unicode transliteration, and optional compound-word protection.

## Features

- **Smart Slugging**: Intelligent splitting of acronyms, camelCase, and numeric boundaries.
- **Compound Words Support**: Protect specific brand names (e.g., "MySQL", "iPhone") from being split.
- **Multilingual Support**: Robust transliteration for Greek, Malayalam, Hindi, Arabic, Chinese, Japanese, and more.
- **SEO Optimized**: Handles symbols like `@` as `-at-` and respects word boundaries when limiting length.
- **Unique Hash Generation**: Monotonic time-based unique hashes with custom lengths.

## Installation

```bash
composer require hatchyu/string-utils
```

## Requirements

- PHP `^8.3`
- `ext-intl` is optional, but recommended if you want transliteration such as `ÜberCafe -> uber-cafe` or non-Latin text converted into Latin slugs

Without `ext-intl`, the slug helpers still work, but transliteration depends only on the raw input that PHP receives.

## Usage

```php
use Hatchyu\String\StrTo;

echo StrTo::slug('DBSettings');         // db-settings
echo StrTo::slug('rajesh@example.com'); // rajesh-at-example-com
echo StrTo::headline('DBSettings');     // DB Settings
```

## StrTo Helpers

### Slugging helpers

```php
StrTo::slug('apiV10Endpoint');              // api-v10-endpoint
StrTo::snake('apiV10Endpoint');             // api_v10_endpoint
StrTo::kebab('apiV10Endpoint');             // api-v10-endpoint
StrTo::dotted('moduleDirectory/FileName');  // module.directory.file.name
StrTo::dotPath('moduleDirectory/FileName'); // module-directory.file-name
```

- `slug(string $string, int $maxLength = 120, string $lang = 'en'): string`
  Produces a URL-friendly slug.
  Keeps `@` as `-at-`, transliterates to ASCII for English when `ext-intl` is installed, preserves word boundaries better than a plain flatten-and-replace approach, and trims to the first wrapped segment.
- `snake(string $string): string`
  Same smart word splitting, with `_` separators.
- `kebab(string $string): string`
  Same smart word splitting, with `-` separators.
- `dotted(string $string): string`
  Same smart word splitting, with `.` separators.
- `dotPath(string $path): string`
  Converts slash-separated or namespace-like paths into dot notation with kebab-cased segments.

### Casing helpers

```php
StrTo::title('hatchyu API EndPoint');    // Hatchyu Api End Point
StrTo::words('hatchyu API EndPoint');    // Hatchyu API End Point
StrTo::headline('hatchyu API EndPoint'); // Hatchyu API End Point
StrTo::studly('hatchyu API EndPoint');   // HatchyuApiEndPoint
StrTo::camel('hatchyu API EndPoint');    // hatchyuApiEndPoint
StrTo::upper('hatchyu API EndPoint');    // HATCHYU API ENDPOINT
StrTo::lower('hatchyu API EndPoint');    // hatchyu api endpoint
StrTo::ucfirst('hatchyu API EndPoint');  // Hatchyu API EndPoint
StrTo::lcfirst('hatchyu API EndPoint');  // hatchyu API EndPoint
```

- `title(string $string): string`
  Converts the string to title case after smart word splitting.
- `words(string $string): string`
  A smarter `ucwords()` variant that keeps fully-uppercase technical tokens like `DB`.
- `headline(string $string): string`
  Alias of `words()`.
- `studly(string $string): string`
  Converts the string to StudlyCase.
- `camel(string $string): string`
  Converts the string to camelCase.
- `upper(string $string): string`
  Converts the string to uppercase.
- `lower(string $string): string`
  Converts the string to lowercase.
- `ucfirst(string $string): string`
  Uppercases only the first character.
- `lcfirst(string $string): string`
  Lowercases only the first character.
- `substr(string $string, int $start, ?int $length = null): string`
  UTF-8 safe substring helper.

## Compound Words

Some business or technical terms are better kept intact instead of being split by the generic regex rules. For that, you can register compound words once:

```php
use Hatchyu\String\StrTo;

StrTo::setCompoundWords(['B2B', 'MySQL', 'OAuth2', 'IPv6', 'i18n', 'GraphQL', 'YouTube', 'macOS', 'iPhone']);

StrTo::slug('B2BLeadAPI');        // b2b-lead-api
StrTo::slug('MySQL8Adapter');     // mysql8-adapter
StrTo::slug('OAuth2CallbackURL'); // oauth2-callback-url
StrTo::slug('IPv6Address');       // ipv6-address
StrTo::slug('i18nConfig');        // i18n-config
StrTo::slug('GraphQLAPI');        // graphql-api
StrTo::slug('YouTubeAPIClient');  // youtube-api-client
StrTo::slug('macOSConfig');       // macos-config
StrTo::slug('iPhoneCase');        // iphone-case
```

This is useful when your SEO slugs need to reflect business or domain vocabulary more precisely than generic splitting rules can infer on their own.

Important note:

- `setCompoundWords()` changes package-wide static state for the current PHP process
- if you call it, later `StrTo` calls in the same process will use that configured list until you replace or clear it

To clear the configured list:

```php
StrTo::setCompoundWords([]);
```

## Laravel Comparison

The table below compares Laravel's default `Str::slug()` behavior with the default `StrTo::slug()` behavior, without any configured compound words.

| Seed | Laravel Str::slug() | Hatchyu StrTo::slug() |
| :--- | :--- | :--- |
| DBSettings | dbsettings | db-settings |
| hasConsecutiveCAPS | hasconsecutivecaps | has-consecutive-caps |
| NewHDDModule | newhddmodule | new-hdd-module |
| apiV2Endpoint | apiv2endpoint | api-v2-endpoint |
| usingSHA256Hashing | usingsha256hashing | using-sha256-hashing |
| Version2API | version2api | version2-api |
| XMLHttpRequest2Handler | xmlhttprequest2handler | xml-http-request2-handler |
| IPv6Address | ipv6address | ipv6-address |
| parseURL2HTML | parseurl2html | parse-url2-html |
| JSON2XMLConverter | json2xmlconverter | json2-xml-converter |
| userID42Profile | userid42profile | user-id42-profile |
| MySQL8Adapter | mysql8adapter | my-sql8-adapter |
| SimpleXMLParser | simplexmlparser | simple-xml-parser |
| CSS3Parser | css3parser | css3-parser |
| admin/ModuleName/File.php | adminmodulenamefilephp | admin-module-name-file-php |
| WebERP | weberp | web-erp |
| HDDCapacity | hddcapacity | hdd-capacity |
| testUPPERIsOKNow | testupperisoknow | test-upper-is-ok-now |
| 2GB RAMWillBe 2gb ram | 2gb-ramwillbe-2gb-ram | 2-gb-ram-will-be-2-gb-ram |
| 123number and number123 small | 123number-and-number123-small | 123-number-and-number123-small |
| FirstCaps and lastcapYes | firstcaps-and-lastcapyes | first-caps-and-lastcap-yes |
| CreatedAt | createdat | created-at |
| Τάχιστη αλώπηξ βαφής ψημένη γη, | takhisti-alwpiks-vafis-psimeni-ghi | tachiste-alopex-baphes-psemene-ge |
|  δρασκελίζει υπέρ νωθρού κυνός | draskelizei-iper-nothrou-kinos | draskelizei-yper-nothrou-kynos |
| Τάχιστη Αλώπηξ Βαφήσ Ψημένη Γη, | takhisti-alwpiks-bafis-psimeni-gi | tachiste-alopex-baphes-psemene-ge |
|  Δρασκελίζει Υπέρ Νωθρού Κυνόσ | draskelizei-yper-nothrou-kinos | draskelizei-yper-nothrou-kynos |
| more...Dots.... yes. | moredots-yes | more-dots-yes |
| ഇതിന് മലയാളം പരിഭാഷപ്പെടുത്താനും കഴിയും |  | itin-malayalam-paribhasappetuttanum-kaliyum |
| यह हिंदी का अनुवाद भी कर सकता है | yaha-hatha-ka-anavatha-bha-kara-sakata-ha | yaha-hindi-ka-anuvada-bhi-kara-sakata-hai |
| இது தமிழையும் மொழிபெயர்க்கலாம் |  | itu-tamilaiyum-molipeyarkkalam |
| ఇది తెలుగును కూడా అనువదించగలదు |  | idi-telugunu-kuda-anuvadincagaladu |
| ಇದು ಕನ್ನಡವನ್ನೂ ಅನುವಾದಿಸಬಹುದು |  | idu-kannadavannu-anuvadisabahudu |
| هذا يمكن أيضا أن يترجم العربية | htha-ymkn-ayda-an-ytrgm-alaarby | hdha-ymkn-ayda-an-ytrjm-al-rbyt |
| 这个也可以翻译成中文 |  | zhe-ge-ye-ke-yi-fan-yi-cheng-zhong-wen |
| これは日本語も翻訳できます |  | koreha-ri-ben-yumo-fan-yidekimasu |
| `rajesh@example.com` | rajesh-at-examplecom | rajesh-at-example-com |

## Default Slug vs Compound-Word-Aware Slug

The generic slug rules already improve many technical strings, but they cannot always infer business-specific or brand-specific tokens perfectly. When you explicitly register compound words, the slug can become even more intentional.

Examples:

```php
StrTo::setCompoundWords(['B2B', 'MySQL', 'i18n', 'GraphQL', 'YouTube', 'iPhone']);
```

| Seed | Default StrTo::slug() | With setCompoundWords(...) |
| :--- | :--- | :--- |
| B2BLeadAPI | b2-blead-api | b2b-lead-api |
| MySQL8Adapter | my-sql8-adapter | mysql8-adapter |
| GraphQL API | graph-ql-api | graphql-api |
| YouTube APIClient | you-tube-api-client | youtube-api-client |
| i18nConfig | i18-n-config | i18n-config |
| iPhoneCase | i-phone-case | iphone-case |

The important idea is not that every string needs configured compounds. The default behavior should stay generic. Compound words are there when your business vocabulary deserves better-than-generic splitting.

## Hash Helper

```php
use Hatchyu\String\Hash;

echo Hash::uniqueHash(40);
```

- `uniqueHash(int $length = 40): string`
  Generates a monotonic unique hash with a time-derived prefix and random padding.
- minimum supported length is `24`

## License

MIT
