# Upgrade from 7.x to 8.0

## Introduction

This is a major release, but its aim is to provide as much backward compatibility as possible to ease upgrades from 7.x to 8.0.

## Notable changes

* The SDK supports only actively supported PHP versions.
  As a result, support for PHP < 8.3 has been dropped;
  supported versions are 8.3, 8.4, and 8.5.
* [Firebase Dynamic Links was shut down on August 25th, 2025](https://firebase.google.com/support/dynamic-links-faq)
  and has been removed from the SDK.
* Realtime Database objects considered value objects have been made final and readonly
* Method arguments are now fully type-hinted
* Sensitive data parameters now use `#[SensitiveParameter]` attributes to prevent exposure in stack traces.

### Type simplifications to reduce runtime overhead

Several argument types have been simplified to their most common forms to eliminate runtime type conversion overhead.
For example, methods that previously accepted `Stringable|string` now only accept `string`.

The union types were originally added for convenience,
but introduced overhead when processing arguments,
requiring runtime type conversion and validation that could be replaced with static analysis.

**Migration:**

If you were passing `Stringable` objects, extract/convert to the string value first:

```diff
+use Kreait\Firebase\Auth\CreateUser;
+
 $userRecord = $auth->createUser(
-    CreateUser::new()->withEmail($emailObject)
+    CreateUser::new()->withEmail((string) $emailObject)
 );

-$user = $auth->getUser($userRecord);
+$user = $auth->getUser($userRecord->uid);
```

### Sensitive Parameter Protection

The SDK now leverages PHP 8.2+ `#[SensitiveParameter]` attributes to enhance security
by preventing sensitive data from appearing in stack traces and error logs.
This affects methods that handle:

- Passwords and authentication credentials
- JWT tokens (ID tokens, refresh tokens, custom tokens)
- API keys and service account data
- One-time codes and session cookies

## Dependency Changes

### PSR Log Dependency

`psr/log` is now a development dependency instead of a runtime dependency.
This change reduces the production dependency footprint.
If you were using PSR Log interfaces directly in your application code,
you should add `psr/log` to your project's composer.json.

### Removed Constants

The `Kreait\Firebase\Contract\Messaging::BATCH_MESSAGE_LIMIT` constant has been removed.
If you were using this constant in your code,
you should define the limit (500) in your application or use the Firebase messaging service limits documentation as reference.

### Exception Handling Changes

Exception codes are no longer preserved when wrapping exceptions.
If your code relies on specific exception codes for error handling,
you should update it to use exception types or messages instead.

### Cloud Message Builder Method Renames

The `CloudMessage` builder methods for setting message targets have been renamed
to follow the `with*` naming pattern for consistency with other builder methods in the SDK.

**Migration:**

```diff
-$message = CloudMessage::new()
-    ->toToken('device-token')
-    ->withNotification(['title' => 'Hello']);
+$message = CloudMessage::new()
+    ->withToken('device-token')
+    ->withNotification(['title' => 'Hello']);

-$message->toTopic('news');
+$message->withTopic('news');

-$message->toCondition("'dogs' in topics");
+$message->withCondition("'dogs' in topics");
```

The old methods are still available as deprecated aliases,
so your code will continue to work during the transition period.

### Removed Factory Methods

Several debugging and logging methods have been removed from the `Factory` class.

**Migration:**

```diff
-$factory = $factory->withHttpLogger($logger);
-$factory = $factory->withHttpDebugLogger($logger);
+// Use Guzzle middleware or your HTTP client's logging instead
+// See: https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html

-$debugInfo = $factory->getDebugInfo();
+// No direct replacement - inspect services directly if needed
```

The `withFirestoreDatabase()` method has also been removed as it was never fully implemented.

### Remote Config Value Objects

The `DefaultValue` and `ExplicitValue` classes have been removed and replaced with the unified `ParameterValue` class.

**Migration:**

```diff
 use Kreait\Firebase\RemoteConfig\Parameter;
+use Kreait\Firebase\RemoteConfig\ParameterValue;

-$parameter = Parameter::named('feature_flag', DefaultValue::with('false'));
+$parameter = Parameter::named('feature_flag', 'false');

-$value = $parameter->defaultValue();
-if ($value instanceof DefaultValue) {
-    // Handle default
-}
+$value = $parameter->defaultValue();
+if ($value instanceof ParameterValue) {
+    $stringValue = $value->value();
+}
```

---

**See the complete list of breaking changes below** to identify any adjustments needed.
Most changes should (hopefully) be trivial (e.g., passing `$user->uid` instead of `$user`).
Run your test suite to catch any breaking changes.

## Complete list of breaking changes

The following list has been generated with [roave/backward-compatibility-check](https://github.com/Roave/BackwardCompatibilityCheck).

```
[BC] ADDED: Method getUserByProviderUid() was added to interface Kreait\Firebase\Contract\Auth
[BC] CHANGED: Class Kreait\Firebase\Database\Query became final
[BC] CHANGED: Class Kreait\Firebase\Database\Reference became final
[BC] CHANGED: Class Kreait\Firebase\Database\RuleSet became final
[BC] CHANGED: Class Kreait\Firebase\Database\Snapshot became final
[BC] CHANGED: Class Kreait\Firebase\Database\Transaction became final
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from 0 to NULL
[BC] CHANGED: Default parameter value for parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from 0 to NULL
[BC] CHANGED: Kreait\Firebase\Auth\ActionCodeSettings\ValidatedActionCodeSettings was marked "@internal"
[BC] CHANGED: Kreait\Firebase\Request\EditUserTrait was marked "@internal"
[BC] CHANGED: The number of required arguments for Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() increased from 1 to 2
[BC] CHANGED: The parameter $accessToken of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from string to Lcobucci\JWT\Token|string
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getEmailActionLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getEmailActionLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getEmailVerificationLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getEmailVerificationLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getPasswordResetLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getPasswordResetLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getSignInWithEmailLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#getSignInWithEmailLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendEmailActionLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendEmailActionLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendEmailVerificationLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendEmailVerificationLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendPasswordResetLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendPasswordResetLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendSignInWithEmailLink() changed from no type to Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $actionCodeSettings of Kreait\Firebase\Contract\Auth#sendSignInWithEmailLink() changed from no type to a non-contravariant Kreait\Firebase\Auth\ActionCodeSettings|array|null
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Contract\Auth#signInWithEmailAndPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $clearTextPassword of Kreait\Firebase\Request\EditUserTrait#withClearTextPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\TransactionFailed#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $code of Kreait\Firebase\Exception\Database\UnsupportedQuery#__construct() changed from int to a non-contravariant Throwable|null
[BC] CHANGED: The parameter $condition of Kreait\Firebase\RemoteConfig\ConditionalValue::basedOn() changed from no type to Kreait\Firebase\RemoteConfig\Condition|string
[BC] CHANGED: The parameter $condition of Kreait\Firebase\RemoteConfig\ConditionalValue::basedOn() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\Condition|string
[BC] CHANGED: The parameter $config of Kreait\Firebase\Messaging\CloudMessage#withAndroidConfig() changed from no type to a non-contravariant Kreait\Firebase\Messaging\AndroidConfig|array
[BC] CHANGED: The parameter $config of Kreait\Firebase\Messaging\CloudMessage#withApnsConfig() changed from no type to a non-contravariant Kreait\Firebase\Messaging\ApnsConfig|array
[BC] CHANGED: The parameter $config of Kreait\Firebase\Messaging\CloudMessage#withWebPushConfig() changed from no type to a non-contravariant Kreait\Firebase\Messaging\WebPushConfig|array
[BC] CHANGED: The parameter $defaultValue of Kreait\Firebase\RemoteConfig\Parameter#withDefaultValue() changed from no type to a non-contravariant array|string|bool|null
[BC] CHANGED: The parameter $defaultValue of Kreait\Firebase\RemoteConfig\Parameter#withDefaultValue() changed from no type to array|string|bool|null
[BC] CHANGED: The parameter $defaultValue of Kreait\Firebase\RemoteConfig\Parameter::named() changed from no type to a non-contravariant array|string|bool|null
[BC] CHANGED: The parameter $defaultValue of Kreait\Firebase\RemoteConfig\Parameter::named() changed from no type to array|string|bool|null
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
[BC] CHANGED: The parameter $error of Kreait\Firebase\Messaging\SendReport::failure() changed from Throwable to a non-contravariant Kreait\Firebase\Exception\MessagingException
[BC] CHANGED: The parameter $newEmail of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newEmail of Kreait\Firebase\Contract\Auth#changeUserEmail() changed from Stringable|string to string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#changeUserPassword() changed from Stringable|string to string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#confirmPasswordReset() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $newPassword of Kreait\Firebase\Contract\Auth#confirmPasswordReset() changed from Stringable|string to string
[BC] CHANGED: The parameter $options of Kreait\Firebase\Contract\AppCheck#createToken() changed from no type to Kreait\Firebase\AppCheck\AppCheckTokenOptions|array|null
[BC] CHANGED: The parameter $options of Kreait\Firebase\Contract\AppCheck#createToken() changed from no type to a non-contravariant Kreait\Firebase\AppCheck\AppCheckTokenOptions|array|null
[BC] CHANGED: The parameter $options of Kreait\Firebase\Messaging\CloudMessage#withFcmOptions() changed from no type to a non-contravariant Kreait\Firebase\Messaging\FcmOptions|array
[BC] CHANGED: The parameter $other of Kreait\Firebase\RemoteConfig\UpdateOrigin#equalsTo() changed from no type to a non-contravariant self|string
[BC] CHANGED: The parameter $other of Kreait\Firebase\RemoteConfig\UpdateType#equalsTo() changed from no type to a non-contravariant self|string
[BC] CHANGED: The parameter $other of Kreait\Firebase\RemoteConfig\VersionNumber#equalsTo() changed from no type to a non-contravariant self|string
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
[BC] CHANGED: The parameter $query of Kreait\Firebase\Contract\RemoteConfig#listVersions() changed from no type to Kreait\Firebase\RemoteConfig\FindVersions|array|null
[BC] CHANGED: The parameter $query of Kreait\Firebase\Contract\RemoteConfig#listVersions() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\FindVersions|array|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpAccessToken() changed from no type to string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from no type to a non-contravariant string|null
[BC] CHANGED: The parameter $redirectUrl of Kreait\Firebase\Contract\Auth#signInWithIdpIdToken() changed from no type to string|null
[BC] CHANGED: The parameter $tagColor of Kreait\Firebase\RemoteConfig\Condition#withTagColor() changed from no type to Kreait\Firebase\RemoteConfig\TagColor|string
[BC] CHANGED: The parameter $tagColor of Kreait\Firebase\RemoteConfig\Condition#withTagColor() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\TagColor|string
[BC] CHANGED: The parameter $template of Kreait\Firebase\Contract\RemoteConfig#publish() changed from no type to Kreait\Firebase\RemoteConfig\Template|array
[BC] CHANGED: The parameter $template of Kreait\Firebase\Contract\RemoteConfig#publish() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\Template|array
[BC] CHANGED: The parameter $template of Kreait\Firebase\Contract\RemoteConfig#validate() changed from no type to Kreait\Firebase\RemoteConfig\Template|array
[BC] CHANGED: The parameter $template of Kreait\Firebase\Contract\RemoteConfig#validate() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\Template|array
[BC] CHANGED: The parameter $token of Kreait\Firebase\Messaging\RegistrationTokens#has() changed from no type to a non-contravariant Kreait\Firebase\Messaging\RegistrationToken|string
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
[BC] CHANGED: The parameter $uri of Kreait\Firebase\Contract\Database#getReferenceFromUrl() changed from no type to Psr\Http\Message\UriInterface|string
[BC] CHANGED: The parameter $uri of Kreait\Firebase\Contract\Database#getReferenceFromUrl() changed from no type to a non-contravariant Psr\Http\Message\UriInterface|string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to a non-contravariant string
[BC] CHANGED: The parameter $url of Kreait\Firebase\Request\EditUserTrait#withPhotoUrl() changed from Stringable|string to string
[BC] CHANGED: The parameter $user of Kreait\Firebase\Contract\Auth#signInAsUser() changed from Kreait\Firebase\Auth\UserRecord|Stringable|string to Kreait\Firebase\Auth\UserRecord|string
[BC] CHANGED: The parameter $user of Kreait\Firebase\Contract\Auth#signInAsUser() changed from Kreait\Firebase\Auth\UserRecord|Stringable|string to a non-contravariant Kreait\Firebase\Auth\UserRecord|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#endAt() changed from no type to a non-contravariant float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#endAt() changed from no type to float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#endBefore() changed from no type to a non-contravariant float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#endBefore() changed from no type to float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#equalTo() changed from no type to a non-contravariant float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#equalTo() changed from no type to float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#startAfter() changed from no type to a non-contravariant float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#startAfter() changed from no type to float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#startAt() changed from no type to a non-contravariant float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Query#startAt() changed from no type to float|bool|int|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\Database\Reference#push() changed from no type to mixed
[BC] CHANGED: The parameter $value of Kreait\Firebase\Messaging\ApnsConfig#withDataField() changed from mixed to a non-contravariant string
[BC] CHANGED: The parameter $value of Kreait\Firebase\RemoteConfig\ConditionalValue#withValue() changed from no type to Kreait\Firebase\RemoteConfig\ParameterValue|array|string
[BC] CHANGED: The parameter $value of Kreait\Firebase\RemoteConfig\ConditionalValue#withValue() changed from no type to a non-contravariant Kreait\Firebase\RemoteConfig\ParameterValue|array|string
[BC] CHANGED: The parameter $values of Kreait\Firebase\Messaging\RegistrationTokens::fromValue() changed from no type to a non-contravariant Kreait\Firebase\Messaging\RegistrationToken|Kreait\Firebase\Messaging\RegistrationTokens|array|string
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\ConditionalValue#value() changed from no type to string|array
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\Parameter#defaultValue() changed from Kreait\Firebase\RemoteConfig\DefaultValue|null to Kreait\Firebase\RemoteConfig\ParameterValue|null
[BC] CHANGED: The return type of Kreait\Firebase\RemoteConfig\Parameter#defaultValue() changed from Kreait\Firebase\RemoteConfig\DefaultValue|null to the non-covariant Kreait\Firebase\RemoteConfig\ParameterValue|null
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
[BC] REMOVED: Class Kreait\Firebase\RemoteConfig\DefaultValue has been deleted
[BC] REMOVED: Class Kreait\Firebase\RemoteConfig\ExplicitValue has been deleted
[BC] REMOVED: Class Kreait\Firebase\Request has been deleted
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
[BC] REMOVED: These ancestors of Kreait\Firebase\Request\CreateUser have been removed: ["Kreait\\Firebase\\Request"]
[BC] REMOVED: These ancestors of Kreait\Firebase\Request\UpdateUser have been removed: ["Kreait\\Firebase\\Request"]
```
