<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AesCrypt extends Model
{
    use HasFactory;
    /**
     * Permite cifrar una cadena a partir de un llave proporcionada
     * @param strToEncrypt
     * @param key
     * @return String con la cadena encriptada
     */
    public static function encrypt($plaintext, $key256)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $cipherText = openssl_encrypt($plaintext, 'AES-256-CBC', hex2bin($key256), 1, $iv);
        return base64_encode($iv . $cipherText);
    }
    /**
     * Permite descifrar una cadena a partir de un llave proporcionada
     * @param strToDecrypt
     * @param key
     * @return String con la cadena descifrada
     */
    public static function decrypt($encodedInitialData, $key256)
    {
        $encodedInitialData =  base64_decode($encodedInitialData);
        $iv = substr($encodedInitialData, 0, 16);
        $encodedInitialData = substr($encodedInitialData, 16);
        $decrypted = openssl_decrypt($encodedInitialData, 'AES-256-CBC', hex2bin($key256), 1, $iv);
        
        return $decrypted;
    }
}
