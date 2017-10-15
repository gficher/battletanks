<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function redirect_ssl() {
    $CI =& get_instance();
    $class = $CI->router->directory.$CI->router->class;
    $exclude = array();  // add more controller names to exclude ssl.

    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $isSecure = true;
    }

    // Debuggy scientific method of testing stuff
    //echo $class.'|'.ENVIRONMENT.'|'.$_SERVER["HTTP_HOST"].'|'.$_SERVER['SERVER_PORT'].'|'.$isSecure."\n";

    if (!in_array($class, $exclude) and (in_array(ENVIRONMENT, Array('production','testing')))) {
        // force redirecting to ssl.
        $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
        if (!$isSecure) redirect($CI->uri->uri_string());
    } else {
        if ($isSecure) {
            $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
        }
        // force redirecting with no ssl.
        //$CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
        //if ($isSecure) redirect($CI->uri->uri_string());
    }
}
