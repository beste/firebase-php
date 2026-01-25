<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Valinor\Mapper;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class ServiceAccountTest extends TestCase
{
    /**
     * @see https://github.com/beste/firebase-php/pull/1034
     */
    #[DoesNotPerformAssertions]
    public function testItCanBeMapped(): void
    {
        $mapper = (new Mapper())->allowSuperfluousKeys()->snakeToCamelCase();

        $input = [
            'type' => 'service_account',
            'project_id' => 'project-id',
            'client_email' => 'client-email',
            'private_key' => 'private-key',
        ];

        $mapper->map(ServiceAccount::class, $input);
    }
}
