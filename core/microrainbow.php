<?php

namespace microRainbow;
use microRainbow\Exceptions;


/**
 * Description of microrainbow
 *
 * @author Jonathan Kowalski <jonathankowalski@ymail.com>
 */
class MicroRainbow {

    private $_Colors;
    
    /**
     *
     * @var microRainbow\Image 
     */
    private $_image;

    private $_imagesColors;

    private $_newimage;

    private $_infoColors;

    /**
     *
     * @var microRainbow\Contrib\Converter
     */
    private $_converter;

    const TOLERANCE = 0.1;

    /**
     * Constructeur
     * @param microRainbow\Image $imagepath chemin vers l'image a modifier
     * @param array $Colors tableau associatif comprenant les couleurs à remplacer
     */
    public function __construct($image, $colors=array()){        
        $this->set_image($image);
        if(!empty($colors)){
            $this->switchColor($colors);
        }
    }

    public function show($filename=null, $extension=null){
        if($filename == null && $extension == null){
            header('Content-Type: image/png');
            imagepng($this->_newimage);
        }
    }

    public function  __destruct() {
        if($this->_newimage)
            \imagedestroy($this->_newimage);
    }

    private function _createNewImage(){
        //on recupere les dimensions de l'image
        $this->_newimage = \imagecreatetruecolor($this->_image->get_width(), $this->_image->get_height());
        $this->ImageTrueColorToPalette2($this->_newimage, false, 255);
        //copie de l'image
        imagecopy($this->_newimage, $this->_image->get_identifier(), 0, 0, 0, 0, $this->_image->get_width(), $this->_image->get_height());
    }

    private function _getAllColors(){
        //on va recuperer toutes les couleurs de notre image
        $colors = array();
        for($height = 0; $height < $this->_image->get_height(); $height++){
            for($width = 0; $width < $this->_image->get_width(); $width++){
                $colors [] = \imagecolorat($this->_image->get_identifier(), $width, $height);
            }
        }
        $this->_imagesColors = \array_unique($colors);
    }

    private function _replaceColors(){
        @imagecolormatch($this->_image->get_identifier(), $this->_newimage);
        foreach ($this->_Colors as $old => $new) {
            $oldRef = $this->_converter->hex2rgb($old);
            \imagecolorallocate($this->_newimage, $newRef['red'], $newRef['green'], $newRef['blue']);
            $trueIndex = \imagecolorclosest($this->_newimage, $oldRef['red'], $oldRef['green'], $oldRef['blue']);
            $newRef = $this->_converter->hex2rgb($new);
            \imagecolorset($this->_newimage, $trueIndex, $newRef['red'], $newRef['green'], $newRef['blue']);
        }        
    }  

    public function ImageTrueColorToPalette2( $image, $dither, $ncolors ){
        $width = imagesx( $image );
        $height = imagesy( $image );
        $colors_handle = ImageCreateTrueColor( $width, $height );
        ImageCopyMerge( $colors_handle, $image, 0, 0, 0, 0, $width, $height, 100 );
        ImageTrueColorToPalette( $image, $dither, $ncolors );
        ImageColorMatch( $colors_handle, $image );
        ImageDestroy( $colors_handle );
    }

    public function switchColor($colors){
        $this->set_Colors($colors);
        $this->_createNewImage();
        //$this->_getAllColors();
        $this->_replaceColors();
    }
    
    private function _prepare($colorHex){
        if(isset($this->_infoColors[$colorHex])) return false;
        $color = $this->_converter->hex2rgb($colorHex);
        list($h, $s, $l) = $this->_converter->rgb2hsl($color['red'], $color['green'], $color['blue']);
        $info = array();
        $info['hex'] = $colorHex;
        $info['r'] = $color['red'];
        $info['g'] = $color['green'];
        $info['b'] = $color['blue'];
        $info['h'] = $h;
        $info['s'] = $s;
        $info['l'] = $l;
        $this->_infoColors[$colorHex] = $info;
        return true;
    }

    public function switchHueColors($colors, $tolerance=self::TOLERANCE){
        if(empty($colors) || !\is_array($colors)){
            throw new Exceptions\emptyColors();
        }
        $this->_newimage = \imagecreatetruecolor($this->_image->get_width(), $this->_image->get_height());
        imagecopy($this->_newimage, $this->_image->get_identifier(), 0, 0, 0, 0, $this->_image->get_width(), $this->_image->get_height());
        for($width = 0; $width < $this->_image->get_width(); $width++){
            for($height = 0; $height < $this->_image->get_height(); $height++){
                $rgb = \imagecolorat($this->_newimage, $width, $height);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                list($h, $s, $l) = $this->_converter->rgb2hsl($r, $g, $b);
                foreach($colors as $old => $new){
                    $this->_prepare($old);
                    $this->_prepare($new);
                    $ecartcouleur = $h - $this->_infoColors[$old]['h'];
                    if($ecartcouleur < $tolerance && $ecartcouleur > -$tolerance){
                        list($r, $g, $b) = $this->_converter->hsl2rgb($this->_infoColors[$new]['h'], $s, $l);
                        imagesetpixel($this->_newimage, $width, $height, imagecolorallocate($this->_newimage, $r, $g, $b));
                    }
                }
            }
        }
    }

    public function turnHueBy($angle) {
        $this->_newimage = \imagecreatetruecolor($this->_image->get_width(), $this->_image->get_height());
        imagecopy($this->_newimage, $this->_image->get_identifier(), 0, 0, 0, 0, $this->_image->get_width(), $this->_image->get_height());
        if($angle % 360 == 0) return;
        $width = imagesx($this->_newimage);
        $height = imagesy($this->_newimage);

        for($x = 0; $x < $width; $x++) {
            for($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($this->_newimage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                list($h, $s, $l) = $this->_converter->rgb2hsl($r, $g, $b);
                $h += $angle / 360;
                if($h > 1) $h--;
                list($r, $g, $b) = $this->_converter->hsl2rgb($h, $s, $l);
                imagesetpixel($this->_newimage, $x, $y, imagecolorallocate($this->_newimage, $r, $g, $b));
            }
        }
    }

    public function get_converter() {
        return $this->_converter;
    }

    public function set_converter($_converter) {
        $this->_converter = $_converter;
    }

    
    public function get_Colors() {
        return $this->_Colors;
    }

    public function set_Colors($_Colors) {
        $this->_Colors = $_Colors;
    }

    
    public function get_image() {
        return $this->_image;
    }

    public function set_image($_image) {
        $this->_image = $_image;
    }


}
?>
