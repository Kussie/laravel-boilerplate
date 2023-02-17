<?php

namespace Utilities;

use PhpCsFixer\Fixer\Comment\CommentToPhpdocFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

use function count;

use const T_COMMENT;
use const T_DOC_COMMENT;

class CommentToPhpdocOnNonStructuralElement implements FixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Fixes inline PHPDoc-like comments and makes the proper PHPDoc annotations.',
            [
                $a = new CodeSample(
                    <<<'DIFF'
                        <?php
                        - /* @var \\App\\User */
                        + /** @var \\App\\User */
                        $user = \\App\\User::create($attributes);
                        DIFF
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $commentsAnalyzer = new CommentsAnalyzer();

        for ($index = 0, $limit = count($tokens); $index < $limit; $index++) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if ($commentsAnalyzer->isHeaderComment($tokens, $index)) {
                continue;
            }

            if ($commentsAnalyzer->isBeforeStructuralElement($tokens, $index)) {
                continue;
            }

            $commentIndices = $commentsAnalyzer->getCommentBlockIndices($tokens, $index);

            if ($this->isCommentCandidate($tokens, $commentIndices)) {
                $this->fixSingleLineComment($tokens, reset($commentIndices));
            }

            $index = max($commentIndices);
        }
    }

    public function getName(): string
    {
        return 'Bp/comment_to_phpdoc_on_non_structural';
    }

    public function getPriority(): int
    {
        return (new CommentToPhpdocFixer())->getPriority() + 1;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isCommentCandidate(Tokens $tokens, array $indices): bool
    {
        return array_reduce(
            $indices,
            function ($carry, $index) use ($tokens) {
                if ($carry) {
                    return true;
                }

                return ! (1 !== Preg::match('~(?:\\/\\*+|\\R(?: \\*)?)\\s*\\@([a-zA-Z0-9_\\-]+)(?=\\s|\\(|$)~', $tokens[$index]->getContent(), $matches));
            },
            false
        );
    }

    private function fixSingleLineComment(Tokens $tokens, $index): void
    {
        $message = $this->getMessage($tokens[$index]->getContent());

        if ('' !== trim(mb_substr($message, 0, 1))) {
            $message = ' ' . $message;
        }

        if ('' !== trim(mb_substr($message, -1))) {
            $message .= ' ';
        }

        $tokens[$index] = new Token([T_DOC_COMMENT, '/**' . $message . '*/']);
    }

    private function getMessage($content): string
    {
        return rtrim(ltrim($content, '/*'), '*/');
    }
}
