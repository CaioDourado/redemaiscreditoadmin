<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vault_model extends ModelAuth{
    protected $tabela = 'vault';
    protected $campo_id = 'id_vault';
    protected $campo_principal = 'data';

    public function get_vault_now(){
        $now = date('Y-m-d');
        $sql = 'SELECT * FROM vault WHERE data = "'.$now.'"';
        return $this->db->query($sql);
    }

    public function retornar_todos($limit = 10){
        $this->db->order_by('data','DESC');
        $this->db->limit($limit);
        return $this->db->get($this->tabela);
    }
}