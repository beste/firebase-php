<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

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
    #[Test]
    #[RunInSeparateProcess]
    public function itWorksWithoutCredentials(): void
    {
        if (Util::authEmulatorHost() === null) {
            $this->markTestSkipped('The Auth emulator must be running');
        }

        Util::rmenv('GOOGLE_APPLICATION_CREDENTIALS');
        $auth = (new Factory())->withProjectId('demo-project')->createAuth();
        $email = bin2hex(random_bytes(5)).'@example.com';
        $user = $auth->createUserWithEmailAndPassword($email, 'password123');

        try {
            $result = $auth->signInWithEmailAndPassword($email, 'password123');
            $this->assertSame($user->uid, $result->firebaseUserId());

            $token = $auth->verifyIdToken($result->idToken());
            $this->assertSame($user->uid, $token->claims()->get('sub'));
        } finally {
            $auth->deleteUser($user->uid);
        }
    }
}
