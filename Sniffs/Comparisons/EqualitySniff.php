<?php
/**
 * WinkBrace_Sniffs_Comparisons_EqualitySniff.
 *
 * Checks that control structures have the correct spacing around brackets.
 *
 * @package   winkbrace/codesniffer
 * @author    Bas de Ruiter <winkbrace@gmail.com>
 */
class WinkBrace_Sniffs_Comparisons_EqualitySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_IS_EQUAL, T_IS_NOT_EQUAL);
    }
    
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
        
        // When the augend or addend of a comparison are number or boolean
        // then a (not) identical operator is required instead of a (not) equal operator
        $augend = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        $addend = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        
        foreach (array($tokens[$augend], $tokens[$addend]) as $token)
        {
            if (in_array($token['code'], array(T_LNUMBER, T_DNUMBER, T_TRUE, T_FALSE)))
            {
                $error = 'Comparison with number or boolean must use identical operator. %s found.';
                $data = array($tokens[$stackPtr]['content']);
                $phpcsFile->addError($error, $stackPtr, 'EqualityInsteadOfIdenticality', $data);
            }
            
            if (in_array($token['code'], array(T_NULL)))
            {
                $error = 'Comparison with null must only be done using is_null().';
                $phpcsFile->addError($error, $stackPtr, 'ComparingWithNull');
            }
        }
    }
    
}
