<?php

declare(strict_types=1);

namespace Utils\Rector\Tests\Rector\InstanceofNegationParenthesesRector;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RemoveParenthesesTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/FixtureRemove');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/remove_parentheses.php';
    }
}
