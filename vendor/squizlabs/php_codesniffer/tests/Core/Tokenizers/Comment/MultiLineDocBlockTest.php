<?php
/**
 * Tests that multiline docblocks are tokenized correctly.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Comment;

/**
 * Tests that multiline docblocks are tokenized correctly.
 *
 * @covers PHP_CodeSniffer\Tokenizers\Comment
 */
final class MultiLineDocBlockTest extends CommentTestCase
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
            'Multi line docblock: no contents'              => [
                'marker'       => '/* testEmptyDocblock */',
                'closerOffset' => 3,
                'expectedTags' => [],
            ],
            'Multi line docblock: variety of text and tags' => [
                'marker'       => '/* testMultilineDocblock */',
                'closerOffset' => 95,
                // phpcs:ignore Squiz.Arrays.ArrayDeclaration.SingleLineNotAllowed
                'expectedTags' => [21, 29, 36, 46, 56, 63, 73, 80, 90],
            ],
            'Multi line docblock: no leading stars'         => [
                'marker'       => '/* testMultilineDocblockNoStars */',
                'closerOffset' => 32,
                // phpcs:ignore Squiz.Arrays.ArrayDeclaration.SingleLineNotAllowed
                'expectedTags' => [10, 16, 21, 27],
            ],
            'Multi line docblock: indented'                 => [
                'marker'       => '/* testMultilineDocblockIndented */',
                'closerOffset' => 60,
                // phpcs:ignore Squiz.Arrays.ArrayDeclaration.SingleLineNotAllowed
                'expectedTags' => [21, 28, 38, 45, 55],
            ],
            'Multi line docblock: opener not on own line'   => [
                'marker'       => '/* testMultilineDocblockOpenerNotOnOwnLine */',
                'closerOffset' => 10,
                'expectedTags' => [],
            ],
            'Multi line docblock: closer not on own line'   => [
                'marker'       => '/* testMultilineDocblockCloserNotOnOwnLine */',
                'closerOffset' => 11,
                'expectedTags' => [],
            ],
            'Multi line docblock: stars not aligned'        => [
                'marker'       => '/* testMultilineDocblockStarsNotAligned */',
                'closerOffset' => 26,
                'expectedTags' => [],
            ],
        ];

    }//end dataDocblockOpenerCloser()


    /**
     * Verify tokenization of an empty, multi-line DocBlock.
     *
     * @phpcs:disable Squiz.Arrays.ArrayDeclaration.SpaceBeforeDoubleArrow -- Readability is better with alignment.
     *
     * @return void
     */
    public function testEmptyDocblock()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testEmptyDocblock()


    /**
     * Verify tokenization of a multi-line DocBlock containing all possible tokens.
     *
     * @return void
     */
    public function testMultilineDocblock()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'This is a multi-line docblock.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With blank lines, stars, tags, and tag descriptions.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tagWithoutDescription'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@since'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => '10.3'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@deprecated'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => '11.5'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@requires'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'PHP 7.1 -- PHPUnit tag.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tag-with-dashes-is-suppported'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Description.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@tag_with_underscores'],
            [T_DOC_COMMENT_WHITESPACE => '          '],
            [T_DOC_COMMENT_STRING     => 'Description.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'string    $p1 Description 1.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'int|false $p2 Description 2.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@return'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'void'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblock()


    /**
     * Verify tokenization of a multi-line DocBlock with extra starts for the opener/closer and no stars on the lines between.
     *
     * @return void
     */
    public function testMultilineDocblockNoStars()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/****'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_STRING     => 'This is a multi-line docblock, but the lines are not marked with stars.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_STRING     => 'Then again, the opener and closer have an abundance of stars.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_TAG        => '@since'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => '10.3'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'string    $p1 Description 1.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'int|false $p2 Description 2.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_TAG        => '@return'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'void'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '**/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblockNoStars()


    /**
     * Verify tokenization of a multi-line, indented DocBlock.
     *
     * @return void
     */
    public function testMultilineDocblockIndented()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'This is a multi-line indented docblock.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'With blank lines, stars, tags, and tag descriptions.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@since'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => '10.3'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@deprecated'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => '11.5'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'string    $p1 Description 1.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@param'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'int|false $p2 Description 2.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_TAG        => '@return'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'void'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '     '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblockIndented()


    /**
     * Verify tokenization of a multi-line DocBlock, where the opener is not on its own line.
     *
     * @return void
     */
    public function testMultilineDocblockOpenerNotOnOwnLine()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Start of description'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'description continued.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblockOpenerNotOnOwnLine()


    /**
     * Verify tokenization of a multi-line DocBlock, where the closer is not on its own line.
     *
     * @return void
     */
    public function testMultilineDocblockCloserNotOnOwnLine()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Start of description'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'description continued. '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblockCloserNotOnOwnLine()


    /**
     * Verify tokenization of a multi-line DocBlock with inconsistent indentation.
     *
     * @return void
     */
    public function testMultilineDocblockStarsNotAligned()
    {
        $expectedSequence = [
            [T_DOC_COMMENT_OPEN_TAG   => '/**'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Start of description.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => '   '],
            [T_DOC_COMMENT_STRING     => 'Line below this is missing a star.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '   '],
            [T_DOC_COMMENT_STRING     => 'Text'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Star indented.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_STAR       => '*'],
            [T_DOC_COMMENT_WHITESPACE => ' '],
            [T_DOC_COMMENT_STRING     => 'Closer indented.'],
            [T_DOC_COMMENT_WHITESPACE => "\n"],
            [T_DOC_COMMENT_WHITESPACE => '    '],
            [T_DOC_COMMENT_CLOSE_TAG  => '*/'],
        ];

        $target = $this->getTargetToken('/* '.__FUNCTION__.' */', T_DOC_COMMENT_OPEN_TAG);

        $this->checkTokenSequence($target, $expectedSequence);

    }//end testMultilineDocblockStarsNotAligned()


}//end class
