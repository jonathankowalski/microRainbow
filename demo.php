<?php
include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';
include 'contrib/converter.php';

try{
    
    $image = new microRainbow\Image('imagesFiles/redsquare.png');
    $converter = new microRainbow\Contrib\Converter();
    $rainbow = new microRainbow\MicroRainbow($image);
    $rainbow->set_converter($converter);
    $rainbow->switchHueColors(array('#f00' => '0f0'),0.15);
    //$rainbow->switchColor(array('#209ed4' => 'f00'));
    //$rainbow->turnHueBy(90);
    $rainbow->show();

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
