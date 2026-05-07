<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Negativacao_model extends ModelAuth{
    protected $tabela = 'negativacao';
    protected $campo_id = 'id_negativacao';
    protected $campo_principal = 'cpf_cnpj';

    public function retornar_pefin(){
        $sql  = 'SELECT tbmain.*,tb2.nome_ou_fantasia AS cliente ';
        $sql .= 'FROM negativacao AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE slug = "negativacaopefinpf" OR slug = "negativacaopefinpj" ';
        $sql .= 'ORDER BY cliente ASC, tbmain.criado_em ASC';
        return $this->db->query($sql);
    }

    public function retornar_pefin_ativo(){
        $sql  = 'SELECT tbmain.*,tb2.nome_ou_fantasia AS cliente ';
        $sql .= 'FROM negativacao AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'LEFT JOIN negativacao_baixa AS tb3 ON tbmain.cpf_cnpj = tb3.cpf_cnpj ';
        $sql .= 'WHERE (tbmain.slug = "negativacaopefinpf" OR tbmain.slug = "negativacaopefinpj") AND tb2.status = 1 AND tb2.id_cliente > 4 AND tb3.id_negativacao_baixa IS NULL ';
        $sql .= 'ORDER BY cliente ASC, tbmain.criado_em ASC';
        return $this->db->query($sql);
    }

    public function retornar_baixa($baixa=null){
        $sql  = 'SELECT tbmain.*, tb2.usuario, tb2.senha FROM fornecedor_consulta AS tbmain ';
        $sql .= 'LEFT JOIN fornecedor AS tb2 ON tbmain.id_fornecedor_fk = tb2.id_fornecedor ';
        $sql .= 'WHERE tbmain.slug = "'.$baixa.'" LIMIT 1 ';

        return $this->db->query($sql);
    }

    public function retornar_negativacoes(){
        $sql = 'SELECT id_negativacao,cpf_cnpj, requisicao, retorno FROM `negativacao` WHERE `criado_em` > "2021-06-02 16:34:33" AND retorno NOT LIKE "3%%" ';
        return $this->db->query($sql);
    }
}