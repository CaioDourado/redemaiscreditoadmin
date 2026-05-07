<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('adm_load_env')){
    function adm_load_env($path=null){
        static $loaded = false;
        if($loaded) return;

        $loaded = true;
        if($path===null){
            $path = FCPATH.'.env';
        }

        if(!is_file($path) || !is_readable($path)){
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line){
            $line = trim($line);
            if($line==='' || strpos($line, '#')===0 || strpos($line, '=')===false){
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if($value!=='' && (($value[0]==='"' && substr($value, -1)==='"') || ($value[0]==="'" && substr($value, -1)==="'"))){
                $value = substr($value, 1, -1);
            }

            if($key!=='' && getenv($key)===false){
                putenv($key.'='.$value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

if(!function_exists('adm_env')){
    function adm_env($key, $default=null){
        adm_load_env();
        $value = getenv($key);
        if($value===false || $value===''){
            return $default;
        }

        $lower = strtolower($value);
        if($lower==='true') return true;
        if($lower==='false') return false;
        if($lower==='null') return null;

        return $value;
    }
}

adm_load_env();
