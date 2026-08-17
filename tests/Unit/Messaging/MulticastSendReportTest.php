<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Iterator;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\SenderIdMismatch;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\SendReport;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MulticastSendReportTest extends TestCase
{
    #[DataProvider('unknownTokenErrors')]
    public function testItReturnsUnknownTokens(MessagingException $error): void
    {
        $target = MessageTarget::with(MessageTarget::TOKEN, 'token-from-another-project');
        $sendReport = SendReport::failure($target, $error);
        $report = MulticastSendReport::withItems([$sendReport]);

        $this->assertSame(['token-from-another-project'], $report->unknownTokens());
    }

    /**
     * @return Iterator<string, array{MessagingException}>
     */
    public static function unknownTokenErrors(): Iterator
    {
        yield 'not found' => [new NotFound('Not found')];
        yield 'sender ID mismatch' => [new SenderIdMismatch('SenderId mismatch')];
    }
}
