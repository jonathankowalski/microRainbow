<?php
include 'microrainbow.php';

try{
    
    $rainbow = new microrainbow('imagesFiles/redsquare.png');
    $rainbow->switchHueColors(array('#f00' => '0f0'),0.15);
    $rainbow->show();

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
