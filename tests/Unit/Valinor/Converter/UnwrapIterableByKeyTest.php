<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Valinor\Converter;

use CuyZ\Valinor\Mapper\Source\JsonSource;
use Kreait\Firebase\Valinor\Converter\UnwrapIterableByKey;
use Kreait\Firebase\Valinor\Mapper;
use PHPUnit\Framework\TestCase;

final class UnwrapIterableByKeyTest extends TestCase
{
    public function testItExtractsTheConfiguredKeyWhenPresent(): void
    {
        $mapper = (new Mapper())
            ->withConverter(new UnwrapIterableByKey('items'))
            ->allowSuperfluousKeys();

        $result = $mapper->map(
            'list<'.Item::class.'>',
            new JsonSource('{"items":[{"name":"name"}]}'),
        );

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Item::class, $result[0]);
        $this->assertSame('name', $result[0]->name);
    }

    public function testItFallsBackToTheOriginalValueWhenTheConfiguredKeyIsMissing(): void
    {
        $mapper = (new Mapper())
            ->withConverter(new UnwrapIterableByKey('items'));

        $result = $mapper->map(Item::class, ['name' => 'name']);

        $this->assertSame('name', $result->name);
    }
}
