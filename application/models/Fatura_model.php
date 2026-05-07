<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fatura_model extends ModelAuth{
    protected $tabela = 'fatura';
    protected $campo_id = 'id_fatura';
    protected $campo_principal = 'nome';

    public function retornar_todos(){
        $sql  = 'SELECT tbmain.*,tb2.nome_ou_fantasia AS nome FROM fatura AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        return $this->db->query($sql);
    }

    public function retornar_todos_mes(){
        $sql  = 'SELECT tbmain.*,tb2.nome_ou_fantasia AS nome FROM fatura AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.vencimento > "'.date('Y-m').'-01" ';
        return $this->db->query($sql);
    }

    public function retornar_itens($id_fatura=null){
        $this->db->where(array('id_fatura_fk'=>$id_fatura));
        return $this->db->get('fatura_item');
    }

    public function retornar_itens_group($id_fatura=null){
        $sql = 'SELECT nome, COUNT(*) AS qtd, valor , SUM(valor) AS total FROM fatura_item WHERE id_fatura_fk = '.$id_fatura.' GROUP BY nome';
        return $this->db->query($sql);
    }
}