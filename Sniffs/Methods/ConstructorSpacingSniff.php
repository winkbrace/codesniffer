<?php
/**
 * WinkBrace_Sniffs_Methods_ConstructorSpacingSniff
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
 * WinkBrace_Sniffs_Methods_ConstructorSpacingSniff
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
class WinkBrace_Sniffs_Methods_ConstructorSpacingSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
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

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null)
        {
            // Ignore closures.
            return;
        }
        
        // we are only interested in the constructor
        if ($methodName !== '__construct')
        {
            return;
        }
        
        // walk back up in the file. Comments are allowed.
        // We are looking for 2 blank lines above the comments or the function if there are no comments.
        $line = $tokens[$stackPtr]['line'];
        for ($i = $stackPtr - 1; $i > 0; $i--)
        {
            if ($tokens[$i]['code'] === T_WHITESPACE || $tokens[$i]['code'] === T_PUBLIC)
            {
                continue;
            }
            
            if ($tokens[$i]['code'] === T_DOC_COMMENT)
            {
                $line = $tokens[$i]['line'];
            }
            else
            {
                // the first line that is not a comment and not a whitespace should be 3 lines less than $line,
                // because 2 lines must be blank
                if ($tokens[$i]['line'] !== $line - 3)
                {
                    $error = 'A constructor must have 2 leading blank lines.';
                    $phpcsFile->addError($error, $stackPtr, 'ConstructorSpacing');
                }
                
                break;
            }
        }

    }

}
