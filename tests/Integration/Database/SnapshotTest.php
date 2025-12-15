<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Database;

use Kreait\Firebase\Database\Reference;
use Kreait\Firebase\Tests\Integration\DatabaseTestCase;

/**
 * @internal
 */
final class SnapshotTest extends DatabaseTestCase
{
    private Reference $ref;

    protected function setUp(): void
    {
        $this->ref = self::$db->getReference(self::$refPrefix);
    }

    public function testGetReference(): void
    {
        $this->assertSame($this->ref, $this->ref->getSnapshot()->getReference());
    }

    public function testGetKey(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $this->assertSame(__FUNCTION__, $ref->getSnapshot()->getKey());
    }

    public function testGetChildKey(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set(['childKey' => 'value']);

        $this->assertSame('childKey', $ref->getSnapshot()->getChild('childKey')->getKey());
    }

    public function testGetChildValue(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set(['childKey' => 'value']);

        $this->assertSame('value', $ref->getSnapshot()->getChild('childKey')->getValue());
    }

    public function testAnAbsentChildHasNoValue(): void
    {
        $this->assertNull($this->ref->getSnapshot()->getChild('absent')->getValue());
    }

    public function testAChildExists(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set('any');

        $this->assertTrue($ref->getSnapshot()->exists());
    }

    public function testAnAbsentChildDoesNotExist(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        $this->assertFalse($ref->getSnapshot()->exists());
    }

    public function testASnapshotContainingAnArrayHasChildren(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set(['first' => 'value', 'second' => 'value']);

        $this->assertTrue($ref->getSnapshot()->hasChildren());
        $this->assertSame(2, $ref->getSnapshot()->numChildren());
    }

    public function testASnapshotNotContainingAnArrayHasNoChildren(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set('string');

        $this->assertFalse($ref->getSnapshot()->hasChildren());
        $this->assertSame(0, $ref->getSnapshot()->numChildren());
    }

    /**
     * @see https://github.com/kreait/firebase-php/issues/212
     */
    public function testGetChildWithKeyStartingWithANonAlphabeticalCharacter(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set( [
            '123' => 'value',
            '-abc' => 'value',
        ]);

        $this->assertTrue($ref->getSnapshot()->hasChild('123'));
        $this->assertTrue($ref->getSnapshot()->hasChild('-abc'));
    }
}
