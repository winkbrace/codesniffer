<?php
/**
 * WinkBrace_Sniffs_Methods_MethodDocCommentSniff
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

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false)
{
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * WinkBrace_Sniffs_Methods_MethodDocCommentSniff
 *
 * Checks that the constructor method spacing is correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.6
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WinkBrace_Sniffs_Methods_MethodDocCommentSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    
    /**
     * Constructs a Squiz_Sniffs_Scope_MethodScopeSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore closures.
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null)
            return;
        
        // walk back up in the file. a Doc comment is expected directly above the function/method.
        $line = $tokens[$stackPtr]['line'];
        for ($i = $stackPtr - 1; $i > 0; $i--)
        {
            if ($tokens[$i]['code'] === T_WHITESPACE)
            {
                continue;
            }
            
            // if we find a doc comment, we're happy
            if ($tokens[$i]['code'] === T_DOC_COMMENT)
            {
                break;
            }
            
            // if we are 2 lines higher than the function without having encountered a doc comment, we add an error
            if ($tokens[$i]['line'] < $line - 1)
            {
                $error = 'Methods must have PHPDOC comments.';
                $phpcsFile->addError($error, $stackPtr, 'MethodDocComment');
                break;
            }
        }

    }

}
