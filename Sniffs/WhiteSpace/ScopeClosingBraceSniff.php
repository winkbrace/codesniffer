<?php
/**
 * Squiz_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.6
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WinkBrace_Sniffs_WhiteSpace_ScopeClosingBraceSniff implements PHP_CodeSniffer_Sniff
{
    
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$scopeOpeners;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if (isset($tokens[$stackPtr]['scope_closer']) === false)
            return;
        
        // closures are allowed to be closed in one line
        if ($tokens[$tokens[$stackPtr]['scope_condition']]['code'] === T_CLOSURE)
            return;

        
        $openBrace   = $tokens[$stackPtr]['scope_opener'];
        $closeBrace  = $tokens[$stackPtr]['scope_closer'];
        
        // if scope closer is not a brace, it's the ender for php html templating like "endforeach"
        if ($tokens[$closeBrace]['code'] !== T_CLOSE_CURLY_BRACKET)
            return;
        
        // Allow opening brace immediately next to closing brace to indicate empty body
        if ($tokens[$openBrace]['line'] === $tokens[$closeBrace]['line']
            && $tokens[$openBrace]['column'] === $tokens[$closeBrace]['column'] - 1
            )
        {
            return;
        }
        
        
        var_dump($tokens[$closeBrace]);

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = ($stackPtr - 1);
        for ($lineStart; $lineStart > 0; $lineStart--)
        {
            if (strpos($tokens[$lineStart]['content'], $phpcsFile->eolChar) !== false)
            {
                break;
            }
        }

        // We found a new line, now go forward and find the
        // first non-whitespace token.
        $lineStart = $phpcsFile->findNext(array(T_WHITESPACE), ($lineStart + 1), null, true);

        $startColumn = $tokens[$lineStart]['column'];
        $scopeStart  = $tokens[$stackPtr]['scope_opener'];
        $scopeEnd    = $tokens[$stackPtr]['scope_closer'];
        $scopeCondition = $tokens[$stackPtr]['scope_condition'];
        
        // Check that the closing brace is on it's own line.
        $lastContent = $phpcsFile->findPrevious(array(T_WHITESPACE), ($scopeEnd - 1), $scopeStart, true);
        if ($tokens[$lastContent]['line'] === $tokens[$scopeEnd]['line'])
        {
            if (! in_array($tokens[$scopeCondition]['code'], array(T_CASE, T_DEFAULT)))  // switch statements contents are allowed on one line
            {
                $error = 'Closing brace must be on a line by itself';
                $phpcsFile->addError($error, $scopeEnd, 'ContentBefore');
                return;
            }
        }

        // Check now that the closing brace is lined up correctly.
        $braceIndent = $tokens[$scopeEnd]['column'];
        if ($braceIndent !== $startColumn)
        {
            if (! in_array($tokens[$stackPtr]['code'], array(T_CASE, T_DEFAULT)))  // switch statements contents are allowed on one line
            {
                $error = 'Closing brace indented incorrectly; expected %s spaces, found %s';
                $data  = array(
                          ($startColumn - 1),
                          ($braceIndent - 1),
                         );
                $phpcsFile->addError($error, $scopeEnd, 'Indent', $data);
            }
        }

    }//end process()

}//end class
