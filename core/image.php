<?php
namespace microRainbow;

use microRainbow\Exceptions;
/**
 * Description of image
 *
 * @author Jonathan Kowalski <jonathankowalski@ymail.com>
 */
class Image {
    private $_path;
    private $_ext;
    private $_identifier;
    private $_width;
    private $_height;

    public function __construct($path){
        $this->set_path($path);
        $this->set_ext($this->_findExt());
        $this->set_identifier($this->_createImage());
        if(false == $this->get_identifier()){
            throw new Exceptions\invalidImage('Impossible de creer l\'image');
        }
        $this->_getImageSize();
    }

    private function _findExt(){
        $imageName = \basename($this->get_path());
        $extension = \array_pop(\explode('.', $imageName));
        return strtolower($extension);
    }

    private function _createImage(){
        switch ($this->get_ext()){
            case 'png':
                return \imagecreatefrompng($this->get_path()); break;
            case 'jpg':
            case 'jpeg':
                return \imagecreatefromjpeg($this->get_path()); break;
            case 'gif':
                return \imagecreatefromgif($this->get_path()); break;
            default :
                throw new Exceptions\notSupportedTypeImage('le type '.$this->get_ext().' n\'est pas géré');
        }
    }

    public function  __destruct() {
        \imagedestroy($this->get_identifier());
    }

    private function _getImageSize(){
        list($w, $h) = \getimagesize($this->get_path());
        $this->set_height($h);
        $this->set_width($w);
    }

    public function get_path() {
        return $this->_path;
    }

    public function set_path($_path) {
        $this->_path = $_path;
    }

    public function get_ext() {
        return $this->_ext;
    }

    public function set_ext($_ext) {
        $this->_ext = $_ext;
    }

    public function get_identifier() {
        return $this->_identifier;
    }

    public function set_identifier($_identifier) {
        $this->_identifier = $_identifier;
    }

    public function get_width() {
        return $this->_width;
    }

    public function set_width($_width) {
        $this->_width = $_width;
    }

    public function get_height() {
        return $this->_height;
    }

    public function set_height($_height) {
        $this->_height = $_height;
    }


}
?>
