<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Database;

use Kreait\Firebase\Database\Reference;
use Kreait\Firebase\Database\RuleSet;
use Kreait\Firebase\Exception\Database\UnsupportedQuery;
use Kreait\Firebase\Tests\Integration\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use function current;

/**
 * @internal
 */
#[Group('database-emulator')]
#[Group('emulator')]
final class QueryTest extends DatabaseTestCase
{
    private Reference $ref;

    protected function setUp(): void
    {
        $this->ref = self::$db->getReference(self::$refPrefix);
    }

    public function testLimitToLast(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $this->updateRules(__FUNCTION__, ['.indexOn' => ['key']]);

        $ref->push(['key' => 1]);
        $ref->push(['key' => 3]);
        $ref->push(['key' => 2]);

        $value = $ref->orderByChild('key')->limitToLast(1)->getValue();

        $this->assertSame(['key' => 3], current($value));
    }

    public function testOrderByKey(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $ref->set(['b' => 1, 'a' => 2]);

        $snapshot = $ref->orderByKey()->getSnapshot();

        $this->assertSame(['a' => 2, 'b' => 1], $snapshot->getValue());
    }

    public function testOrderByValue(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $this->updateRules(__FUNCTION__, ['.indexOn' => '.value']);

        $ref->push(2);
        $ref->push(1);

        $snapshot = $ref->orderByValue()->getSnapshot();

        $this->assertSame([1, 2], array_values($snapshot->getValue()));
    }

    public function testOrderByChild(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $this->updateRules(__FUNCTION__, ['.indexOn' => ['child/grandchild']]);

        $ref->getChild('first')->set(['child' => ['grandchild' => 3]]);
        $ref->getChild('second')->set(['child' => ['grandchild' => 4]]);
        $ref->getChild('third')->set(['child' => ['grandchild' => 1]]);
        $ref->getChild('fourth')->set(['child' => ['grandchild' => 2]]);

        $check = $ref->orderByChild('child/grandchild')->getValue();
        $keys = array_keys($check);

        $this->assertSame(['third', 'fourth', 'first', 'second'], $keys);
    }

    public function testOnlyOneSorterIsAllowed(): void
    {
        $this->expectException(UnsupportedQuery::class);
        $this->expectExceptionMessage('already ordered');

        $this->ref->orderByKey()->orderByValue();
    }

    public function testUndefinedIndex(): void
    {
        $this->expectException(UnsupportedQuery::class);
        $this->expectExceptionMessage('Index not defined');

        $this->ref->orderByValue()->getSnapshot();
    }

    /**
     * @param non-empty-string $childPath
     * @param array<non-empty-string, mixed> $rule
     */
    private function updateRules(string $childPath, array $rule): void
    {
        $rules = self::$db->getRuleSet()->getRules();

        $rules['rules'][$this->ref->getPath()] = [
            $childPath => $rule,
        ];

        self::$db->updateRules(RuleSet::fromArray($rules));
    }
}
