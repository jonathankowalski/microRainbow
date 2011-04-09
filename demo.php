<?php
include 'core/microrainbow.php';
include 'core/image.php';
include 'core/exceptions/exceptions.php';

$image = new microRainbow\Image('imagesFiles/redsquare.png');
$rainbow = new microRainbow\MicroRainbow($image, array('ff0000' => '0000ff'));
$rainbow->show();

?>
