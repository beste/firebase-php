# CHANGELOG

**Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications.
If it saves you or your team time, please consider [sponsoring its development](https://github.com/sponsors/jeromegamez).

## [Unreleased]

### Breaking changes

* The SDK supports only actively supported PHP versions. As a result, support for PHP < 8.3 has been dropped;
  supported versions are 8.3, 8.4, and 8.5.
* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.
* Deprecated classes, methods and class constants have been removed.
* Type declarations have been simplified to reduce runtime overhead (e.g., `Stringable|string` to `string`).
* The transitional `Kreait\Firebase\Contract\Transitional\FederatedUserFetcher::getUserByProviderUid()` method
  has been moved into the `Kreait\Firebase\Contract\Auth` interface
* Realtime Database objects considered value objects have been made final and readonly

See **[UPGRADE-8.0](UPGRADE-8.0.md) for more details on the changes between 7.x and 8.0.**

## 7.x Changelog

https://github.com/kreait/firebase-php/blob/7.24.0/CHANGELOG.md
