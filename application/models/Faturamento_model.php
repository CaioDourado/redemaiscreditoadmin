<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faturamento_model extends CI_Model{
    public function retornar_faturamento_consultas_atual($dia_faturamento=20){
        $faturamento_fim = date('Y-m').'-'.$dia_faturamento.' 23:59:59';
        $faturamento_inicio = date('Y-m-d H:i:s',strtotime($faturamento_fim.' -1 month'));

        $sql  = 'SELECT  ';
        $sql .= 'tbmain.id_cliente_fk, ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome_ou_fantasia, ';
        $sql .= 'UPPER(tb2.razao_social) AS razao_social, ';
        $sql .= 'ROUND(tb2.mensalidade,2) AS mensalidade, ';
        $sql .= 'ROUND(tb2.franquia,2) AS franquia, ';
        $sql .= 'tb2.limite_consulta_qtd AS limite_qtd, ';
        $sql .= 'tb2.limite_consulta_valor AS limite_valor, ';
        $sql .= 'COUNT(*) AS qtd, ';
        $sql .= 'ROUND(SUM(tbmain.custo),2) AS custo, ';
        $sql .= 'ROUND(SUM(tbmain.valor),2) AS valor, ';
        $sql .= '(CASE WHEN (tb2.franquia - SUM(tbmain.valor)) < 0 THEN tb2.franquia ELSE 0 END) AS consumo ';
        $sql .= 'FROM `consulta_efetuada` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$faturamento_inicio.'" AND "'.$faturamento_fim.'"  ';
        $sql .= 'GROUP BY tbmain.id_cliente_fk ';

        return $this->db->query($sql);
    }

    public function retornar_faturamento_negativacoes_atual($dia_faturamento=20){
        $faturamento_fim = date('Y-m').'-'.$dia_faturamento.' 23:59:59';
        $faturamento_inicio = date('Y-m-d H:i:s',strtotime($faturamento_fim.' -1 month'));

        $sql  = 'SELECT  ';
        $sql .= 'tbmain.id_cliente_fk, ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome_ou_fantasia, ';
        $sql .= 'UPPER(tb2.razao_social) AS razao_social, ';
        $sql .= 'ROUND(tb2.mensalidade,2) AS mensalidade, ';
        $sql .= 'ROUND(tb2.franquia,2) AS franquia, ';
        $sql .= 'COUNT(*) AS qtd, ';
        $sql .= 'ROUND(SUM(tbmain.custo),2) AS custo, ';
        $sql .= 'ROUND(SUM(tbmain.valor),2) AS valor ';
        $sql .= 'FROM `negativacao` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$faturamento_inicio.'" AND "'.$faturamento_fim.'"  ';
        $sql .= 'GROUP BY tbmain.id_cliente_fk ';

        return $this->db->query($sql);
    }

    public function retornar_faturamento_negativacoes_baixa_atual($dia_faturamento=20){
        $faturamento_fim = date('Y-m').'-'.$dia_faturamento.' 23:59:59';
        $faturamento_inicio = date('Y-m-d H:i:s',strtotime($faturamento_fim.' -1 month'));

        $sql  = 'SELECT  ';
        $sql .= 'tbmain.id_cliente_fk, ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome_ou_fantasia, ';
        $sql .= 'UPPER(tb2.razao_social) AS razao_social, ';
        $sql .= 'ROUND(tb2.mensalidade,2) AS mensalidade, ';
        $sql .= 'ROUND(tb2.franquia,2) AS franquia, ';
        $sql .= 'COUNT(*) AS qtd, ';
        $sql .= 'ROUND(SUM(tbmain.custo),2) AS custo, ';
        $sql .= 'ROUND(SUM(tbmain.valor),2) AS valor ';
        $sql .= 'FROM `negativacao_baixa` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$faturamento_inicio.'" AND "'.$faturamento_fim.'"  ';
        $sql .= 'GROUP BY tbmain.id_cliente_fk ';

        return $this->db->query($sql);
    }

    public function retornar_faturamento_carta_atual($dia_faturamento=20){
        $faturamento_fim = date('Y-m').'-'.$dia_faturamento.' 23:59:59';
        $faturamento_inicio = date('Y-m-d H:i:s',strtotime($faturamento_fim.' -1 month'));

        $sql  = 'SELECT  ';
        $sql .= 'tbmain.id_cliente_fk, ';
        $sql .= 'UPPER(tb2.nome_ou_fantasia) AS nome_ou_fantasia, ';
        $sql .= 'UPPER(tb2.razao_social) AS razao_social, ';
        $sql .= 'ROUND(tb2.mensalidade,2) AS mensalidade, ';
        $sql .= 'ROUND(tb2.franquia,2) AS franquia, ';
        $sql .= 'COUNT(*) AS qtd, ';
        $sql .= '0 AS custo, ';
        $sql .= 'ROUND(SUM(tbmain.valor_carta),2) AS valor ';
        $sql .= 'FROM `carta` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$faturamento_inicio.'" AND "'.$faturamento_fim.'"  ';
        $sql .= 'GROUP BY tbmain.id_cliente_fk ';

        return $this->db->query($sql);
    }

    public function retornar_clientes_faturamento($dia_faturamento){
        return $this->db->query('SELECT * FROM cliente WHERE consultor = 0 AND dia_vencimento = '.$dia_faturamento.' AND id_franquia_fk = 0 AND status = 1 AND id_cliente>4 ORDER BY nome_ou_fantasia ASC');
    }

    public function retornar_clientes_faturamento_franquia($dia_faturamento,$franquia = 0){
		$sql = 'SELECT * FROM cliente WHERE consultor = 0 AND dia_vencimento = '.$dia_faturamento.' AND id_franquia_fk = '.$franquia.' AND status = 1 AND id_cliente>4 ORDER BY nome_ou_fantasia ASC';
        return $this->db->query($sql);
    }

    public function retornar_gerar_faturamento_consultas($inicio_faturamento,$fim_faturamento,$dia_vencimento){
        $sql  = 'SELECT tbmain.* FROM consulta_efetuada AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE (tbmain.criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'") AND tbmain.id_cliente_fk > 4 AND tb2.dia_vencimento = '.$dia_vencimento.' AND tb2.status = 1 ';
        $sql .= 'ORDER BY tbmain.criado_em ASC ';
        return $this->db->query($sql);
    }

    public function retornar_gerar_faturamento($inicio_faturamento,$fim_faturamento,$dia_vencimento){
        $sql  = 'SELECT id_cliente_fk,entrada,nome,grupo,valor,data ';
        $sql .= 'FROM ( ';
        $sql .= 'SELECT id_cliente_fk,pesquisa AS entrada,nome,slug AS grupo,valor,criado_em AS data FROM consulta_efetuada WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,"" AS entrada,"+ CrÃ©dito Veicular" AS nome,"veicular" AS grupo,valor,criado_em AS data FROM consulta_veicular WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito Carta Extra Judicial" AS nome,"carta" AS grupo, valor_carta AS valor, criado_em AS data FROM carta WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito NegativaÃ§Ã£o" AS nome,"negativacao" AS grupo, valor, criado_em AS data FROM negativacao WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito Baixa" AS nome,"baixa" AS grupo, valor, criado_em AS data FROM negativacao_baixa WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
		$sql .= 'UNION ALL ';
		$sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",pesquisa) AS entrada, nome, "scorepluspfnova" AS grupo, valor, criado_em AS data FROM consulta_gerada WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= ') AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.id_cliente_fk > 4 AND tb2.dia_vencimento = '.$dia_vencimento.' AND tb2.status = 1 ';
        $sql .= 'ORDER BY data ASC ';

        return $this->db->query($sql);
    }

    public function retornar_gerar_faturamento_individual($id_cliente,$inicio_faturamento,$fim_faturamento){
        $sql  = 'SELECT id_cliente_fk,entrada,nome,grupo,valor,data ';
        $sql .= 'FROM ( ';
        $sql .= 'SELECT id_cliente_fk,pesquisa AS entrada,nome,slug AS grupo,valor,criado_em AS data FROM consulta_efetuada WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,"" AS entrada,"+ CrÃ©dito Veicular" AS nome,"veicular" AS grupo,valor,criado_em AS data FROM consulta_veicular WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito Carta Extra Judicial" AS nome,"carta" AS grupo, valor_carta AS valor, criado_em AS data FROM carta WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito NegativaÃ§Ã£o" AS nome,"negativacao" AS grupo, valor, criado_em AS data FROM negativacao WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= 'UNION ALL ';
        $sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ CrÃ©dito Baixa" AS nome,"baixa" AS grupo, valor, criado_em AS data FROM negativacao_baixa WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
		$sql .= 'UNION ALL ';
		$sql .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",pesquisa) AS entrada, nome, "scorepluspfnova" AS grupo, valor, criado_em AS data FROM consulta_gerada WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
        $sql .= ') AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.id_cliente_fk = '.$id_cliente.' ';
        $sql .= 'ORDER BY data ASC ';

        return $this->db->query($sql);
    }

    public function retornar_faturamento_franquia_consultas_resumido($id_franquia, $inicio, $fim){
        $sql  = 'SELECT tbmain.nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, ROUND(SUM(custo)) AS custo_interno ';
        $sql .= 'FROM `consulta_efetuada` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY nome ';
        return $this->db->query($sql);
    }

	public function retornar_faturamento_franquia_consultas_novas_resumido($id_franquia, $inicio, $fim){
		$sql  = 'SELECT tbmain.nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, ROUND(SUM(custo)) AS custo_interno ';
		$sql .= 'FROM `consulta_gerada` AS tbmain ';
		$sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
		$sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
		$sql .= 'GROUP BY nome ';
		return $this->db->query($sql);
	}

    public function retornar_faturamento_franquia_consultas_veicular_resumido($id_franquia, $inicio, $fim){
        $sql  = 'SELECT tbmain.slug AS nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, ROUND(SUM(custo)) AS custo_interno ';
        $sql .= 'FROM `consulta_veicular_efetuada` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
        $sql .= 'GROUP BY slug ';
        return $this->db->query($sql);
    }

    public function retornar_faturamento_franquia_cartas_resumido($id_franquia, $inicio, $fim){
        $sql  = 'SELECT "Carta Extra Judicial" AS nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, 0 AS custo_interno ';
        $sql .= 'FROM `carta` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
        //$sql .= 'GROUP BY nome ';
        return $this->db->query($sql);
    }

    public function retornar_faturamento_franquia_negativacoes_resumido($id_franquia, $inicio, $fim){
        $sql  = 'SELECT "NegativaÃ§Ã£o" AS nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, ROUND(SUM(custo)) AS custo_interno ';
        $sql .= 'FROM `negativacao` AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
        return $this->db->query($sql);
    }

	public function retornar_faturamento_franquia_baixas_resumido($id_franquia, $inicio, $fim){
		$sql  = 'SELECT "Baixa" AS nome,COUNT(*) AS qtd, ROUND(MAX(custo_franquia),2) AS und, ROUND(SUM(custo_franquia),2) AS custo, ROUND(SUM(custo)) AS custo_interno ';
		$sql .= 'FROM `negativacao_baixa` AS tbmain ';
		$sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
		$sql .= 'WHERE tbmain.criado_em BETWEEN "'.$inicio.' 00:00:00" AND "'.$fim.' 23:59:59" AND tb2.id_franquia_fk = '.$id_franquia.' ';
		return $this->db->query($sql);
	}

    public function inserir_fatura($dados){
        if($dados!=null):
            $this->db->insert('fatura',$dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }

    public function id_ultima_fatura(){
        $sql = 'SELECT id_fatura FROM fatura ORDER BY id_fatura DESC LIMIT 1';
        return $this->db->query($sql)->row()->id_fatura;
    }

    public function inserir_fatura_item($dados){
        if($dados!=null):
            $this->db->insert('fatura_item',$dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }

    public function retornar_franquia_dados($id_franquia=null){
        $sql  = 'SELECT tbmain.*,tb2.* FROM franquia AS tbmain ';
        $sql .= 'LEFT JOIN franquia_configuracao AS tb2 ON tbmain.id_franquia = tb2.id_franquia_fk ';
        $sql .= 'WHERE tbmain.id_franquia = '.$id_franquia;
        return $this->db->query($sql);
    }
}


