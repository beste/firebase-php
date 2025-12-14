# Upgrade from 7.x to 8.0

## Introduction

This is a major release, but its aim is to provide as much backward compatibility as possible to ease upgrades
from 7.x to 8.0.

## Notable changes

* The SDK supports only actively supported PHP versions. As a result, support for PHP < 8.3 has been dropped;
  supported versions are 8.3, 8.4, and 8.5.
* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.

### Type simplifications to reduce runtime overhead

Several argument types have been simplified to their most common forms to eliminate runtime type conversion overhead.
For example, methods that previously accepted `Stringable|string` now only accept `string`.

The union types were originally added for convenience, but introduced overhead when processing arguments, requiring
runtime type conversion and validation that could be replaced with static analysis.

**See the complete list of breaking changes below** to identify any adjustments needed. Most changes should (hopefully)
be trivial (e.g., passing `$user->uid` instead of `$user`). Run your test suite to catch any breaking changes.

## Complete list of breaking changes

The following list has been generated with [roave/backward-compatibility-check](https://github.com/Roave/BackwardCompatibilityCheck).

```
[BC] ADDED: Method getUserByProviderUid() was added to interface Kreait\Firebase\Contract\Auth
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from 0 to NULL
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from 0 to NULL
[BC] CHANGED: The number of required arguments for Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() increased from 1 to 2
[BC] CHANGED: The parameter $accessToken of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from string to Lcobucci\JWT\Token|string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $config of Kreait\Firebase\Messaging\CloudMessage#withAndroidConfig() changed from no type to a non-contravariant Kreait\Firebase\Messaging\AndroidConfig|array
[BC] CHANGED: The parameter $config of Kreait\Firebase\Messaging\CloudMessage#withApnsConfig() changed from no type to a non-contravariant Kreait\Firebase\Messaging\ApnsConfig|array
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#createUserWithEmailAndPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#createUserWithEmailAndPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getEmailActionLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getEmailActionLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getEmailVerificationLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getEmailVerificationLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getPasswordResetLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getPasswordResetLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getSignInWithEmailLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getSignInWithEmailLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getUserByEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#getUserByEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendEmailActionLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendEmailActionLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendEmailVerificationLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendEmailVerificationLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendPasswordResetLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendPasswordResetLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendSignInWithEmailLink() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#sendSignInWithEmailLink() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#signInWithEmailAndOobCode() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#signInWithEmailAndOobCode() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withUnverifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withUnverifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withUnverifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withUnverifiedEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withVerifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withVerifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withVerifiedEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $email of Kreait\Firebase\Request\EditUserTrait#withVerifiedEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $newEmail of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newEmail of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#confirmPasswordReset() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#confirmPasswordReset() changed from Stringable|string to string
[BC] CHANGED: The parameter $password of Kreait\Firebase\Contract\Auth#createUserWithEmailAndPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $password of Kreait\Firebase\Contract\Auth#createUserWithEmailAndPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Contract\Auth#getUserByPhoneNumber() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Contract\Auth#getUserByPhoneNumber() changed from Stringable|string to string
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Request\EditUserTrait#withPhoneNumber() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Request\EditUserTrait#withPhoneNumber() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Request\EditUserTrait#withPhoneNumber() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $phoneNumber of Kreait\Firebase\Request\EditUserTrait#withPhoneNumber() changed from no type to string|null
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from Stringable|string to string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from Stringable|string to string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#unlinkProvider() changed from array|Stringable|string to a non-contravariant array|string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Contract\Auth#unlinkProvider() changed from array|Stringable|string to array|string
[BC] CHANGED: The parameter $provider of Kreait\Firebase\Request\UpdateUser#withRemovedProvider() changed from no type to a non-contravariant string
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from no type to string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from no type to string|null
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#createCustomToken() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#createCustomToken() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#deleteUser() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#deleteUser() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#disableUser() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#disableUser() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#enableUser() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#enableUser() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#getUser() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#getUser() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#revokeRefreshTokens() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#revokeRefreshTokens() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#setCustomUserClaims() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#setCustomUserClaims() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#unlinkProvider() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#unlinkProvider() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#updateUser() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Contract\Auth#updateUser() changed from Stringable|string to string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to a non-contravariant string
[BC] CHANGED: The parameter $uid of Kreait\Firebase\Request\EditUserTrait#withUid() changed from no type to string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to string
[BC] CHANGED: The parameter $user of Kreait\Firebase\Contract\Auth#signInAsUser() changed from Kreait\Firebase\Auth\UserRecord|Stringable|string to Kreait\Firebase\Auth\UserRecord|string
[BC] CHANGED: The parameter $user of Kreait\Firebase\Contract\Auth#signInAsUser() changed from Kreait\Firebase\Auth\UserRecord|Stringable|string to a non-contravariant Kreait\Firebase\Auth\UserRecord|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Messaging\ApnsConfig#withDataField() changed from mixed to a non-contravariant string
[BC] CHANGED: The parameter $value of Kreait\Firebase\RemoteConfig\ConditionalValue#withValue() changed from no type to Kreait\Firebase\RemoteConfig\ParameterValue|array|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\RemoteConfig\ConditionalValue#withValue() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\ParameterValue|array|string
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\ConditionalValue#value() changed from no type to string|array
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\TagColor#__toString() changed from no type to string
[BC] REMOVED: Class Kreait\Firebase\Contract\DynamicLinks has been deleted
[BC] REMOVED: Class Kreait\Firebase\Contract\Transitional\FederatedUserFetcher has been deleted
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
[BC] REMOVED: Method Kreait\Firebase\Factory#withHttpDebugLogger() was removed
[BC] REMOVED: Method Kreait\Firebase\Factory#withHttpLogger() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#hasTarget() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#target() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage#withChangedTarget() was removed
[BC] REMOVED: Method Kreait\Firebase\Messaging\CloudMessage::withTarget() was removed
```
