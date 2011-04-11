<?php

include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';
include 'contrib/converter.php';

/**
 * Description of microrainbow
 *
 * @author Jonathan Kowalski <jonathankowalski@ymail.com>
 */
class microrainbow {

    private $_rainbow;
    private $_reflector;

    public function  __construct($path) {
        $image = new microRainbow\Image($path);        
        $converter = new microRainbow\Contrib\Converter();
        $this->_rainbow = new microRainbow\MicroRainbow($image);
        $this->_rainbow->set_converter($converter);
        $this->_reflector = new ReflectionClass($this->_rainbow);
    }

    public function  __call($name, $arguments) {
        if(method_exists($this->_rainbow, $name)){
            return $this->_reflector->getMethod($name)->invokeArgs($this->_rainbow, $arguments);
        } else {
            throw new Exception('Methode '.$name.' inexsitante');
        }
    }
}
?>
