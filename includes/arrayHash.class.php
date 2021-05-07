<?php
/**
 *  Archivo: ArrayHash.class.php
 *	Utilidad: codifica y decodifica una cadena usando una palabra clave secreta
 *
 * @author Panizza Facundo E.
*/
abstract class ArrayHash {
    /**
     * @param <type> $vars = array de valores a encriptar
     * @return <type> hash usando md5
     */
    public static function encode($vars = null){
        if(!is_array($vars)){
            $aux = $vars;
            $vars = array($aux);
        }
        $vars[] = 'CLAVE_DE_ENCRIPTAR';
        $data = '';
        foreach ($vars as $value) {
            $data .= $value;
        }
        return md5($data);
    }
    /**
     * @param <type> $hash el hash a controlar
     * @param <type> $vars array de variables a transformar y controlar
     * @return <type> boolean true/false
     */
    public static function check($hash, $vars = null){
        $hash2 = self::encode($vars);
        return ($hash2 == $hash);
    }
}
//-------------------------------- 
?>