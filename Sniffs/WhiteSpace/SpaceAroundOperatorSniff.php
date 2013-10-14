<?php
/**
 * WinkBrace_Sniffs_WhiteSpace_SpaceAroundOperatorSniff
 *
 * Checks that the opening brace of a control structure is on the line after the
 * function declaration.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bas de Ruiter <info@basderuiter.nl>
 */
class WinkBrace_Sniffs_WhiteSpace_SpaceAroundOperatorSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokensToRegister = array(T_BOOLEAN_NOT);
        foreach (array( PHP_CodeSniffer_Tokens::$equalityTokens,
                        PHP_CodeSniffer_Tokens::$comparisonTokens,
                        PHP_CodeSniffer_Tokens::$arithmeticTokens,
                        PHP_CodeSniffer_Tokens::$operators,
                        PHP_CodeSniffer_Tokens::$assignmentTokens,
                        ) as $array)
        {
            foreach ($array as $token)
            {
                if (! in_array($token, $tokensToRegister))
                {
                    $tokensToRegister[] = $token;
                }
            }
        }
        
        return $tokensToRegister;

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
        
        // if the token is a '&', it might be used in a control structure, (e.g. foreach ($arr as &$row) )
        // in which case it isn't an operator and doesn't require spaces around it.
        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND)
        {
            $parenthesisOpener = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$parenthesisOpeners, $stackPtr - 1);
            if (in_array($tokens[$parenthesisOpener], array(T_FUNCTION, T_CLOSURE, T_FOREACH)));
                return;
        }
        
        // assignment operator can have trailing & -> =& (even though it is discouraged to use)
        if ($tokens[$stackPtr]['code'] === T_EQUAL && $tokens[$stackPtr + 1]['code'] === T_BITWISE_AND)
            return;
        
        // all operators MUST have a space before and after them.
        // only the ! operator requires only a trailing space (because a space is not allowed after openening parenthesis
        $data = array($tokens[$stackPtr]['content']);
        if ($tokens[$stackPtr]['code'] !== T_BOOLEAN_NOT && $tokens[$stackPtr - 1]['code'] !== T_WHITESPACE)
        {
            $error = 'The %s operator requires a leading space.';
            $phpcsFile->addError($error, $stackPtr, 'Leading space', $data);
        }
        
        // minus operator can indicate a negative number
        if ($tokens[$stackPtr]['code'] !== T_MINUS && $tokens[$stackPtr + 1]['code'] !== T_WHITESPACE)
        {
            $error = 'The %s operator requires a trailing space.';
            $phpcsFile->addError($error, $stackPtr, 'Trailing space', $data);
        }
         
    }//end process()

}//end class
