<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Database\Query\Filter;

use GuzzleHttp\Psr7\Uri;
use Kreait\Firebase\Database\Query\Filter\Shallow;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ShallowTest extends TestCase
{
    public function testModifyUri(): void
    {
        $filter = new Shallow();

        $this->assertStringContainsString('shallow=true', (string) $filter->modifyUri(new Uri('https://example.com')));
    }
}
