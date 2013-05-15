<?php namespace Dirlister\lib;

class Renderer {
    /** Template file path
     * @var string
     */
    protected $_tpl;

    /** Create a renderer of a template
     * @param string $template
     */
    function __construct($template){
        if (!file_exists($template))
            throw new \RuntimeException("File '{$template} does not exist'");
        $this->_tpl = $template;
    }

    /** Render the template using a context
     * @param array $context
     * @return string
     */
    function render(array $context){
        extract($context);

        ob_start();
        include $this->_tpl;
        return ob_get_clean();
    }
}
