<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration;

use InvalidArgumentException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Tests\IntegrationTestCase;
use Kreait\Firebase\Util;
use Kreait\Firebase\Valinor\Mapper;
use Kreait\Firebase\Valinor\Normalizer;
use Kreait\Firebase\Valinor\Source;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ServiceAccountTest extends IntegrationTestCase
{
    private ServiceAccount $serviceAccount;

    private Normalizer $normalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceAccount = (new Mapper())
            ->snakeToCamelCase()
            ->allowSuperfluousKeys()
            ->map(ServiceAccount::class, Source::parse(self::$credentials));

        $this->normalizer = (new Normalizer())->camelToSnakeCase();
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function withPathToServiceAccount(): void
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.__FUNCTION__.'.json';
        file_put_contents($path, $this->normalizer->toJson($this->serviceAccount));

        try {
            $factory = (new Factory())->withServiceAccount($path);
            $this->assertFunctioningConnection($factory);
        } finally {
            unlink($path);
        }
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function withJsonString(): void
    {
        $factory = (new Factory())->withServiceAccount($this->normalizer->toJson($this->serviceAccount));

        $this->assertFunctioningConnection($factory);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function withArray(): void
    {
        $factory = (new Factory())->withServiceAccount($this->normalizer->toArray($this->serviceAccount));

        $this->assertFunctioningConnection($factory);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function withGoogleApplicationCredentialsAsFilePath(): void
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.__FUNCTION__.'.json';
        file_put_contents($path, $this->normalizer->toJson($this->serviceAccount));

        Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', $path);

        try {
            $this->assertFunctioningConnection(new Factory());
        } finally {
            unlink($path);
        }
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function withGoogleApplicationCredentialsAsJsonString(): void
    {
        Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', $this->normalizer->toJson($this->serviceAccount));

        $this->assertFunctioningConnection(new Factory());
    }

    #[Test]
    public function withInvalidServiceAccount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Factory())->withServiceAccount(['invalid' => 'data']);
    }

    private function assertFunctioningConnection(Factory $factory): void
    {
        $auth = $factory->createAuth();
        $user = null;

        try {
            $user = $auth->createAnonymousUser();
        } finally {
            if ($user !== null) {
                $auth->deleteUser($user->uid);
            }
        }
    }
}
