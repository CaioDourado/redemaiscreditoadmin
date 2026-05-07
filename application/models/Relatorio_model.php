<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Relatorio_model extends ModelAuth {
    public function relatorio_consultas_fornecedores($inicio = null, $fim = null){
        $sql  = 'SELECT fornecedor, slug, COUNT(*) AS qtd,TRUNCATE(AVG(custo),2) AS preco_medio ,TRUNCATE(SUM(custo),2) AS total ';
        $sql .= 'FROM `consulta_efetuada`';
        $sql .= 'WHERE `criado_em` BETWEEN "'.$inicio.'" AND "'.$fim.'" GROUP BY slug, fornecedor ORDER BY fornecedor ASC';
        return $this->db->query($sql);
    }

    public function relatorio_veiculares_fornecedores($inicio = null, $fim = null){
        $sql  = 'SELECT fornecedor, slug, COUNT(*) AS qtd,TRUNCATE(AVG(custo),2) AS preco_medio ,TRUNCATE(SUM(custo),2) AS total ';
        $sql .= 'FROM `consulta_veicular_efetuada`';
        $sql .= 'WHERE `criado_em` BETWEEN "'.$inicio.'" AND "'.$fim.'" GROUP BY slug, fornecedor ORDER BY fornecedor ASC';
        return $this->db->query($sql);
    }

    public function relatorio_negativacoes_fornecedores($inicio = null, $fim = null){
        $sql = 'SELECT fornecedor, slug, qtd, preco_medio, total FROM ( ';
        $sql .= 'SELECT fornecedor, slug,COUNT(*) AS qtd,TRUNCATE(AVG(custo),2) AS preco_medio ,TRUNCATE(SUM(custo),2) AS total FROM `negativacao` WHERE `criado_em` BETWEEN "2022-01-01" AND "2022-02-01" GROUP BY slug, fornecedor ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT fornecedor, slug,COUNT(*) AS qtd,TRUNCATE(AVG(custo),2) AS preco_medio ,TRUNCATE(SUM(custo),2) AS total FROM `negativacao_baixa` WHERE `criado_em` BETWEEN "2022-01-01" AND "2022-02-01" GROUP BY slug, fornecedor ';
        $sql .=') AS tbmain  ORDER BY fornecedor ASC, slug ASC ';
        return $this->db->query($sql);
    }

    public function relatorio_clientes_base(){
        $sql  = 'SELECT ';
        $sql .= 'SUM(CASE WHEN id_franquia_fk = 0 THEN 1 ELSE 0 END) qtd_matriz, ';
        $sql .= 'SUM(CASE WHEN id_franquia_fk > 0 THEN 1 ELSE 0 END) qtd_franquia, ';
        $sql .= 'SUM(CASE WHEN id_franquia_fk = 0 THEN mensalidade ELSE 0 END) val_matriz, ';
        $sql .= 'SUM(CASE WHEN id_franquia_fk > 0 THEN mensalidade ELSE 0 END) val_franquia, ';
        $sql .= 'COUNT(*) AS total_qtd, ';
        $sql .= 'SUM(mensalidade) AS total_val ';
        $sql .= 'FROM cliente WHERE status = 1 ';
        return $this->db->query($sql);
    }
}