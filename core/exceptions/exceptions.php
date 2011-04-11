<?php
namespace microRainbow\Exceptions;


class notSupportedTypeImage extends \Exception{

}

class invalidImage extends \Exception{
    
}

class emptyColors extends \Exception{
    public function __construct(){
        $message = 'Dois-je vous rappeler que le but de ce code est d\'echanger des couleurs ?';
        return parent::__construct($message, 2);
    }
}

?>
