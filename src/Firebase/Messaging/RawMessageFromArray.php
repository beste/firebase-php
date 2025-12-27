<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Beste\Json;

final readonly class RawMessageFromArray implements Message
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return Json::decode(Json::encode($this->data), true);
    }
}
