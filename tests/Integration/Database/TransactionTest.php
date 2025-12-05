<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Database;

use Kreait\Firebase\Database\Reference;
use Kreait\Firebase\Database\Transaction;
use Kreait\Firebase\Exception\Database\TransactionFailed;
use Kreait\Firebase\Tests\Integration\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[Group('database-emulator')]
#[Group('emulator')]
final class TransactionTest extends DatabaseTestCase
{
    private Reference $ref;

    protected function setUp(): void
    {
        $this->ref = self::$db->getReference(self::$refPrefix);
    }

    public function testAValueCanBeWritten(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);

        self::$db->runTransaction(static function (Transaction $transaction) use ($ref): void {
            $transaction->snapshot($ref);

            $transaction->set($ref, 'new value');
        });

        $this->assertSame('new value', $ref->getValue());
    }

    public function testATransactionPreventsAChangeWhenTheRemoteHasChanged(): void
    {
        $firstRef = $this->ref->getChild(__FUNCTION__);
        $firstRef->set(['key' => 'value']);

        $this->expectException(TransactionFailed::class);

        self::$db->runTransaction(static function (Transaction $transaction) use ($firstRef): void {
            // Register a transaction for the given reference
            $transaction->snapshot($firstRef);

            // Set the value without a transaction
            $firstRef->set('new value');

            // This should fail
            $transaction->set($firstRef, 'new value');
        });
    }

    public function testATransactionKeepsTrackOfMultipleReferences(): void
    {
        $firstRef = $this->ref->getChild(__FUNCTION__.'_first');
        $secondRef = $this->ref->getChild(__FUNCTION__.'_second');

        $this->expectException(TransactionFailed::class);

        self::$db->runTransaction(function (Transaction $transaction) use ($firstRef, $secondRef): void {
            // Register a transaction for the given reference
            $firstSnapshot = $transaction->snapshot($firstRef);
            $secondSnapshot = $transaction->snapshot($secondRef);

            $firstCurrentValue = $firstSnapshot->getValue() ?? 0;
            assert(is_int($firstCurrentValue));
            $newFirstValue = ++$firstCurrentValue;

            $secondCurrentValue = $secondSnapshot->getValue() ?? 0;
            assert(is_int($secondCurrentValue));
            $newSecondValue = ++$secondCurrentValue;

            // Set the value without a transaction
            $firstRef->set($newFirstValue);
            $secondRef->set($newSecondValue);

            // A transactional "set" will now fail
            try {
                $transaction->set($firstRef, $newFirstValue);
                $this->fail('An exception should have been thrown');
            } catch (TransactionFailed) {
                // this is expected
            }

            $transaction->set($secondRef, $newSecondValue);
        });
    }

    public function testAValueCanBeDeleted(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set('value');

        self::$db->runTransaction(function (Transaction $transaction) use ($ref): void {
            $snapshot = $transaction->snapshot($ref);

            $this->assertSame('value', $snapshot->getValue());

            // This should not throw an exception
            $transaction->remove($ref);
        });
    }

    public function testATransactionPreventsADeletionWhenTheRemoteHasChanged(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set(['key' => 'value']);

        $this->expectException(TransactionFailed::class);

        self::$db->runTransaction(static function (Transaction $transaction) use ($ref): void {
            // Register a transaction for the given reference
            $transaction->snapshot($ref);

            // Set the value without a transaction
            $ref->set('new value');

            // This should fail
            $transaction->remove($ref);
        });
    }
}
