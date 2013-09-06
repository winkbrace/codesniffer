<?php
/**
 * WinkBrace_Sniffs_ControlStructures_FollowingBlankLineSniff
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * WinkBrace_Sniffs_ControlStructures_FollowingBlankLineSniff
 *
 * Checks that control structures have the correct spacing around brackets.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.6
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WinkBrace_Sniffs_ControlStructures_FollowingBlankLineSniff implements PHP_CodeSniffer_Sniff
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
            T_ELSE,
            T_ELSEIF,
            T_FUNCTION,
            T_CATCH,
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
        
        if (isset($tokens[$stackPtr]['scope_closer']) === true)
        {
            $closer = $tokens[$stackPtr]['scope_closer'];
            
            for ($i = $closer + 1; $i < count($tokens); $i++)
            {
                // the first non-whitespace token should be 2 lines below the closing bracket
                // other closing brackets, php end tag, elseif and else are the exception
                if (! in_array($tokens[$i]['code'], array(T_WHITESPACE, T_COMMENT)))
                {
                    if (in_array($tokens[$i]['code'], array(T_CLOSE_CURLY_BRACKET, T_ELSE, T_ELSEIF, T_CLOSE_TAG)))
                    {
                        return;
                    }
                    
                    $gap = $tokens[$i]['line'] - $tokens[$closer]['line'] - 1;
                    if ($gap < 1)
                    {
                        $error = 'Expected at least 1 empty line after closing bracket; %s found';
                        $data  = array($gap);
                        $phpcsFile->addError($error, $closer, 'BlankLineAfterClosingBrace', $data);
                    }
                    
                    break;
                }
            }
        }//end if

    }//end process()

}//end class
