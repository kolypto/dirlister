<?php namespace Dirlister\lib;

/** Simplistic configuration storage, backed-up by a PHP file
 */
class Config {
    /** Load a config file from the filename
     * @param string $fileName
     */
    function __construct($fileName){
        $vars = require $fileName;
        foreach ($vars as $name => $value)
            $this->{$name} = $value;
        $this->_preprocess();
    }

    /** Preprocess the configuration
     */
    protected function _preprocess(){}
}
