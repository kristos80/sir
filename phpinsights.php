<?php
declare(strict_types = 1);

return [
	'preset' => 'default',
	'remove' => [
		\PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff::class,
		\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class,
		\PhpCsFixer\Fixer\Basic\BracesFixer::class,
		\PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff::class,
		\SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff::class,
		\PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer::class,
		\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class,
		\PhpCsFixer\Fixer\Import\OrderedImportsFixer::class,
		\SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff::class,
		\PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff::class,
		\PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::class,
	],
	'config' => [
		\ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class => [
			'maxLength' => 32,
		],
		\PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
			'lineLimit' => 129,
			'absoluteLineLimit' => 160,
			'ignoreComments' => FALSE,
		],
		\NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
			'maxComplexity' => 12,
		],
		\SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class => [
			'linesCountBeforeFirstContent' => 0,
			'linesCountBetweenDescriptionAndAnnotations' => 1,
			'linesCountBetweenDifferentAnnotationsTypes' => 0,
			'linesCountBetweenAnnotationsGroups' => 1,
			'linesCountAfterLastContent' => 0,
			'annotationsGroups' => [],
		],
		\PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer::class => [
			'space' => 'single', // possible values
			                      // ['none', 'single']
		],
	],
];