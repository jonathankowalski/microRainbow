<?php

namespace microRainbow\Contrib;

/**
 * Description of converter
 *
 * @author Jonathan Kowalski <jonathankowalski@ymail.com>
 */
class Converter {
    public function hex2rgb($hex) {
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
}
?>