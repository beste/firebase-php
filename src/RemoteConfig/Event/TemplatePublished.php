<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig\Event;

use Kreait\Firebase\RemoteConfig\Template;

final readonly class TemplatePublished
{
    /**
     * @param non-empty-string $etag
     */
    public function __construct(
        public Template $publishedTemplate,
        public string $etag,
    ) {
    }
}
