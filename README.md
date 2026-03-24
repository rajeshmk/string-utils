# Hatchyu String Utils

A powerful PHP string manipulation library that handles complex slugging, casing, and unique hash generation with precision. It is designed to be smarter than standard implementations like Laravel's `Str` by intelligently handling acronyms, camelCase transitions, and multi-language transliteration.

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

## Usage

### Slugging & Casing

```php
use Hatchyu\String\StrTo;

// Basic Slugging
echo StrTo::slug('DBSettings'); // db-settings
echo StrTo::slug('apiV2Endpoint'); // api-v2-endpoint

// Custom Character Support
echo StrTo::slug('rajesh@example.com'); // rajesh-at-example-com

// Monotonic Path Conversion
echo StrTo::dotPath('admin/ModuleName/TestNamespace'); // admin.module-name.test-namespace
```

### Compound Words Protection

Preserve specific words that should not be split by the camelCase logic.

```php
StrTo::setCompoundWords(['MySQL', 'i18n']);

echo StrTo::slug('MySQL8Adapter'); // mysql8-adapter (not my-sql8-adapter)
echo StrTo::slug('i18nConfig');    // i18n-config (not i18-n-config)
```

### Unique Hashes

```php
use Hatchyu\String\Hash;

echo Hash::uniqueHash(40); // e.g. 1a2b3c... (monotonic time-based)
```

## The Difference: Laravel vs. Hatchyu

Below is a comparison of how this package handles complex strings compared to the default Laravel `Str::slug` implementation.

| Seed | Laravel Slug | Hatchyu Slug |
| :--- | :--- | :--- |
| DBSettings | dbsettings | **db-settings** |
| hasConsecutiveCAPS | hasconsecutivecaps | **has-consecutive-caps** |
| NewHDDModule | newhddmodule | **new-hdd-module** |
| has123chars | has123chars | **has123-chars** |
| apiV2Endpoint | apiv2endpoint | **api-v2-endpoint** |
| XMLHttpRequest2Handler | xmlhttprequest2handler | **xml-http-request2-handler** |
| JSON2XMLConverter | json2xmlconverter | **json2-xml-converter** |
| MySQL8Adapter | mysql8adapter | **my-sql8-adapter** |
| HTTPRequest202Accepted | httprequest202accepted | **http-request202-accepted** |
| i18nConfig | i18nconfig | **i18-n-config** |
| OpenAI4oMiniModel | openai4ominimodel | **open-ai4-o-mini-model** |
| 2GB RAMWillBe 2gb ram | 2gb-ramwillbe-2gb-ram | **2-gb-ram-will-be-2-gb-ram** |
| 你好世界 !@£alph$%^&*()hey | at-psalphhey | **ni-hao-shi-jie-at-alph-hey** |
| മലയാളത്തിൽ എഴുതിയാലെന്താ? | | **malayalattil-elutiyalenta** |
| यह हिंदी का अनुवाद भी कर सकता है | yaha-hatha-ka-anavatha-bha-kara-sakata-ha | **yaha-hindi-ka-anuvada-bhi-kara-sakata-hai** |

## License

MIT
