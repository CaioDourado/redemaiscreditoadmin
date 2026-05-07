<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Consulta_model extends ModelAuth {
    protected $tabela = 'consulta';
    protected $campo_id = 'id_consulta';
    protected $campo_principal = 'nome';

    public function retornar_grupos(){
        return $this->db->get('consulta_grupo');
    }

    public function retornar_grupos_array(){
        $grupos = $this->retornar_grupos()->result();
        $retorno = array();
        foreach($grupos as $index => $grupo):
            $retorno[$grupo->id_consulta_grupo] = $grupo->nome;
        endforeach;
        return $retorno;
    }

    public function retornar_bins(){
        $sql = 'SELECT * FROM `consulta_veicular_efetuada` WHERE `slug` LIKE "%bin%" AND fornecedor LIKE "%informbank%"';
        return $this->db->query($sql);
    }

    public function retornar_consulta_teste($id_consulta){
        $sql = 'SELECT * FROM consulta_teste WHERE id_consulta_teste = '.$id_consulta.' LIMIT 1';
        return $this->db->query($sql);
    }

    public function retornar_todos_com_custo(){
        $sql = 'SELECT ';
        $sql .= 'tbmain.*, ';
        $sql .= '(SELECT custo FROM fornecedor_consulta AS tb2 WHERE tbmain.slug = tb2.slug ORDER BY custo ASC LIMIT 1) AS custo ';
        $sql .= 'FROM `consulta` AS tbmain ';
        return $this->db->query($sql);
    }
}