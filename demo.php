<?php
include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';

try{
    
    $image = new microRainbow\Image('imagesFiles/degrade.gif');
    $rainbow = new microRainbow\MicroRainbow($image);
    $rainbow->switchHueColors(array('#6290D0' => 'f00'));
    //$rainbow->switchColor(array('#e4eaf2' => 'f00'));
    //$rainbow->turnHueBy(90);
    $rainbow->show();

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
