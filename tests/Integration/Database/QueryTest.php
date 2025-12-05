<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Database;

use Kreait\Firebase\Database\Reference;
use Kreait\Firebase\Database\RuleSet;
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

        $rules = self::$db->getRuleSet()->getRules();

        $rules['rules'][$this->ref->getPath()]
            = [__FUNCTION__ => ['.indexOn' => ['key']],
            ];

        self::$db->updateRules(RuleSet::fromArray($rules));

        $ref->push(['key' => 1]);
        $ref->push(['key' => 3]);
        $ref->push(['key' => 2]);

        $value = $ref->orderByChild('key')->limitToLast(1)->getValue();

        $this->assertSame(['key' => 3], current($value));
    }

    public function testOrderByChild(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $rules = self::$db->getRuleSet()->getRules();

        $rules['rules'][$this->ref->getPath()] = [
            __FUNCTION__ => [
                '.indexOn' => ['child/grandchild']
            ],
        ];
        self::$db->updateRules(RuleSet::fromArray($rules));

        $ref->getChild('first')->set(['child' => ['grandchild' => 3]]);
        $ref->getChild('second')->set(['child' => ['grandchild' => 4]]);
        $ref->getChild('third')->set(['child' => ['grandchild' => 1]]);
        $ref->getChild('fourth')->set(['child' => ['grandchild' => 2]]);

        $check = $ref->orderByChild('child/grandchild')->getValue();
        $keys = array_keys($check);

        $this->assertSame(['third', 'fourth', 'first', 'second'], $keys);
    }
}
