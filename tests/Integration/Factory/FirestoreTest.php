<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Factory;

use Exception;
use Kreait\Firebase\Tests\IntegrationTestCase;
use Kreait\Firebase\Util;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

/**
 * @internal
 */
#[Group('grpc')]
#[RequiresPhpExtension('grpc')]
final class FirestoreTest extends IntegrationTestCase
{
    /**
     * If we write a document into the instance created without an explicit database name,
     * we should be able to read it from the instance created with an explicit database name.
     */
    public function testItUsesTheDefaultDatabaseByDefault(): void
    {
        $collection = __FUNCTION__;
        $documentName = __FUNCTION__.self::randomString();

        $default = self::$factory->createFirestore()->database()->collection($collection);
        $explicit = self::$factory->createFirestore('(default)')->database()->collection($collection);

        try {
            $default->document($documentName)->create(['field' => 'value']);

            $this->assertTrue($explicit->document($documentName)->snapshot()->exists());
            $this->assertSame(['field' => 'value'], $explicit->document($documentName)->snapshot()->data());
        } finally {
            $default->document($documentName)->delete();
        }
    }

    public function testItCannotConnectToAnUnknownDatabase(): void
    {
        $name = self::randomString();

        $database = self::$factory->createFirestore($name)->database();

        $this->expectException(Exception::class);
        // No need to deserialize the returned JSON
        $this->expectExceptionMessageMatches("/$name/");

        $database->collection('foo')->document(__FUNCTION__)->create();
    }

    public function testItCanConnectToACustomDatabase(): void
    {
        $collection = __FUNCTION__;
        $documentName = __FUNCTION__.self::randomString();

        $database = self::$factory->createFirestore($this->customDBName())->database();

        try {
            $database->collection($collection)->document($documentName)->create();
            $this->assertTrue($database->collection($collection)->document($documentName)->snapshot()->exists());
        } finally {
            $database->collection($collection)->document($documentName)->delete();
        }
    }

    public function testItSupportsAdditionalFirestoreConfig(): void
    {
        $collection = __FUNCTION__;
        $documentName = __FUNCTION__.self::randomString();

        $database = self::$factory
            ->withFirestoreClientConfig(['database' => $this->customDBName()])
            ->createFirestore()->database();

        try {
            $database->collection($collection)->document($documentName)->create();
            $this->assertTrue($database->collection($collection)->document($documentName)->snapshot()->exists());
        } finally {
            $database->collection($collection)->document($documentName)->delete();
        }
    }

    /**
     * @return non-empty-string
     */
    private function customDBName(): string
    {
        $customDBName = Util::getenv('TEST_FIRESTORE_CUSTOM_DB_NAME');

        if ($customDBName === null) {
            $this->markTestSkipped('No custom Firestore DB name set via the environment variable `TEST_FIRESTORE_CUSTOM_DB_NAME`');
        }

        return $customDBName;
    }
}
