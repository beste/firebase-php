<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\RemoteConfig;

use Beste\Json;
use Kreait\Firebase\RemoteConfig\Parameter;
use Kreait\Firebase\RemoteConfig\ParameterGroup;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ParameterGroupTest extends TestCase
{
    public function testItCanBeCreated(): void
    {
        $group = ParameterGroup::named($name = 'name')
            ->withDescription($description = 'description')
            ->withParameter($first = Parameter::named('first'))
            ->withParameter($second = Parameter::named('second'))
        ;

        $this->assertSame($name, $group->name());
        $this->assertSame($description, $group->description());

        $this->assertContains($first, $group->parameters());
        $this->assertContains($second, $group->parameters());

        $this->assertSame(
            '{"description":"description","parameters":{"first":{"valueType":"PARAMETER_VALUE_TYPE_UNSPECIFIED"},"second":{"valueType":"PARAMETER_VALUE_TYPE_UNSPECIFIED"}}}',
            Json::encode($group),
        );
    }

    /**
     * @see https://github.com/beste/firebase-php/issues/1164
     */
    public function testAnEmptyGroupIsSerializedWithoutParameters(): void
    {
        $group = ParameterGroup::named('empty')->withDescription('description');

        $this->assertSame('{"description":"description"}', Json::encode($group));
    }
}
