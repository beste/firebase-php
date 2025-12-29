<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use DateInterval;
use InvalidArgumentException;
use Kreait\Firebase\Auth\ActionCodeSettings;
use Kreait\Firebase\Auth\CreateActionLink\FailedToCreateActionLink;
use Kreait\Firebase\Auth\CreateSessionCookie\FailedToCreateSessionCookie;
use Kreait\Firebase\Auth\DeleteUsersResult;
use Kreait\Firebase\Auth\SendActionLink\FailedToSendActionLink;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Auth\SignInResult;
use Kreait\Firebase\Auth\UserQuery;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Exception;
use Kreait\Firebase\Exception\Auth\ExpiredOobCode;
use Kreait\Firebase\Exception\Auth\FailedToVerifySessionCookie;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Exception\Auth\InvalidOobCode;
use Kreait\Firebase\Exception\Auth\OperationNotAllowed;
use Kreait\Firebase\Exception\Auth\RevokedIdToken;
use Kreait\Firebase\Exception\Auth\RevokedSessionCookie;
use Kreait\Firebase\Exception\Auth\UserDisabled;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Request\CreateUser;
use Kreait\Firebase\Request\UpdateUser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use SensitiveParameter;
use Traversable;

/**
 * @phpstan-import-type UserQueryShape from UserQuery
 */
interface Auth
{
    /**
     * @param non-empty-string $uid
     *
     * @throws UserNotFound
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function getUser(string $uid): UserRecord;

    /**
     * @param non-empty-list<non-empty-string> $uids
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     *
     * @return array<non-empty-string, UserRecord|null>
     */
    public function getUsers(array $uids): array;

    /**
     * @param UserQuery|UserQueryShape $query
     *
     * @throws Exception\FirebaseException
     * @throws Exception\AuthException
     *
     * @return array<non-empty-string, UserRecord>
     */
    public function queryUsers(UserQuery|array $query): array;

    /**
     * @param positive-int $maxResults
     * @param positive-int $batchSize
     *
     * @throws Exception\FirebaseException
     * @throws Exception\AuthException
     *
     * @return Traversable<UserRecord>
     */
    public function listUsers(int $maxResults = 1000, int $batchSize = 1000): Traversable;

    /**
     * Creates a new user with the provided properties.
     *
     * @param array<non-empty-string, mixed>|CreateUser $properties
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function createUser(array|CreateUser $properties): UserRecord;

    /**
     * Updates the given user with the given properties.
     *
     * @param non-empty-string $uid
     * @param non-empty-array<non-empty-string, mixed>|UpdateUser $properties
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function updateUser(string $uid, array|UpdateUser $properties): UserRecord;

    /**
     * @param non-empty-string $email
     * @param non-empty-string $password
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function createUserWithEmailAndPassword(string $email, #[SensitiveParameter] string $password): UserRecord;

    /**
     * @param non-empty-string $email
     *
     * @throws UserNotFound
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function getUserByEmail(string $email): UserRecord;

    /**
     * @param non-empty-string $phoneNumber
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function getUserByPhoneNumber(string $phoneNumber): UserRecord;

    /**
     * @param non-empty-string $providerId
     * @param non-empty-string $providerUid
     *
     * @throws UserNotFound
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function getUserByProviderUid(string $providerId, string $providerUid): UserRecord;

    /**
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function createAnonymousUser(): UserRecord;

    /**
     * @param non-empty-string $uid
     * @param non-empty-string $newPassword
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function changeUserPassword(string $uid, #[SensitiveParameter] string $newPassword): UserRecord;

    /**
     * @param non-empty-string $uid
     * @param non-empty-string $newEmail
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function changeUserEmail(string $uid, string $newEmail): UserRecord;

    /**
     * @param non-empty-string $uid
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function enableUser(string $uid): UserRecord;

    /**
     * @param non-empty-string $uid
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function disableUser(string $uid): UserRecord;

    /**
     * @param non-empty-string $uid
     *
     * @throws UserNotFound
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function deleteUser(string $uid): void;

    /**
     * @param iterable<non-empty-string> $uids
     * @param bool $forceDeleteEnabledUsers Whether to force deleting accounts that are not in disabled state. If false, only disabled accounts will be deleted, and accounts that are not disabled will be added to the errors.
     *
     * @throws Exception\AuthException
     */
    public function deleteUsers(iterable $uids, bool $forceDeleteEnabledUsers = false): DeleteUsersResult;

    /**
     * @param non-empty-string $type
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToCreateActionLink
     */
    public function getEmailActionLink(string $type, string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): string;

    /**
     * @param non-empty-string $type
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws UserNotFound
     * @throws FailedToSendActionLink
     */
    public function sendEmailActionLink(string $type, string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): void;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToCreateActionLink
     */
    public function getEmailVerificationLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): string;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToSendActionLink
     */
    public function sendEmailVerificationLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): void;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToCreateActionLink
     */
    public function getPasswordResetLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): string;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToSendActionLink
     */
    public function sendPasswordResetLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): void;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToCreateActionLink
     */
    public function getSignInWithEmailLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): string;

    /**
     * @param non-empty-string $email
     * @param ActionCodeSettings|array<non-empty-string, mixed>|null $actionCodeSettings
     * @param non-empty-string|null $locale
     *
     * @throws FailedToSendActionLink
     */
    public function sendSignInWithEmailLink(string $email, ActionCodeSettings|array|null $actionCodeSettings = null, ?string $locale = null): void;

    /**
     * Sets additional developer claims on an existing user identified by the provided UID.
     *
     * @see https://firebase.google.com/docs/auth/admin/custom-claims
     *
     * @param non-empty-string $uid
     * @param array<non-empty-string, mixed>|null $claims
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function setCustomUserClaims(string $uid, ?array $claims): void;

    /**
     * @param array<non-empty-string, mixed> $claims
     * @param int<0, 3600>|DateInterval|non-empty-string $ttl
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     * @throws Exception\InvalidArgumentException
     */
    public function createCustomToken(string $uid, array $claims = [], int|DateInterval|string $ttl = 3600): UnencryptedToken;

    /**
     * @param non-empty-string $tokenString
     */
    public function parseToken(#[SensitiveParameter] string $tokenString): UnencryptedToken;

    /**
     * Creates a new Firebase session cookie with the given lifetime.
     *
     * The session cookie JWT will have the same payload claims as the provided ID token.
     *
     * @param Token|non-empty-string $idToken The Firebase ID token to exchange for a session cookie
     * @param DateInterval|positive-int $ttl
     *
     * @throws InvalidArgumentException if the token or TTL is invalid
     * @throws FailedToCreateSessionCookie
     */
    public function createSessionCookie(#[SensitiveParameter] Token|string $idToken, DateInterval|int $ttl): string;

    /**
     * Verifies a JWT auth token.
     *
     * Returns a token with the token's claims or rejects it if the token could not be verified.
     *
     * If checkRevoked is set to true, verifies if the session corresponding to the ID token was revoked.
     * If the corresponding user's session was invalidated, a RevokedIdToken exception is thrown.
     * If not specified the check is not applied.
     *
     * NOTE: Allowing time inconsistencies might impose a security risk. Do this only when you are not able
     * to fix your environment's time to be consistent with Google's servers.
     *
     * @param Token|non-empty-string $idToken the JWT to verify
     * @param bool $checkIfRevoked whether to check if the ID token is revoked
     * @param positive-int|null $leewayInSeconds number of seconds to allow a token to be expired, in case that there
     *                                           is a clock skew between the signing and the verifying server
     *
     * @throws FailedToVerifyToken if the token could not be verified
     * @throws RevokedIdToken if the token has been revoked
     */
    public function verifyIdToken(#[SensitiveParameter] Token|string $idToken, bool $checkIfRevoked = false, ?int $leewayInSeconds = null): UnencryptedToken;

    /**
     * Verifies a JWT session cookie.
     *
     * Returns a token with the cookie's claims or rejects it if the session cookie could not be verified.
     *
     * If checkRevoked is set to true, verifies if the session corresponding to the ID token was revoked.
     * If the corresponding user's session was invalidated, a RevokedSessionCookie exception is thrown.
     * If not specified the check is not applied.
     *
     * NOTE: Allowing time inconsistencies might impose a security risk. Do this only when you are not able
     * to fix your environment's time to be consistent with Google's servers.
     *
     * @param non-empty-string $sessionCookie
     * @param positive-int|null $leewayInSeconds
     *
     * @throws FailedToVerifySessionCookie
     * @throws RevokedSessionCookie
     */
    public function verifySessionCookie(#[SensitiveParameter] string $sessionCookie, bool $checkIfRevoked = false, ?int $leewayInSeconds = null): UnencryptedToken;

    /**
     * Verifies the given password reset code and returns the associated user's email address.
     *
     * @see https://firebase.google.com/docs/reference/rest/auth#section-verify-password-reset-code
     *
     * @param non-empty-string $oobCode
     *
     * @throws ExpiredOobCode
     * @throws InvalidOobCode
     * @throws OperationNotAllowed
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function verifyPasswordResetCode(#[SensitiveParameter] string $oobCode): string;

    /**
     * Applies the password reset requested via the given OOB code and returns the associated user's email address.
     *
     * @see https://firebase.google.com/docs/reference/rest/auth#section-confirm-reset-password
     *
     * @param non-empty-string $oobCode the email action code sent to the user's email for resetting the password
     * @param non-empty-string $newPassword
     * @param bool $invalidatePreviousSessions Invalidate sessions initialized with the previous credentials
     *
     * @throws ExpiredOobCode
     * @throws InvalidOobCode
     * @throws OperationNotAllowed
     * @throws UserDisabled
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function confirmPasswordReset(#[SensitiveParameter] string $oobCode, #[SensitiveParameter] string $newPassword, bool $invalidatePreviousSessions = true): string;

    /**
     * Revokes all refresh tokens for the specified user identified by the uid provided.
     * In addition to revoking all refresh tokens for a user, all ID tokens issued
     * before revocation will also be revoked on the Auth backend. Any request with an
     * ID token generated before revocation will be rejected with a token expired error.
     *
     * @param string $uid the user whose tokens are to be revoked
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function revokeRefreshTokens(string $uid): void;

    /**
     * @param non-empty-string $uid
     * @param list<non-empty-string>|non-empty-string $provider
     *
     * @throws Exception\AuthException
     * @throws Exception\FirebaseException
     */
    public function unlinkProvider(string $uid, array|string $provider): UserRecord;

    /**
     * @param UserRecord|non-empty-string $user
     * @param array<non-empty-string, mixed>|null $claims
     *
     * @throws FailedToSignIn
     */
    public function signInAsUser(UserRecord|string $user, ?array $claims = null): SignInResult;

    /**
     * @param Token|non-empty-string $token
     *
     * @throws FailedToSignIn
     */
    public function signInWithCustomToken(#[SensitiveParameter] Token|string $token): SignInResult;

    /**
     * @param non-empty-string $refreshToken
     *
     * @throws FailedToSignIn
     */
    public function signInWithRefreshToken(#[SensitiveParameter] string $refreshToken): SignInResult;

    /**
     * @param non-empty-string $email
     * @param non-empty-string $clearTextPassword
     *
     * @throws FailedToSignIn
     */
    public function signInWithEmailAndPassword(string $email, #[SensitiveParameter] string $clearTextPassword): SignInResult;

    /**
     * @param non-empty-string $email
     * @param non-empty-string $oobCode
     *
     * @throws FailedToSignIn
     */
    public function signInWithEmailAndOobCode(string $email, #[SensitiveParameter] string $oobCode): SignInResult;

    /**
     * @throws FailedToSignIn
     */
    public function signInAnonymously(): SignInResult;

    /**
     * @see https://docs.cloud.google.com/identity-platform/docs/reference/rest/v1/accounts/signInWithIdp
     *
     * @param non-empty-string $provider
     * @param Token|non-empty-string $accessToken
     * @param non-empty-string|null $redirectUrl
     * @param non-empty-string|null $oauthTokenSecret
     * @param non-empty-string|null $linkingIdToken
     * @param non-empty-string|null $rawNonce
     *
     * @throws FailedToSignIn
     */
    public function signInWithIdpAccessToken(string $provider, #[SensitiveParameter] Token|string $accessToken, ?string $redirectUrl = null, #[SensitiveParameter] ?string $oauthTokenSecret = null, #[SensitiveParameter] ?string $linkingIdToken = null, #[SensitiveParameter] ?string $rawNonce = null): SignInResult;

    /**
     * @param non-empty-string $provider
     * @param Token|non-empty-string $idToken
     * @param non-empty-string|null $redirectUrl
     * @param non-empty-string|null $linkingIdToken
     * @param non-empty-string|null $rawNonce
     *
     * @throws FailedToSignIn
     */
    public function signInWithIdpIdToken(string $provider, #[SensitiveParameter] Token|string $idToken, ?string $redirectUrl = null, #[SensitiveParameter] ?string $linkingIdToken = null, #[SensitiveParameter] ?string $rawNonce = null): SignInResult;
}
