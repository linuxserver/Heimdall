<?php
/**
 * Tests that PHPCS native annotations in docblocks are tokenized correctly.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Comment;

/**
 * Tests that PHPCS native annotations in docblocks are tokenized correctly.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Comment
 */
final class PhpcsAnnotationsInDocBlockTest extends CommentTestCase
{


    /**
     * Data provider.
     *
     * @see testDocblockOpenerCloser()
     *
     * @return array<string, array<string, string|int|array<int>>>
     */
    public static function dataDocblockOpenerCloser()
    {
        return [
            'Single-line: @phpcs:ignoreFile annotation'            => [
                'marker'       => '/* testSingleLineDocIgnoreFileAnnotation */',
                'closerOffset' => 3,
                'expectedTags' => [],
            ],
            'Single-line: @phpcs:ignore annotation'                => [
                'marker'       => '/* testSingleLineDocIgnoreAnnotation */',
                'closerOffset' => 3,
                'expectedTags' => [],
            ],
            'Single-line: @phpcs:disable annotation'               => [
                'marker'       => '/* testSingleLineDocDisableAnnotation */',
                'closerOffset' => 3,
                'expectedTags' => [],
            ],
            'Single-line: @phpcs:enable annotation; no whitespace' => [
                'marker'       => '/* testSingleLineDocEnableAnnotationNoWhitespace */',
                'closerOffset' => 2,
                'expectedTags' => [],
            ],

            'Multi-line: @phpcs:ignoreFile at the start'           => [
                'marker'       => '/* testMultiLineDocIgnoreFileAnnotationAtStart */',
                'closerOffset' => 13,
                'expectedTags' => [],
            ],
            'Multi-line: @phpcs:ignore at the start'               => [
                'marker'       => '/* testMultiLineDocIgnoreAnnotationAtStart */',
                'closerOffset' => 13,
                'expectedTags' => [10],
            ],
            'Multi-line: @phpcs:disable at the start'              => [
                'marker'       => '/* testMultiLineDocDisableAnnotationAtStart */',
                'closerOffset' => 13,
                'expectedTags' => [],
            ],
            'Multi-line: @phpcs:enable at the start'               => [
                'marker'       => '/* testMultiLineDocEnableAnnotationAtStart */',
                'closerOffset' => 18,
                'expectedTags' => [13],
            ],

            'Multi-line: @phpcs:ignoreFile in the middle'          => [
                'marker'       => '/* testMultiLineDocIgnoreFileAnnotationInMiddle */',
                'closerOffset' => 21,
                'expectedTags' => [],
            ],
            'Multi-line: @phpcs:ignore in the middle'              => [
                'marker'       => '/* testMultiLineDocIgnoreAnnotationInMiddle */',
                'closerOffset' => 23,
                'expectedTags' => [5],
            ],
            'Multi-line: @phpcs:disable in the middle'             => [
                'marker'       => '/* testMultiLineDocDisableAnnotationInMiddle */',
                'closerOffset' => 26,
                'expectedTags' => [21],
            ],
            'Multi-line: @phpcs:enable in the middle'              => [
                'marker'       => '/* testMultiLineDocEnableAnnotationInMiddle */',
                'closerOffset' => 24,
                'expectedTags' => [21],
            ],

            'Multi-line: @phpcs:ignoreFile at the end'             => [
                'marker'       => '/* testMultiLineDocIgnoreFileAnnotationAtEnd */',
                'closerOffset' => 16,
                'expectedTags' => [5],
            ],
            'Multi-line: @phpcs:ignore at the end'                 => [
                'marker'       => '/* testMultiLineDocIgnoreAnnotationAtEnd */',
                'closerOffset' => 16,
                'expectedTags' => [],
            ],
            'Multi-line: @phpcs:disable at the end'                => [
                'marker'       => '/* testMultiLineDocDisableAnnotationAtEnd */',
                'closerOffset' => 18,
                'expectedTags' => [5],
            ],
            'Multi-line: @phpcs:enable at the end'                 => [
                'marker'       => '/* testMultiLineDocEnableAnnotationAtEnd */',
                'closerOffset' => 16,
                'expectedTags' => [],
            ],
        ];

    }//end dataDocblockOpenerCloser()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignoreFile annotation.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testSingleLineDocIgnoreFileAnnotation()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE_FILE      => '@phpcs:ignoreFile '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testSingleLineDocIgnoreFileAnnotation()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignore annotation.
     *
     * @return void
     */
    public function testSingleLineDocIgnoreAnnotation()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE           => '@phpcs:ignore Stnd.Cat.SniffName -- With reason '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testSingleLineDocIgnoreAnnotation()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS disable annotation.
     *
     * @return void
     */
    public function testSingleLineDocDisableAnnotation()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_DISABLE          => '@phpcs:disable Stnd.Cat.SniffName,Stnd.Other '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testSingleLineDocDisableAnnotation()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS enable annotation.
     *
     * @return void
     */
    public function testSingleLineDocEnableAnnotationNoWhitespace()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_PHPCS_ENABLE           => '@phpcs:enable Stnd.Cat.SniffName'],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testSingleLineDocEnableAnnotationNoWhitespace()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignoreFile annotation at the start.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreFileAnnotationAtStart()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE_FILE      => '@phpcs:ignoreFile'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Something.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreFileAnnotationAtStart()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignore annotation at the start.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreAnnotationAtStart()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE           => '@phpcs:ignore Stnd.Cat.SniffName'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tag'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreAnnotationAtStart()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS disable annotation at the start.
     *
     * @return void
     */
    public function testMultiLineDocDisableAnnotationAtStart()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_DISABLE          => '@phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Something.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocDisableAnnotationAtStart()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS enable annotation at the start.
     *
     * @return void
     */
    public function testMultiLineDocEnableAnnotationAtStart()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_ENABLE           => '@phpcs:enable Stnd.Cat,Stnd.Other'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tag'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With description.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocEnableAnnotationAtStart()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignoreFile annotation in the middle.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreFileAnnotationInMiddle()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Check tokenization of PHPCS annotations within docblocks.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE_FILE      => '@phpcs:ignoreFile'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Something.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreFileAnnotationInMiddle()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignore annotation in the middle.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreAnnotationInMiddle()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagBefore'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With Description'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE           => '@phpcs:ignore Stnd.Cat.SniffName'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Something.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreAnnotationInMiddle()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS disable annotation in the middle.
     *
     * @return void
     */
    public function testMultiLineDocDisableAnnotationInMiddle()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Check tokenization of PHPCS annotations within docblocks.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_DISABLE          => '@phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagAfter'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With Description'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocDisableAnnotationInMiddle()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS enable annotation in the middle.
     *
     * @return void
     */
    public function testMultiLineDocEnableAnnotationInMiddle()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Check tokenization of PHPCS annotations within docblocks.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_ENABLE           => '@phpcs:enable Stnd.Cat,Stnd.Other'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagAfter'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocEnableAnnotationInMiddle()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignoreFile annotation at the end.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreFileAnnotationAtEnd()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagBefore'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE_FILE      => '@phpcs:ignoreFile'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreFileAnnotationAtEnd()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS ignore annotation at the end.
     *
     * @return void
     */
    public function testMultiLineDocIgnoreAnnotationAtEnd()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Check tokenization of PHPCS annotations within docblocks.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_IGNORE           => '@phpcs:ignore Stnd.Cat.SniffName'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocIgnoreAnnotationAtEnd()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS disable annotation at the end.
     *
     * @return void
     */
    public function testMultiLineDocDisableAnnotationAtEnd()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagBefore'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With Description.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_DISABLE          => '@phpcs:disable Stnd.Cat.SniffName -- Ensure PHPCS annotations are also retokenized correctly.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocDisableAnnotationAtEnd()


    /**
     * Verify tokenization of a single line DocBlock containing a PHPCS enable annotation at the end.
     *
     * @return void
     */
    public function testMultiLineDocEnableAnnotationAtEnd()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Check tokenization of PHPCS annotations within docblocks.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_PHPCS_ENABLE           => '@phpcs:enable Stnd.Cat,Stnd.Other'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultiLineDocEnableAnnotationAtEnd()


}//end class
