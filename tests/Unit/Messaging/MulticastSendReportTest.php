<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Iterator;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
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

    #[DataProvider('unknownTokenErrors')]
    public function testItReturnsUnknownFids(MessagingException $error): void
    {
        $target = MessageTarget::with(MessageTarget::FID, 'fid-from-another-project');
        $sendReport = SendReport::failure($target, $error);
        $report = MulticastSendReport::withItems([$sendReport]);

        $this->assertSame(['fid-from-another-project'], $report->unknownFids());
    }

    /**
     * @return Iterator<string, array{MessagingException}>
     */
    public static function unknownTokenErrors(): Iterator
    {
        yield 'not found' => [new NotFound('Not found')];
        yield 'sender ID mismatch' => [new SenderIdMismatch('SenderId mismatch')];
    }

    public function testItSeparatesValidTokensFromValidFids(): void
    {
        $report = $this->mixedReport();

        $this->assertSame(['valid-token'], $report->validTokens());
        $this->assertSame(['valid-fid'], $report->validFids());
    }

    public function testItSeparatesUnknownTokensFromUnknownFids(): void
    {
        $report = $this->mixedReport();

        $this->assertSame(['unknown-token'], $report->unknownTokens());
        $this->assertSame(['unknown-fid'], $report->unknownFids());
    }

    public function testItSeparatesInvalidTokensFromInvalidFids(): void
    {
        $report = $this->mixedReport();

        $this->assertSame(['invalid-token'], $report->invalidTokens());
        $this->assertSame(['invalid-fid'], $report->invalidFids());
    }

    public function testItIgnoresOtherTargetTypes(): void
    {
        $report = MulticastSendReport::withItems([
            SendReport::success(MessageTarget::with(MessageTarget::TOPIC, 'valid-topic'), []),
            SendReport::failure(MessageTarget::with(MessageTarget::TOPIC, 'unknown-topic'), new NotFound('Not found')),
            SendReport::failure(MessageTarget::with(MessageTarget::CONDITION, "'invalid' in topics"), new InvalidMessage('Invalid registration token')),
        ]);

        $this->assertSame([], $report->validTokens());
        $this->assertSame([], $report->unknownTokens());
        $this->assertSame([], $report->invalidTokens());
        $this->assertSame([], $report->validFids());
        $this->assertSame([], $report->unknownFids());
        $this->assertSame([], $report->invalidFids());
    }

    private function mixedReport(): MulticastSendReport
    {
        return MulticastSendReport::withItems([
            SendReport::success(MessageTarget::with(MessageTarget::TOKEN, 'valid-token'), []),
            SendReport::success(MessageTarget::with(MessageTarget::FID, 'valid-fid'), []),
            SendReport::failure(MessageTarget::with(MessageTarget::TOKEN, 'unknown-token'), new NotFound('Not found')),
            SendReport::failure(MessageTarget::with(MessageTarget::FID, 'unknown-fid'), new NotFound('Not found')),
            SendReport::failure(MessageTarget::with(MessageTarget::TOKEN, 'invalid-token'), new InvalidMessage('The registration token is not a valid FCM registration token')),
            SendReport::failure(MessageTarget::with(MessageTarget::FID, 'invalid-fid'), new InvalidMessage('Invalid fid')),
        ]);
    }
}
