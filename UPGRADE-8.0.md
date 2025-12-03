# Upgrade from 7.x to 8.0

## Introduction

This is a major release, but its aim is to provide as much backward compatibility as possible to ease upgrades
from 7.x to 8.0.

## Notable changes

* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.

## Complete list of breaking changes

The following list has been generated with [roave/backward-compatibility-check](https://github.com/Roave/BackwardCompatibilityCheck).

```
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
