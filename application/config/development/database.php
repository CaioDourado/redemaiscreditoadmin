<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn' => '',
	'hostname' => adm_env('DB_HOST', '127.0.0.1'),
	'username' => adm_env('DB_USER', 'root'),
	'password' => adm_env('DB_PASS', ''),
	'database' => adm_env('DB_NAME', 'redemaiscredito'),
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
