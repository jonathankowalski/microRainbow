<?php
include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';

try{
    /**/
    $image = new microRainbow\Image('imagesFiles/redsquare.png');
    $rainbow = new microRainbow\MicroRainbow($image, array('f00' => '9AE4E8'));
    /**
    $image = new microRainbow\Image('http://rivieraconsulting.fr//images/moteur_recherche_haut_index.png');
    $rainbow = new microRainbow\MicroRainbow($image, array('333' => '9AE4E8','414141' => '9AE4E8','596263' => '9AE4E8'));
    /**/

    $rainbow->show();
} catch(Exception $e) {
    echo $e->getMessage();
}

?>
