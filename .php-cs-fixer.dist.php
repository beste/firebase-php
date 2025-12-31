<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS3x0:risky' => true,
        '@PHPUnit9x1Migration:risky' => true,
        'class_attributes_separation' => true,
        'class_definition' => [
            'single_line' => true,
        ],
        'concat_space' => [
            'spacing' => 'none',
        ],
        'final_internal_class' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true,
            'leading_backslash_in_global_namespace' => true,
        ],
        'method_argument_space' => [
            'attribute_placement' => 'standalone', // same_line can break PHP <8.2
        ],
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_unneeded_braces' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'php_unit_method_casing' => [
            'case' => 'camel_case',
        ],
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'this',
            'methods' => [],
        ],
        'single_line_empty_body' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder);
