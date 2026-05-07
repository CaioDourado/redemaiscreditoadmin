<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pesquisa_model extends ModelAuth {
    protected $tabela = 'cliente';
    protected $campo_id = 'id_cliente';
    protected $campo_principal = 'nome_ou_fantasia';

    public function by_name_cpf_cnpj($entrada = null){
        if($entrada!=null){
            $sql  = 'SELECT * FROM cliente ';
            $sql .= 'WHERE nome_ou_fantasia LIKE "%'.$entrada.'%" OR razao_social LIKE "%'.$entrada.'%" OR cpf_cnpj LIKE "%'.$entrada.'%"';
            $sql .= 'ORDER BY nome_ou_fantasia';
            return $this->db->query($sql);
        }else{
            return null;
        }
    }
}