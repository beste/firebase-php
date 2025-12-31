<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Instanceof_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class InstanceofNegationParenthesesRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const ADD_PARENTHESES = 'add_parentheses';

    public const REMOVE_PARENTHESES = 'remove_parentheses';

    private string $mode = self::ADD_PARENTHESES;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add or remove parentheses around negated instanceof checks',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
if (!$foo instanceof Foo) {}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
if (!($foo instanceof Foo)) {}
CODE_SAMPLE,
                ),
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
if (!$foo instanceof Foo) {}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
if (!($foo instanceof Foo)) {}
CODE_SAMPLE,
                    [
                        'mode' => self::ADD_PARENTHESES,
                    ],
                ),
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
if (!($foo instanceof Foo)) {}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
if (!$foo instanceof Foo) {}
CODE_SAMPLE
                    ,
                    [
                        'mode' => self::REMOVE_PARENTHESES,
                    ],
                ),
            ],
        );
    }

    public function configure(array $configuration): void
    {
        if ($configuration !== []) {
            Assert::keyExists($configuration, 'mode');
            Assert::oneOf($configuration['mode'], [self::ADD_PARENTHESES, self::REMOVE_PARENTHESES]);
        }

        $this->mode = $configuration['mode'] ?? $this->mode;
    }

    public function getNodeTypes(): array
    {
        return [BooleanNot::class];
    }

    /**
     * @param BooleanNot $node
     */
    public function refactor(Node $node): ?BooleanNot
    {
        if (!$node->expr instanceof Instanceof_) {
            return null;
        }

        if ($this->mode === null) {
            return null;
        }

        $oldTokens = $this->file->getOldTokens();

        $tokensStartWithOpeningParens = ((string) $oldTokens[$node->getStartTokenPos() + 1]) === '(';
        $tokensEndWithClosingParens = ((string) $oldTokens[$node->getEndTokenPos()]) === ')';

        $alreadyWrapped = $tokensStartWithOpeningParens && $tokensEndWithClosingParens;

        if ($this->mode === self::ADD_PARENTHESES && $alreadyWrapped === true) {
            return null;
        }

        if ($this->mode === self::ADD_PARENTHESES) {
            $node->expr->setAttribute(AttributeKey::WRAPPED_IN_PARENTHESES, true);
            return $node;
        }

        if ($alreadyWrapped === false) {
            return null;
        }

        return new BooleanNot($node->expr);
    }
}
