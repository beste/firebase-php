<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Utils\Rector\Rector\InstanceofNegationParenthesesRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(InstanceofNegationParenthesesRector::class, [
        'mode' => InstanceofNegationParenthesesRector::REMOVE_PARENTHESES,
    ]);
};
