# WinkBrace codesniffer #

WinkBrace Standard for [PHP CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer/)

WinkBrace codesniffer is built with the PSR2 config as starting point. Most of those rules are changed or deleted. 

## Installation ##
Place the repository in the CodeSniffer/Standards directory. It should create a folder named WinkBrace, so your path to the xml file should be CodeSniffer/Standards/WinkBrace/ruleset.xml


## Code format example ##

    <?php namespace WinkSnif;
    
    use WinkForm\Form;
    
    class SnifForm extends Form
    {
        private $foo,
                $bar,
                $baz;
        
        
        /**
         * render the form
         * @see \WinkForm\Form::render()
         */
        public function render()
        {
            if ($this->inCli())
                return;
            
            // get the max of something
            $max = 0;
            if ($this->foo > $this->bar)
            {
                foreach ($this->baz as $key => $val)
                {
                    $max = $val > $max ? $val : $max;
                }
            }
        }
    
        /**
         * is the form posted?
         * @see \WinkForm\Form::isPosted()
         */
        public function isPosted()
        {
            return $this->foo->isPosted();
        }
        
    }
