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

    /**
     * Constructeur
     * @param microRainbow\Image $imagepath chemin vers l'image a modifier
     * @param array $Colors tableau associatif comprenant les couleurs à remplacer
     */
    public function __construct($image, $colors){
        $this->set_Colors($colors);
        $this->set_image($image);

        $this->_createNewImage();
        //$this->_getAllColors();
        $this->_replaceColors();
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
        $myref = array();
        /*
        foreach($this->_imagesColors as $colorIndex){
            $reference = (\imagecolorsforindex($this->_newimage, $colorIndex));
            \imagecolorallocate($this->_newimage, $reference['red'], $reference['green'], $reference['blue']);
        }
        */
        imagecolormatch($this->_image->get_identifier(), $this->_newimage);
        foreach ($this->_Colors as $old => $new) {
            $oldRef = $this->HexToRGB($old);
            $myref[$old] = $oldRef;
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

    public function ImageTrueColorToPalette2( $image, $dither, $ncolors )
{
    $width = imagesx( $image );
    $height = imagesy( $image );
    $colors_handle = ImageCreateTrueColor( $width, $height );
    ImageCopyMerge( $colors_handle, $image, 0, 0, 0, 0, $width, $height, 100 );
    ImageTrueColorToPalette( $image, $dither, $ncolors );
    ImageColorMatch( $colors_handle, $image );
    ImageDestroy( $colors_handle );
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
