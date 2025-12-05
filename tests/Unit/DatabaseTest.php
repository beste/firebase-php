<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use GuzzleHttp\Psr7\Uri;
use Kreait\Firebase\Database;
use Kreait\Firebase\Database\ApiClient;
use Kreait\Firebase\Database\RuleSet;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
final class DatabaseTest extends UnitTestCase
{
    private ApiClient&MockObject $apiClient;

    private string $url;

    private Database $database;

    protected function setUp(): void
    {
        $this->url = 'https://database.firebaseio.com';
        $this->apiClient = $this->createMock(ApiClient::class);

        $this->database = new Database(new Uri($this->url), $this->apiClient);
    }

    public function testGetReference(): void
    {
        $this->assertSame('any', $this->database->getReference('any')->getPath());
    }

    public function testGetRootReference(): void
    {
        $this->assertSame('/', $this->database->getReference()->getUri()->getPath());
    }

    public function testGetReferenceFromUrl(): void
    {
        $url = $this->url.'/foo/bar';

        $this->assertSame($url, (string) $this->database->getReferenceFromUrl($url)->getUri());
    }

    public function testGetReferenceFromNonMatchingUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->database->getReferenceFromUrl('https://example.com');
    }

    public function testGetRuleSet(): void
    {
        $this->apiClient
            ->method('get')
            ->with('/.settings/rules')
            ->willReturn($expected = RuleSet::default()->getRules())
        ;

        $ruleSet = $this->database->getRuleSet();

        $this->assertEqualsCanonicalizing($expected, $ruleSet->getRules());
    }
}
