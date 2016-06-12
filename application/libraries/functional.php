<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This class contain of all support fuction for development
 * @author : Parama_Fadli_Kurnia
 * Developer can make some additional code in this class
 */
class Functional {
    /* validate gender by user input */

    function setIDTransaction(){
        
    }
    
    /* get url object */

    function mcurl($url) {
        $result = "";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // save data into specific file
    function save_to($session, $content, $file_type) {
        $myfile = fopen(DATA . $session . "." . $file_type, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
    }

    // get web content via curl process
    function get($url) {
        $result = shell_exec(CURL . ' "' . $url . '"');
        $json = (array) json_decode($result);
        return $json;
    }

    // check if key exist in array
    function validate($key, $array) {
        return (array_key_exists($key, $array) ? $array[$key] : "NULL");
    }

    function get_json_result($command) {
        $result = shell_exec(CURL . " " . $command);
        $json = (array) json_decode($result);
        return $json;
    }

    function make_token() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 24; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $str_pass = implode($pass);

        $numlink = '0123456789';
        $pass_numlink = array(); //remember to declare $pass as an array
        $numlinkLength = strlen($numlink) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $numlinkLength);
            $pass_numlink[] = $numlink[$n];
        }
        $str_numlink = implode($pass_numlink);
        $token = $str_pass . "|" . $str_numlink;
        $data = array();
        $data["tk_apps"] = $str_numlink;
        $data["tk_secret"] = $str_pass;
        $data["tk_token"] = $token;
        return $data; //turn the array into a string
    }

}
