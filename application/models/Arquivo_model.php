<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Arquivo_model extends ModelAuth{
    protected $tabela = 'arquivo';
    protected $campo_id = 'id_arquivo';
    protected $campo_principal = 'nome';
}