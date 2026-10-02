<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Tests\FirebaseTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;

use function bin2hex;
use function random_bytes;

/**
 * @internal
 */
#[Group('emulator')]
final class AuthEmulatorTest extends FirebaseTestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        parent::setUp();

        if (Util::authEmulatorHost() === null) {
            $this->markTestSkipped('The Auth emulator must be running');
        }

        $projectId = Util::getenv('TEST_FIREBASE_PROJECT_ID');

        if ($projectId === null) {
            $this->markTestSkipped('Emulator tests require a project ID');
        }

        Util::rmenv('GOOGLE_APPLICATION_CREDENTIALS');
        $this->auth = (new Factory())->withProjectId($projectId)->createAuth();
    }

    #[Test]
    #[RunInSeparateProcess]
    public function itWorksWithoutCredentials(): void
    {
        $email = bin2hex(random_bytes(5)).'@example.com';
        $user = $this->auth->createUserWithEmailAndPassword($email, 'password123');

        try {
            $result = $this->auth->signInWithEmailAndPassword($email, 'password123');
            $this->assertSame($user->uid, $result->firebaseUserId());

            $token = $this->auth->verifyIdToken($result->idToken());
            $this->assertSame($user->uid, $token->claims()->get('sub'));
        } finally {
            $this->auth->deleteUser($user->uid);
        }
    }
}
