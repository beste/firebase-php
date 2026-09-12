<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging\Event;

use Kreait\Firebase\Messaging\MulticastSendReport;

final readonly class MessagesSent
{
    public function __construct(
        public MulticastSendReport $report,
        public bool $validateOnly,
    ) {
    }
}
