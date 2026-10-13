<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Factory;

use Iterator;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Exception\RuntimeException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Tests\FirebaseTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * @internal
 */
#[Group('grpc')]
#[Group('emulator')]
#[RequiresPhpExtension('grpc')]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class FirestoreEmulatorTest extends FirebaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Util::putenv('FIRESTORE_EMULATOR_HOST', '127.0.0.1:8080');
        Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', 'credentials-must-not-be-used');
        Util::rmenv('GOOGLE_CLOUD_PROJECT');
    }

    public function testItIgnoresInvalidCredentialsWithAnExplicitProjectId(): void
    {
        $database = (new Factory())->withProjectId('demo-project')->createFirestore()->database();

        $this->assertSame(
            'projects/demo-project/databases/(default)/documents/tests/example',
            $database->document('tests/example')->name(),
        );
    }

    public function testItIgnoresInvalidCredentialsWithAProjectIdFromTheEnvironment(): void
    {
        Util::putenv('GOOGLE_CLOUD_PROJECT', 'demo-project');

        $database = (new Factory())->createFirestore('custom')->database();

        $this->assertSame(
            'projects/demo-project/databases/custom/documents/tests/example',
            $database->document('tests/example')->name(),
        );
    }

    public function testItUsesTheProjectIdFromAnExplicitServiceAccount(): void
    {
        Util::putenv('GOOGLE_CLOUD_PROJECT', 'demo-other-project');

        $database = (new Factory())->withServiceAccount([
            'type' => 'service_account',
            'project_id' => 'demo-project',
            'client_email' => 'emulator@example.com',
            'private_key' => 'unused',
        ])->createFirestore()->database();

        $this->assertSame(
            'projects/demo-project/databases/(default)/documents/tests/example',
            $database->document('tests/example')->name(),
        );
    }

    public function testItStillReadsCredentialsWhenTheEmulatorIsDisabled(): void
    {
        Util::putenv('FIRESTORE_EMULATOR_HOST', '0');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('credentials-must-not-be-used');

        (new Factory())->withProjectId('demo-project')->createFirestore();
    }

    #[DataProvider('superglobalHosts')]
    public function testItDoesNotEnableTheEmulatorFromSuperglobals(bool $useServer): void
    {
        Util::rmenv('FIRESTORE_EMULATOR_HOST');
        if ($useServer) {
            $_SERVER['FIRESTORE_EMULATOR_HOST'] = '127.0.0.1:8080';
        } else {
            $_ENV['FIRESTORE_EMULATOR_HOST'] = '127.0.0.1:8080';
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('credentials-must-not-be-used');

        (new Factory())->withProjectId('demo-project')->createFirestore();
    }

    /**
     * @return Iterator<string, array{bool}>
     */
    public static function superglobalHosts(): Iterator
    {
        yield 'SERVER only' => [true];
        yield 'ENV only' => [false];
    }

    public function testItUsesTheProcessEnvironmentWhenSuperglobalsDisagree(): void
    {
        $_SERVER['FIRESTORE_EMULATOR_HOST'] = '0';
        $_ENV['FIRESTORE_EMULATOR_HOST'] = '0';

        $this->assertSame('127.0.0.1:8080', Util::firestoreEmulatorHost());

        $database = (new Factory())->withProjectId('demo-project')->createFirestore()->database();

        $this->assertSame(
            'projects/demo-project/databases/(default)/documents/tests/example',
            $database->document('tests/example')->name(),
        );
    }

    public function testItStillReadsCredentialsForStorage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('credentials-must-not-be-used');

        (new Factory())->withProjectId('demo-project')->createStorage();
    }
}
