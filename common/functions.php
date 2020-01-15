<?php 

define('ENCRYPTION_KEY', '0702f2c9c1414b70efc1e69f2ff31af0');

function _encrypt($plaintext = ''){
	$method = "AES-256-CBC";
    $iv = '0000000000000000';
    $key = ENCRYPTION_KEY;
    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($ciphertext);
}

function _decrypt($encoded = ''){
	$method = "AES-256-CBC";
    $iv = '0000000000000000';
    $key = ENCRYPTION_KEY;
    $ciphertext  = base64_decode($encoded);
    $plaintext = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
    return $plaintext;
}

function _rand_str($length = 2){
   $random_string = '';
   $permitted_chars = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
   $input_length = strlen($permitted_chars); 
   for($i = 0; $i < $length; $i++) {
      $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
      $random_string .= $random_character;
   }
   return $random_string;
}

function extra_char($string = ''){
   $i = 0;
   $extra_char = '';
   while (isset($string[$i])) {
        $char  = $string[$i];
        if (!is_numeric($char))  break;
        $extra_char .= $char;
        $i++;
   }
   return $extra_char;
}

function _getRand($stringLen = 12, $min_year = 1950) {
    $temp =  $y = date('Y') - $min_year;
    $append = '';
    $div = $temp / 26;
    if (is_int($div)) {
        $temp =  $temp - 26;
    }
    $fixed = $temp >= 26 ? floor($temp / 26) : 0;
    $y = $y % 26 == 0 ?  90 : ($y % 26) + 64;
    $year = $fixed. chr($y);
    $m = date('m') + 64;
    $d = date('d');
    $d = (($d <= 25) ? ($d + 64) : ($d + 23));
    $h = date('H') + 65;
    $i = date('i');
    $s = date('s');
    $timestamp = $year . chr($m) . chr($d) . chr($h). $i . $s;
    $randStrLen = $stringLen - strlen($timestamp);
    return $timestamp . ($randStrLen <= 0 ? '' : _rand_str($randStrLen));
}

function _getRandReverse($string = '', $min_year = 1950) {
    if (is_numeric($string) || strlen($string) < 9) return $string;
    $strlen = strlen($string);
    $extra_char = extra_char($string);
    $extra_year = $extra_char * 26;
    $value = substr($string, strlen($extra_char), $strlen);
    $date = substr($value, 0, 4);
    $time = substr($value, 4, 4);
    $random = substr($value, 8);
    list($y , $m, $d, $h) = str_split($date);
    $y = ord($y) + $min_year - 64 + $extra_year;
    $m = sprintf('%02d', ord($m) - 64);
    $d = sprintf('%02d', is_numeric($d) ? ord($d) - 23 : ord($d) - 64);
    $h = ord($h) - 65;
    $i = substr($time, 0, 2);
    $s = substr($time,-2);
    $datetime = "$y-$m-$d $h:$i:$s";
    return $datetime . ($random ? "-$random" : '' );
}


?>