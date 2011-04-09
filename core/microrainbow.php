<?php

namespace microRainbow;

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
            $oldRef = $this->HexToRGB($old);
            \imagecolorallocate($this->_newimage, $newRef['red'], $newRef['green'], $newRef['blue']);
            $trueIndex = \imagecolorclosest($this->_newimage, $oldRef['red'], $oldRef['green'], $oldRef['blue']);
            $newRef = $this->HexToRGB($new);
            \imagecolorset($this->_newimage, $trueIndex, $newRef['red'], $newRef['green'], $newRef['blue']);
        }        
    }

    public function HexToRGB($hex) {
        $hex = str_replace("#", "", $hex);
        $color = array();

        if(strlen($hex) == 3) {
        $color['red'] = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $color['green'] = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $color['blue'] = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        $color['alpha'] = 0;
        }
        else if(strlen($hex) == 6) {
        $color['red'] = hexdec(substr($hex, 0, 2));
        $color['green'] = hexdec(substr($hex, 2, 2));
        $color['blue'] = hexdec(substr($hex, 4, 2));
        $color['alpha'] = 0;
        }

        return $color;
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
        $color = $this->HexToRGB($colorHex);
        list($h, $s, $l) = $this->rgb2hsl($color['red'], $color['green'], $color['blue']);
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

    public function switchHueColors($colors){
        $this->_newimage = \imagecreatetruecolor($this->_image->get_width(), $this->_image->get_height());
        imagecopy($this->_newimage, $this->_image->get_identifier(), 0, 0, 0, 0, $this->_image->get_width(), $this->_image->get_height());
        for($width = 0; $width < $this->_image->get_width(); $width++){
            for($height = 0; $height < $this->_image->get_height(); $height++){
                $rgb = \imagecolorat($this->_newimage, $width, $height);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                list($h, $s, $l) = $this->rgb2hsl($r, $g, $b);
                foreach($colors as $old => $new){
                    $this->_prepare($old);
                    $this->_prepare($new);
                    $ecartcouleur = $h - $this->_infoColors[$old]['h'];
                    if($ecartcouleur < self::TOLERANCE && $ecartcouleur > -self::TOLERANCE){
                        list($r, $g, $b) = $this->hsl2rgb($this->_infoColors[$new]['h'], $s, $l);
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
                list($h, $s, $l) = $this->rgb2hsl($r, $g, $b);
                $h += $angle / 360;
                if($h > 1) $h--;
                list($r, $g, $b) = $this->hsl2rgb($h, $s, $l);
                imagesetpixel($this->_newimage, $x, $y, imagecolorallocate($this->_newimage, $r, $g, $b));
            }
        }
    }


    public function rgb2hsl($r, $g, $b) {
       $var_R = ($r / 255);
       $var_G = ($g / 255);
       $var_B = ($b / 255);

       $var_Min = min($var_R, $var_G, $var_B);
       $var_Max = max($var_R, $var_G, $var_B);
       $del_Max = $var_Max - $var_Min;

       $v = $var_Max;

       if ($del_Max == 0) {
          $h = 0;
          $s = 0;
       } else {
          $s = $del_Max / $var_Max;

          $del_R = ( ( ( $max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
          $del_G = ( ( ( $max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
          $del_B = ( ( ( $max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

          if      ($var_R == $var_Max) $h = $del_B - $del_G;
          else if ($var_G == $var_Max) $h = ( 1 / 3 ) + $del_R - $del_B;
          else if ($var_B == $var_Max) $h = ( 2 / 3 ) + $del_G - $del_R;

          if ($H < 0) $h++;
          if ($H > 1) $h--;
       }

       return array($h, $s, $v);
    }

    public function hsl2rgb($h, $s, $v) {
        if($s == 0) {
            $r = $g = $B = $v * 255;
        } else {
            $var_H = $h * 6;
            $var_i = floor( $var_H );
            $var_1 = $v * ( 1 - $s );
            $var_2 = $v * ( 1 - $s * ( $var_H - $var_i ) );
            $var_3 = $v * ( 1 - $s * (1 - ( $var_H - $var_i ) ) );

            if       ($var_i == 0) { $var_R = $v     ; $var_G = $var_3  ; $var_B = $var_1 ; }
            else if  ($var_i == 1) { $var_R = $var_2 ; $var_G = $v      ; $var_B = $var_1 ; }
            else if  ($var_i == 2) { $var_R = $var_1 ; $var_G = $v      ; $var_B = $var_3 ; }
            else if  ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2  ; $var_B = $v     ; }
            else if  ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1  ; $var_B = $v     ; }
            else                   { $var_R = $v     ; $var_G = $var_1  ; $var_B = $var_2 ; }

            $r = $var_R * 255;
            $g = $var_G * 255;
            $B = $var_B * 255;
        }
        return array($r, $g, $B);
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
