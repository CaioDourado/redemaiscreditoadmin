<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diario_model extends ModelAuth {
    public function retornar_semana(){
        $hoje = date('Y-m-d').' 23:59:59';
        $uma_semana = date('Y-m-d',strtotime($hoje.' -7 days')).' 00:00:00';

        $sql  = 'SELECT tbmain.*,tb2.nome_ou_fantasia AS nome_cliente FROM (';
            $sql .= 'SELECT "Consulta" AS nome,id_cliente_fk,slug,pesquisa AS entrada,criado_em,retorno_json AS retorno,valor, custo FROM consulta_efetuada WHERE criado_em BETWEEN "'.$uma_semana.'" AND "'.$hoje.'" ';
            $sql .= 'UNION ALL ';
            $sql .= 'SELECT "Negativação" AS nome,id_cliente_fk,slug,cpf_cnpj AS entrada,criado_em,retorno AS retorno,valor,custo FROM negativacao WHERE criado_em BETWEEN "'.$uma_semana.'" AND "'.$hoje.'" ';
            $sql .= 'UNION ALL ';
            $sql .= 'SELECT "Baixa" AS nome,id_cliente_fk,slug,cpf_cnpj AS entrada,criado_em,retorno AS retorno,valor,custo FROM negativacao_baixa WHERE criado_em BETWEEN "'.$uma_semana.'" AND "'.$hoje.'" ';
            $sql .= 'UNION ALL ';
            $sql .= 'SELECT "Veicular" AS nome, id_cliente_fk, slug, pesquisa AS entrada, criado_em, retorno AS retorno,valor,custo FROM consulta_veicular_efetuada WHERE criado_em BETWEEN "'.$uma_semana.'" AND "'.$hoje.'" ';
            //$sql .= 'UNION ALL ';
            //$sql .= 'SELECT "Carta" AS nome,id_cliente_fk,cpf_cnpj AS entrada,criado_em,"" AS retorno,valor_carta AS valor,0 AS custo FROM carta WHERE criado_em BETWEEN "'.$uma_semana.'" AND "'.$hoje.'" ';
        $sql .= ') AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'ORDER BY tbmain.criado_em DESC';

        return $this->db->query($sql);
    }

    //public function retornar_
}