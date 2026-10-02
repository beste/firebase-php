<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use Iterator;
use Kreait\Firebase\Database\RuleSet;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Tests\FirebaseTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;

use function bin2hex;
use function in_array;
use function random_bytes;

/**
 * @internal
 */
#[Group('database-emulator')]
#[Group('emulator')]
final class DatabaseEmulatorTest extends FirebaseTestCase
{
    #[Test]
    #[DataProvider('googleCredentials')]
    #[RunInSeparateProcess]
    public function itWorksWithoutUsableCredentials(?string $credentials): void
    {
        if (in_array(Util::rtdbEmulatorHost(), ['0', null], true)) {
            $this->markTestSkipped('The Database emulator must be running');
        }

        if ($credentials === null) {
            Util::rmenv('GOOGLE_APPLICATION_CREDENTIALS');
        } else {
            Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', $credentials);
        }
        $database = (new Factory())->withProjectId('demo-project')->createDatabase();
        $originalRules = $database->getRuleSet();
        $reference = $database->getReference('tests'.bin2hex(random_bytes(5)));

        try {
            $database->updateRules(RuleSet::private());
            $this->assertSame(RuleSet::private()->getRules(), $database->getRuleSet()->getRules());

            $reference->set('credentialless');
            $this->assertSame('credentialless', $reference->getValue());

            $reference->remove();
            $this->assertNull($reference->getValue());
        } finally {
            $reference->remove();
            $database->updateRules($originalRules);
        }
    }

    /**
     * @return Iterator<string, array{non-empty-string|null}>
     */
    public static function googleCredentials(): Iterator
    {
        yield 'no credentials' => [null];
        yield 'invalid credentials must not be read' => ['credentials-must-not-be-used'];
    }
}
