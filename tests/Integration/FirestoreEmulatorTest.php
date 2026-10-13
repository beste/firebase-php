<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Tests\FirebaseTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * @internal
 */
#[Group('emulator')]
#[RequiresPhpExtension('grpc')]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class FirestoreEmulatorTest extends FirebaseTestCase
{
    public function testItWorksWithoutUsableCredentials(): void
    {
        if (Util::firestoreEmulatorHost() === null) {
            $this->markTestSkipped('The Firestore emulator must be running');
        }

        Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', 'credentials-must-not-be-used');

        $document = (new Factory())
            ->withProjectId('demo-firebase-php')
            ->createFirestore()
            ->database()
            ->collection('tests')
            ->newDocument();

        try {
            $document->set(['field' => 'value']);

            $snapshot = $document->snapshot();
            $this->assertTrue($snapshot->exists());
            $this->assertSame(['field' => 'value'], $snapshot->data());
        } finally {
            $document->delete();
        }

        $this->assertFalse($document->snapshot()->exists());
    }
}
