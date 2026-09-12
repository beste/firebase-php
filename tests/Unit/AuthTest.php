<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Beste\Clock\FrozenClock;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Auth\ApiClient;
use Kreait\Firebase\Auth\Event\CustomUserClaimsChanged;
use Kreait\Firebase\Auth\Event\EmailActionLinkSent;
use Kreait\Firebase\Auth\Event\RefreshTokensRevoked;
use Kreait\Firebase\Auth\Event\UserCreated;
use Kreait\Firebase\Auth\Event\UserDeleted;
use Kreait\Firebase\Auth\Event\UsersDeleted;
use Kreait\Firebase\Auth\Event\UserUpdated;
use Kreait\Firebase\Auth\SignIn\GuzzleHandler;
use Kreait\Firebase\Exception\AuthApiExceptionConverter;
use Kreait\Firebase\Http\ErrorResponseParser;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\SessionCookieVerifier;
use Kreait\Firebase\Tests\UnitTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class AuthTest extends UnitTestCase
{
    public function testItDispatchesEventsAfterAuthChanges(): void
    {
        $dispatcher = new class implements EventDispatcherInterface {
            /** @var list<object> */
            public array $events = [];

            public function dispatch(object $event): object
            {
                $this->events[] = $event;

                return $event;
            }
        };

        $client = new Client(['handler' => new MockHandler([
            new Response(200, [], '{"localId":"created"}'),
            new Response(200, [], '{"users":[{"localId":"created","createdAt":"0"}]}'),
            new Response(200, [], '{"localId":"updated"}'),
            new Response(200, [], '{"users":[{"localId":"updated","createdAt":"0"}]}'),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], '{"localId":"unlinked"}'),
            new Response(200, [], '{"users":[{"localId":"unlinked","createdAt":"0"}]}'),
            new Response(200, [], '{}'),
        ])]);
        $clock = FrozenClock::fromUTC();

        $auth = new Auth(
            new ApiClient(
                'project-id',
                null,
                $client,
                new GuzzleHandler('project-id', $client),
                $clock,
                new AuthApiExceptionConverter(new ErrorResponseParser()),
            ),
            null,
            IdTokenVerifier::createWithProjectId('project-id'),
            SessionCookieVerifier::createWithProjectId('project-id'),
            $clock,
            $dispatcher,
        );

        $created = $auth->createUser([]);
        $updated = $auth->updateUser('updated', []);
        $auth->deleteUser('deleted');
        $deleted = $auth->deleteUsers(['first', 'second']);
        $auth->setCustomUserClaims('claimed', ['admin' => true]);
        $auth->revokeRefreshTokens('revoked');
        $unlinked = $auth->unlinkProvider('unlinked', 'google.com');
        $auth->sendPasswordResetLink('user@example.com', null, 'de');

        $this->assertEquals([
            new UserCreated($created),
            new UserUpdated($updated),
            new UserDeleted('deleted'),
            new UsersDeleted(['first', 'second'], $deleted),
            new CustomUserClaimsChanged('claimed', ['admin' => true]),
            new RefreshTokensRevoked('revoked'),
            new UserUpdated($unlinked),
            new EmailActionLinkSent('PASSWORD_RESET', 'user@example.com', 'de'),
        ], $dispatcher->events);
    }
}
