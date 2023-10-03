# Changelog

## Unreleased

## v5.0.5 (2023-10-03)

### What's fixed

* Fixed duplicate digital product fields being added to variant options #131 #132 by @duncanmcclean

## v5.0.4 (2023-09-29)

### What's fixed

* Fixed a few issues with how fields are added to product blueprints #129 #130 by @duncanmcclean

## v5.0.3 (2023-08-07)

### What's improved

* Improved license verification queries #126 #128 by @barchard

### What's fixed

* Refactored order completion check in `VerificationController` #127 by @duncanmcclean
* Fixed failing tests

## v5.0.2 (2023-05-18)

### What's fixed

- Fixed the digital downloads email #124

## v5.0.1 (2023-05-16)

### What's fixed

- Added a missing import #122 #123

## v5.0.0 (2023-05-09)

- Simple Commerce v5.0 support #120

## v4.0.0 (2022-09-10)

- Simple Commerce v4.0 support #118

## v3.2.1 (2022-06-02)

- Regenerate `composer.lock` file so it works - I really shouldn't be working on a bank holiday 😅

## v3.2.0 (2022-06-02)

### What's new

- Support for Simple Commerce v3.1 and v3.2

## v3.1.0 (2022-05-21)

### What's new

- Support for Product Variants #116

## v3.0.0 (2022-04-28)

Updated for [Simple Commerce v3.0](https://github.com/duncanmcclean/simple-commerce/releases/tag/v3.0.0) 🎉

## v2.3.2 (2022-04-01)

### What's new

- Config file

## v2.3.1 (2022-03-31)

### What's fixed

- Fixed an issue when downloading products with Statamic 3.3

## v2.3.0 (2022-03-15)

### What's new

- Adds support for Simple Commerce v2.4

## v2.2.3 (2021-12-02)

### What's fixed

- Fixed issue where download history wasn't being saved #103 by @digitalam025

## v2.2.2 (2021-08-30)

### What's fixed

- Fixed issue with email view, where it would call a method that doesn't exist
- An exception is now thrown if product does not have any digital downloads

## v2.2.1 (2021-07-05)

### What's improved

- Added a 'breaking changes' warning when updating to v2.2.0 #95

## v2.2.0 (2021-06-30)

**⚠️ This update contains a breaking change.**

This release introduces a few changes to the way email notifications work. Essentially, now the Digital Products addon hooks into Simple Commerce's existing [notifications system](https://simple-commerce.duncanmcclean.com/notifications).

To keep notifications from sending, add the following to the `notifications` array in your `config/simple-commerce.php` file.

```php
'notifications' => [
    'digital_download_ready' => [
        \DoubleThreeDigital\DigitalProducts\Notifications\DigitalDownloadsNotification::class => [
            'to' => 'customer',
        ],
    ],
],
```

## v2.1.0 (2021-05-10)

> **Note**: Before updating, you'll need to upgrade to Simple Commerce v2.3. Review the [upgrade guide](https://simple-commerce.duncanmcclean.com/update-guide).

### What's new?

- Support for Simple Commerce v2.3 #87
- Digital downloads are now zipped up, instead of downloading the raw file.
- You can attach multiple assets to a product for downloading. #62

## v2.0.4 (2021-04-21)

- [fix] Fixed bug with digital product detection sometimes being off #88

## v2.0.3

- [new] Limit the number of downloads per product #71

## v2.0.2

- [new] [Download History](https://github.com/duncanmcclean/sc-digital-products#download-history)
- [new] Supports Statamic 3.1

## v2.0.1

- v2 should only support v2.2

## v2.0.0

- Fully compatible with Simple Commerce v2.2
- Dependency updates

## v1.0.6

- Bump dependencies

## v1.0.5

- [new] Supports Simple Commerce v2.1
- [fix] The Verification endpoint will now get orders from the [configured order collection](https://simple-commerce.duncanmcclean.com//configuring#collections-amp-taxonomies), instead of being hard coded.
- [fix] Fixed bug where enabling the Statamic API wouldn't enable the Verification endpoint
- `LicenseKeyRepository` is now bound by `Statamic::repository`
- Added tests and did some refactoring

## v1.0.4

- Fix dependency issues.

## v1.0.3

- Just realised we don't technically support v2 of Simple Commerce yet 🤦

## v1.0.2

- Officially supports Statamic 3 (not beta)

## v1.0.1

- [new] A REST endpoint for license key verification

## v1.0.0

- Initial release of Digital Products addon for Statamic.
