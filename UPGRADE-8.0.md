# Upgrade from 7.x to 8.0

## Introduction

This is a major release, but its aim is to provide as much backward compatibility as possible to ease upgrades
from 7.x to 8.0.

## Notable changes

* The SDK supports only actively supported PHP versions. As a result, support for PHP < 8.3 has been dropped;
  supported versions are 8.3, 8.4, and 8.5.
* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.

## Complete list of breaking changes

The following list has been generated with [roave/backward-compatibility-check](https://github.com/Roave/BackwardCompatibilityCheck).

```
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from 0 to NULL
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from 0 to NULL
[BC] CHANGED: The number of required arguments for Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() increased from 1 to 2
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to Stringable|string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant Stringable|string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant Stringable|string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant Stringable|string
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\ConditionalValue#value() changed from no type to string|array
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\TagColor#__toString() changed from no type to string
[BC] REMOVED: Class Kreait\Firebase\Contract\DynamicLinks has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\AnalyticsInfo has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\AnalyticsInfo\GooglePlayAnalytics has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\AnalyticsInfo\ITunesConnectAnalytics has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\AndroidInfo has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\CreateDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\CreateDynamicLink\FailedToCreateDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\DynamicLinkStatistics has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\EventStatistics has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\GetStatisticsForDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\GetStatisticsForDynamicLink\FailedToGetStatisticsForDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\IOSInfo has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\NavigationInfo has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\ShortenLongDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\ShortenLongDynamicLink\FailedToShortenLongDynamicLink has been deleted
[BC] REMOVED: Class Kreait\Firebase\DynamicLink\SocialMetaTagInfo has been deleted
[BC] REMOVED: Class Kreait\Firebase\RemoteConfig\ExplicitValue has been deleted
[BC] REMOVED: Constant Kreait\Firebase\Contract\Messaging::BATCH_MESSAGE_LIMIT was removed
[BC] REMOVED: Method Kreait\Firebase\Factory#createDynamicLinksService() was removed
[BC] REMOVED: Method Kreait\Firebase\Factory#getDebugInfo() was removed
[BC] REMOVED: Method Kreait\Firebase\Factory#withFirestoreDatabase() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#hasTarget() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#target() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#withChangedTarget() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage::withTarget() was removed
```
