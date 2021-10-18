<?php

use stdClass;

namespace Rubeus\SecretManagerGcp;

class Hash
{
    private static function options($memoryCost, $timeCost, $threads)
    {

        $options = [
            'memory_cost' => $memoryCost,
            'time_cost' => $timeCost,
            'threads' => $threads
        ];

        return $options;
    }


    public static function create($string, $memoryCost = 1<<15, $timeCost = 4, $threads = 2)
    {

        $options = Hash::options($memoryCost, $timeCost, $threads);
       
        $secretValue = password_hash($string, PASSWORD_ARGON2I, $options);

        return $secretValue;

    }

    public static function verify($string, $secretKey)
    {
        
        $output = false;

        $output = password_verify($string, $secretKey) ? true : false;
       
        return $output;
    }
    
    public static function toString($secretObject)
    {
        $output = "$".$secretObject->algorithm;
        $output .= "$".$secretObject->version;
        $output .= "$".$secretObject->memory_cost;
        $output .= ",".$secretObject->time_cost;
        $output .= ",".$secretObject->parallelism;
        $output .= "$".$secretObject->salt;
        $output .= "$".$secretObject->secret;
        return $output;
    }

    public static function toObject($secretValue)
    {
        $secretDetails = explode("$",$secretValue);
        $costDetails = explode(",",$secretDetails[3]);

        $output = new stdClass;
        $output->algorithm = $secretDetails[1];
        $output->version = $secretDetails[2];
        $output->memory_cost = $costDetails[0];
        $output->time_cost = $costDetails[1];
        $output->parallelism = $costDetails[2];
        $output->salt = $secretDetails[4];
        $output->secret = $secretDetails[5];

        return $output;
    }

}
