<?php
abstract class SanitizeVars {
    public static function UTF8($string){
        return strtr($string,
        "???????��������������������������������������������������������������",
        "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
    }
    public static function STRING($string, $min='', $max=''){
        $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
        $len = strlen($string);
        if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
        return FALSE;

        return $string;
    }
    public static function SYSTEM($string, $min='', $max=''){
        $pattern = '/(;|\||`|>|<|&|^|"|'."\n|\r|'".'|{|}|[|]|\)|\()/i'; // no piping, passing possible environment variables ($),
        $string = preg_replace($pattern, '', $string);
        $string = preg_replace('/\$/', '\\\$', $string); //make sure this is only interpretted as ONE argument
        $len = strlen($string);
        if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
        return FALSE;

        return $string;
    }
    public static function DATE($string){
        $date = explode('-',$string);
        if(sizeof($date) != 3){
            // d/m/Y
            $date = explode('/',$string);
            if(sizeof($date) != 3) return false;
            $d = @mktime(null,null,null,$date[1],$date[0],$date[2]);
            if($d === false) return false;
            return date('Y-m-d',$d);
        }
        // Y-m-d
        $d = @mktime(null,null,null,$date[1],$date[2],$date[0]);
        if($d === false) return false;
        //return $string;
        return date('Y-m-d',$d);
    }
    public static function DATE_HISPANA($string){
        $date = explode('-',$string);
        // d-m-Y
        $d = @mktime(null,null,null,$date[1],$date[0],$date[2]);
        if($d === false){
            return false;
        }
        else{
            return date('Y-m-d',$d);
        }
    }
    public static function SQL($string, $min='', $max=''){
        $pattern[0] = '/(\\\\)/';
        $pattern[1] = "/\"/";
        $pattern[2] = "/'/";
        $replacement[0] = '\\\\\\';
        $replacement[1] = '\"';
        $replacement[2] = "\\'";
        $len = strlen($string);
        if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
            return FALSE;

        return preg_replace($pattern, $replacement, $string);
    }
    public static function LDAP($string, $min='', $max=''){
        $pattern = '/(\)|\(|\||&)/';
        $len = strlen($string);
        if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
            return FALSE;

        return preg_replace($pattern, '', $string);
    }
    public static function HTML($string){
        $pattern[0] = '/\&/';		  $pattern[1] = '/</';
        $pattern[2] = "/>/";		  $pattern[3] = '/\n/';
        $pattern[4] = '/"/';		  $pattern[5] = "/'/";
        $pattern[6] = "/%/";		  $pattern[7] = '/\(/';
        $pattern[8] = '/\)/';		  $pattern[9] = '/\+/';
        $pattern[10] = '/-/';		  $replacement[0] = '&amp;';
        $replacement[1] = '&lt;';	  $replacement[2] = '&gt;';
        $replacement[3] = '<br>';	  $replacement[4] = '&quot;';
        $replacement[5] = '&#39;';  $replacement[6] = '&#37;';
        $replacement[7] = '&#40;';  $replacement[8] = '&#41;';
        $replacement[9] = '&#43;';  $replacement[10] = '&#45;';

        return preg_replace($pattern, $replacement, $string);
    }
    public static function INT($integer, $min='', $max=''){
        $int = intval($integer);
        if((($min != '') && ($int < $min)) || (($max != '') && ($int > $max)))
        return false;

        return $int;
    }
    public static function FLOAT($float, $min='', $max=''){
        $float = floatval($float);
        if((($min != '') && ($float < $min)) || (($max != '') && ($float > $max)))
        return false;

        return $float;
    }
}//-- end CLASS
