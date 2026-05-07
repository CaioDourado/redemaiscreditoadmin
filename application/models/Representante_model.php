<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Representante_model extends ModelAuth {
    protected $tabela = 'representante';
    protected $campo_id = 'id_representante';
    protected $campo_principal = 'nome_ou_fantasia';

    public function retornar($id = null){
        if($id!=null){
            $this->db->where(array('id_cliente'=>$id,'consultor'=>1));
            return $this->db->get('cliente');
        }else{
            return null;
        }
    }

    public function retornar_todos(){
        $sql = 'SELECT * FROM cliente WHERE consultor = 1 ORDER BY status DESC, criado_em ASC';
        return $this->db->query($sql);
    }

    public function retornar_consultas_representantes(){
        $sql = 'SELECT * FROM consultor_valor';
        return $this->db->query($sql);
    }

    public function retornar_consultas_representantes_array(){
        $retorno = array();
        $dados = $this->retornar_consultas_representantes()->result();
        foreach($dados as $i => $l):
            $retorno[$l->id_consulta_fk] = $l;
        endforeach;
        return $retorno;
    }

    public function adiconar_valor_consulta($dados){
        $this->db->insert('consultor_valor', $dados);
    }

    public function remover_consultor_consultas(){
        $sql = 'DELETE FROM consultor_valor ';
        $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function retornar_clientes($id_representante=null){
        $this->db->where(array('id_consultor_fk'=>$id_representante,'status'=>1));
        return $this->db->get('cliente');
    }

    public function retornar_boletos_fat($id_rep, $inicio, $fim){
        $sql  = 'SELECT tbmain.*,tb3.id_fatura AS fatura_id, tb3.valor AS fatura_valor FROM boleto AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'LEFT JOIN fatura AS tb3 ON tbmain.hash = tb3.hash_boleto ';
        $sql .= 'WHERE tb2.id_consultor_fk = '.$id_rep.' AND (tbmain.data_pagamento BETWEEN "'.$inicio.'" AND "'.$fim.'") AND tbmain.pago = 1';
        return $this->db->query($sql);
    }

    public function retornar_fatura_itens($id_fats){
        $ids = '';
        foreach($id_fats as $i => $id): if($i>0){$ids .= ','; } $ids .= $id; endforeach;
        $sql  = 'SELECT tbmain.*,tb2.franquia AS custo, UPPER(tb3.nome_ou_fantasia) AS cliente FROM fatura_item AS tbmain ';
        $sql .= 'LEFT JOIN consulta AS tb2 ON tbmain.grupo = tb2.slug ';
        $sql .= 'LEFT JOIN cliente AS tb3 ON tbmain.id_cliente_fk = tb3.id_cliente ';
        $sql .= 'WHERE id_fatura_fk IN ('.$ids.') ORDER BY id_fatura_fk ';
        return $this->db->query($sql);
    }
}