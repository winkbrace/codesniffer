<?php
/**
 * WinkBrace_Sniffs_ControlStructures_OpeningBraceOnNewLineSniff
 *
 * Checks that the opening brace of a control structure is on the line after the
 * function declaration.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bas de Ruiter <info@basderuiter.nl>
 */
class WinkBrace_Sniffs_ControlStructures_OpeningBraceOnNewLineSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_IF,
                T_WHILE,
                T_FOREACH,
                T_FOR,
                T_SWITCH,
                T_DO,
                T_ELSE,
                T_ELSEIF,
                T_TRY,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // if control statement has no brackets then there's nothing to check
        if (empty($tokens[$stackPtr]['scope_opener']))
            return;

        // opening bracket must be 1 line below the closing parenthesis (in case of multi line if condition for example)
        $structureClose     = ! empty($tokens[$stackPtr]['parenthesis_closer']) ? $tokens[$stackPtr]['parenthesis_closer'] : $stackPtr;
        $structureCloseLine = $tokens[$structureClose]['line'];

        // Find first opening curly bracket. Should be exactly below the start of the control statement.
        $nextBrace = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr + 1);
        if ($tokens[$nextBrace]['column'] !== $tokens[$stackPtr]['column'] || $tokens[$nextBrace]['line'] !== $structureCloseLine + 1)
        {
            $error = 'Expected opening curly bracket exactly underneath start of %s control statement';
            $data  = array($tokens[$stackPtr]['content']);  // for example: "if"
            $position = ! empty($tokens[$nextBrace]['bracket_opener']) ? $tokens[$nextBrace]['bracket_opener'] : $tokens[$nextBrace]['line'];
            $phpcsFile->addError($error, $position, 'SpacingAfterOpenBrace', $data);
        }

    }//end process()

}//end class
