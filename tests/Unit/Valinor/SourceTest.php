<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Valinor;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Valinor\Source;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    #[Test]
    public function itSupportsJsonObjectStrings(): void
    {
        $source = Source::parse('{"foo": "bar"}');

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    #[Test]
    public function itSupportsJsonArrayStrings(): void
    {
        $source = Source::parse('[{"foo": "bar"}]');

        $this->assertSame([['foo' => 'bar']], iterator_to_array($source));
    }

    #[Test]
    public function itRejectsInvalidJsonStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON source');

        Source::parse('{');
    }

    #[Test]
    public function itSupportsJsonFiles(): void
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

    #[Test]
    public function itSupportsJsonFilesWithFileExtensionsNotSuggestingJson(): void
    {
        $source = Source::parse(__DIR__.'/valid.txt');

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    #[Test]
    public function itRejectsInvalidFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/no such file/i');

        Source::parse(sys_get_temp_dir().'/'.uniqid(base64_encode(__METHOD__), true).'.json');
    }

    #[Test]
    public function itSupportsArrays(): void
    {
        $source = Source::parse(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], iterator_to_array($source));
    }

    #[Test]
    public function itSupportsIterables(): void
    {
        $iterable = fn() => yield ['foo' => 'bar'];

        $source = Source::parse($iterable());

        $this->assertSame([['foo' => 'bar']], iterator_to_array($source));
    }
}
