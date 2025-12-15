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

    public function testAValueCanBeDeleted(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set('value');

        self::$db->runTransaction(function (Transaction $transaction) use ($ref): void {
            $transaction->snapshot($ref);
            $transaction->remove($ref);
        });

        $this->assertNull($ref->getValue());
    }

    public function testATransactionPreventsAChangeWhenTheRemoteHasChanged(): void
    {
        $firstRef = $this->ref->getChild(__FUNCTION__);
        $firstRef->set(['key' => 'value']);

        $this->expectException(TransactionFailed::class);

        self::$db->runTransaction(static function (Transaction $transaction) use ($firstRef): void {
            $transaction->snapshot($firstRef);

            // Set the value without a transaction to simulate an external change
            $firstRef->set('new value');

            // The etag has changed, so the transaction will fail
            $transaction->set($firstRef, 'new value');
        });
    }

    public function testATransactionPreventsADeletionWhenTheRemoteHasChanged(): void
    {
        $ref = $this->ref->getChild(__FUNCTION__);
        $ref->set(['key' => 'value']);

        $this->expectException(TransactionFailed::class);

        self::$db->runTransaction(static function (Transaction $transaction) use ($ref): void {
            $transaction->snapshot($ref);

            // Set the value without a transaction to simulate an external change
            $ref->set('new value');

            // This should fail
            $transaction->remove($ref);
        });
    }
}
