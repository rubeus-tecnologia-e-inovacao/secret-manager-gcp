<?php

use stdClass;

namespace Rubeus\SecretManagerGcp;

class Cipher
{
    private static function options($secretKey, $secretIv)
    {

        $options = [
            "encrypt_method" => "AES-256-CBC",
            "secret_key" => $secretKey,
            "secret_iv" => $secretIv,
            "key" => hash('sha256', $secretKey),
            "iv" => substr(hash('sha256', $secretIv), 0, 16)
        ];

        return $options;
    }


    public static function encrypt($secretValue, $secretIv, $string)
    {
        $output = false;

        $options = Cipher::options($secretValue, $secretIv);

        //do the encryption given text/string/number
        $output = openssl_encrypt($string, $options['encrypt_method'], $options['key'], 0, $options['iv']);
        $output = base64_encode($output);

        echo "criptografada: $output\n";
        return $output;
    }

    public static function decrypt($secretValue, $secretIv, $string)
    {

        $output = false;

        $options = Cipher::options($secretValue, $secretIv);

        //do the encryption given text/string/number
        $output = openssl_decrypt(base64_decode($string), $options['encrypt_method'], $options['key'], 0, $options['iv']);

        echo "descriptografada: $output\n";

        return $output;
    }

}
