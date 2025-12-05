<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Valinor;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Valinor\Source;
use PHPUnit\Framework\TestCase;

final class SourceTest extends TestCase
{
    public function testItSupportsJsonObjectStrings(): void
    {
        $source = Source::parse('{"foo": "bar"}');

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    public function testItSupportsJsonArrayStrings(): void
    {
        $source = Source::parse('[{"foo": "bar"}]');

        $this->assertSame([['foo' => 'bar']], iterator_to_array($source));
    }

    public function testItRejectsInvalidJsonStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON source');

        Source::parse('{');
    }

    public function testItSupportsJsonFiles(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid(base64_encode(__METHOD__), true).'.json';
        file_put_contents($path, '{"foo": "bar"}');

        $source = Source::parse($path);

        try {
            $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
        } finally {
            unlink($path);
        }
    }

    public function testItSupportsJsonFilesWithFileExtensionsNotSuggestingJson(): void
    {
        $source = Source::parse(__DIR__.'/valid.txt');

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    public function testItRejectsInvalidFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/no such file/i');

        Source::parse(sys_get_temp_dir().'/'.uniqid(base64_encode(__METHOD__), true).'.json');
    }

    public function testItSupportsArrays(): void
    {
        $source = Source::parse(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    public function testItSupportsIterables(): void
    {
        $iterable = fn() => yield ['foo' => 'bar'];

        $source = Source::parse($iterable());

        $this->assertSame([['foo' => 'bar']], iterator_to_array($source));
    }
}
