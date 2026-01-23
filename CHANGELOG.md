# CHANGELOG

**Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications.
If it saves you or your team time, please consider [sponsoring its development](https://github.com/sponsors/jeromegamez).

## 8.1.0 - 2026-01-23

* Added support for `firebase/php-jwt:^7.0.2` 

## 8.0.0 - 2026-01-08

### Security improvements

* Added `#[SensitiveParameter]` attributes to methods handling sensitive data (passwords, tokens, private keys) 
  to prevent them from appearing in stack traces and error logs.

### Breaking changes

* The SDK supports only actively supported PHP versions. As a result, support for PHP < 8.3 has been dropped;
  supported versions are 8.3, 8.4, and 8.5.
* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.
* Deprecated classes, methods and class constants have been removed.
* Method arguments are now fully type-hinted
* Type declarations have been simplified to reduce runtime overhead (e.g., `Stringable|string` to `string`).
* The transitional `Kreait\Firebase\Contract\Transitional\FederatedUserFetcher::getUserByProviderUid()` method
  has been moved into the `Kreait\Firebase\Contract\Auth` interface
* Realtime Database objects considered value objects have been made final and readonly
* `psr/log` has been moved from runtime dependencies to development dependencies
* `Kreait\Firebase\Contract\Messaging::BATCH_MESSAGE_LIMIT` constant has been removed
* Exception codes are no longer preserved when wrapping exceptions
* `Kreait\Firebase\Messaging\CloudMessage` builder methods have been renamed to follow the `with*` pattern:
  `toToken()` -> `withToken()`, `toTopic()` -> `withTopic()`, `toCondition()` -> `withCondition()`.
  The old methods are deprecated but still available as aliases.

See **[UPGRADE-8.0](UPGRADE-8.0.md) for more details on the changes between 7.x and 8.0.**

## 7.x Changelog

https://github.com/kreait/firebase-php/blob/7.24.0/CHANGELOG.md
