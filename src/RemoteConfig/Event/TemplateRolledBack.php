<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig\Event;

use Kreait\Firebase\RemoteConfig\Template;
use Kreait\Firebase\RemoteConfig\VersionNumber;

final readonly class TemplateRolledBack
{
    public function __construct(
        public VersionNumber $rollbackTargetVersionNumber,
        public Template $activeTemplate,
    ) {
    }
}
