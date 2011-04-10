<?php
include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';
include 'contrib/converter.php';

try{
    
    $image = new microRainbow\Image('imagesFiles/degrade.gif');
    $converter = new microRainbow\Contrib\Converter();
    $rainbow = new microRainbow\MicroRainbow($image);
    $rainbow->set_converter($converter);
    $rainbow->switchHueColors(array('#6290D0' => 'f00'));
    //$rainbow->switchColor(array('#e4eaf2' => 'f00'));
    //$rainbow->turnHueBy(90);
    $rainbow->show();

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
