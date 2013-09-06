<?php
/**
 * Generic_Sniffs_Formatting_DisallowMultipleStatementsSniff.
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
 * Generic_Sniffs_Formatting_DisallowMultipleStatementsSniff.
 *
 * Ensures each statement is on a line by itself.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.6
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WinkBrace_Sniffs_Formatting_DisallowMultipleStatementsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SEMICOLON);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(T_SEMICOLON, ($stackPtr - 1));
        if ($prev === false)
        {
            return;
        }
        
        // Ignore multiple statements in a FOR condition.
        if (! empty($tokens[$stackPtr]['nested_parenthesis']))
        {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $bracket)
            {
                if (isset($tokens[$bracket]['parenthesis_owner']) === false)
                {
                    // Probably a closure sitting inside a function call.
                    continue;
                }

                $owner = $tokens[$bracket]['parenthesis_owner'];
                if ($tokens[$owner]['code'] === T_FOR)
                {
                    return;
                }
            }
        }
        
        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line'])
        {
            // check what scope we are in
            for ($i = $stackPtr - 1; $i > 0; $i--)
            {
                // Ignore multiple statements in a SWITCH statement.
                if (in_array($tokens[$i]['code'], array(T_SWITCH, T_CASE, T_DEFAULT)))
                {
                    return;
                }
                
                // In any other scope opener, multiple statements on 1 line is not ok
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners))
                {
                    $error = 'Each PHP statement must be on a line by itself';
                    $phpcsFile->addError($error, $stackPtr, 'SameLine');
                    return;
                }
            }
        }

    }//end process()

}//end class
