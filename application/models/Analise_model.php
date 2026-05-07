<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Analise_model extends ModelAuth {
    public function retornar_negativacoes(){
        $sql = 'SELECT * FROM negativacao WHERE id_cliente_fk > 4';
        return $this->db->query($sql);
    }
}