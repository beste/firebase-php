<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Auth;

use Kreait\Firebase\Auth\UserQuery;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Tests\IntegrationTestCase;

use function random_int;

/**
 * @internal
 *
 * @phpstan-import-type UserQueryShape from UserQuery
 */
final class UserQueryTest extends IntegrationTestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        $this->auth = self::$factory->createAuth();
        $this->ensureAtLeastTwoUsers();
    }

    public function testItReturnsResultsInAscendingOrder(): void
    {
        $query = UserQuery::all()
            ->withLimit(2)
            ->sortedBy(UserQuery::FIELD_CREATED_AT)
            ->inAscendingOrder();

        $users = $this->auth->queryUsers($query);

        $first = array_shift($users);
        $second = array_shift($users);

        $this->assertInstanceOf(UserRecord::class, $first);
        $this->assertInstanceOf(UserRecord::class, $second);

        $this->assertGreaterThan($first->metadata->createdAt, $second->metadata->createdAt);
    }

    public function testItReturnsResultsInDescendingOrder(): void
    {
        $query = UserQuery::all()
            ->withLimit(2)
            ->sortedBy(UserQuery::FIELD_CREATED_AT)
            ->inDescendingOrder();

        $users = $this->auth->queryUsers($query);

        $first = array_shift($users);
        $second = array_shift($users);

        $this->assertInstanceOf(UserRecord::class, $first);
        $this->assertInstanceOf(UserRecord::class, $second);

        $this->assertLessThan($first->metadata->createdAt, $second->metadata->createdAt);
    }

    public function testLimit(): void
    {
        $result = $this->auth->queryUsers(UserQuery::all()->withLimit(1));

        $this->assertCount(1, $result);
    }

    public function testFilterByUid(): void
    {
        $user = $this->createUserWithEmailAndPassword();

        $query = [
            'filter' => [
                'userId' => $user->uid,
            ],
        ];

        $result = $this->auth->queryUsers($query);

        try {
            $this->assertCount(1, $result);
            $this->assertArrayHasKey($user->uid, $result);
            $this->assertSame($user->uid, $result[$user->uid]->uid);
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }

    public function testFilterByEmail(): void
    {
        $user = $this->createUserWithEmailAndPassword();

        $query = [
            'filter' => [
                'email' => $user->email,
            ],
        ];

        $result = $this->auth->queryUsers($query);

        try {
            $this->assertCount(1, $result);
            $this->assertArrayHasKey($user->uid, $result);
            $this->assertSame($user->email, $result[$user->uid]->email);
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }

    public function testFilterByPhoneNumber(): void
    {
        $user = $this->auth->createUser([
            'phoneNumber' => '+49'.random_int(90_000_000_000, 99_999_999_999),
        ]);

        $query = [
            'filter' => [
                'phoneNumber' => $user->phoneNumber,
            ],
        ];

        $result = $this->auth->queryUsers($query);

        try {
            $this->assertCount(1, $result);
            $this->assertArrayHasKey($user->uid, $result);
            $this->assertSame($user->phoneNumber, $result[$user->uid]->phoneNumber);
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }

    private function createUserWithEmailAndPassword(): UserRecord
    {
        return $this->auth->createUser([
            'email' => self::randomEmail(),
            'clear_text_password' => self::randomString(),
        ]);
    }

    private function ensureAtLeastTwoUsers(): void
    {
        $expected = 2;
        $present = $this->auth->queryUsers(UserQuery::all()->withLimit($expected));
        $count = count($present);

        while ($count < $expected) {
            $this->createUserWithEmailAndPassword();
            $count++;
        }
    }
}
