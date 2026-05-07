<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends ModelAuth{
    protected $tabela = 'usuario';
    protected $campo_id = 'id_usuario';
    protected $campo_principal = 'nome';
}