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

    public function listar_todas($limite = 200, $offset = 0, $busca = ''){
        $this->db->select('tbmain.*, tbcliente.nome_ou_fantasia AS cliente_nome, tbusuario.usuario AS usuario_nome, COALESCE(baixa_fk.baixas_qtd, baixa_doc.baixas_qtd, 0) AS baixas_qtd, COALESCE(baixa_fk.ultima_baixa_em, baixa_doc.ultima_baixa_em) AS ultima_baixa_em, COALESCE(baixa_fk.id_ultima_baixa, baixa_doc.id_ultima_baixa) AS id_ultima_baixa', false);
        $this->db->from('negativacao AS tbmain');
        $this->db->join('cliente AS tbcliente', 'tbcliente.id_cliente = tbmain.id_cliente_fk', 'left');
        $this->db->join('usuario AS tbusuario', 'tbusuario.id_usuario = tbmain.id_usuario_fk', 'left');
        $this->db->join('(SELECT id_negativacao_fk, COUNT(*) AS baixas_qtd, MAX(criado_em) AS ultima_baixa_em, MAX(id_negativacao_baixa) AS id_ultima_baixa FROM negativacao_baixa WHERE id_negativacao_fk IS NOT NULL GROUP BY id_negativacao_fk) AS baixa_fk', 'baixa_fk.id_negativacao_fk = tbmain.id_negativacao', 'left');
        $this->db->join('(SELECT id_cliente_fk, cpf_cnpj, COUNT(*) AS baixas_qtd, MAX(criado_em) AS ultima_baixa_em, MAX(id_negativacao_baixa) AS id_ultima_baixa FROM negativacao_baixa GROUP BY id_cliente_fk, cpf_cnpj) AS baixa_doc', 'baixa_doc.id_cliente_fk = tbmain.id_cliente_fk AND baixa_doc.cpf_cnpj = tbmain.cpf_cnpj', 'left');
        $this->aplicar_busca($busca);
        $this->db->order_by('tbmain.id_negativacao', 'DESC');
        $this->db->limit((int) $limite, (int) $offset);
        return $this->db->get();
    }

    public function contar_todas($busca = ''){
        $this->db->from('negativacao AS tbmain');
        $this->db->join('cliente AS tbcliente', 'tbcliente.id_cliente = tbmain.id_cliente_fk', 'left');
        $this->aplicar_busca($busca);
        return (int) $this->db->count_all_results();
    }

    private function aplicar_busca($busca){
        $busca = trim((string) $busca);
        if($busca === ''){
            return;
        }

        $this->db->group_start();
        $this->db->like('tbmain.cpf_cnpj', $busca);
        $this->db->or_like('tbmain.slug', $busca);
        $this->db->or_like('tbmain.contrato', $busca);
        $this->db->or_like('tbmain.fornecedor', $busca);
        $this->db->or_like('tbcliente.nome_ou_fantasia', $busca);
        $this->db->group_end();
    }

    public function retornar_dossie($id_negativacao){
        $this->db->select('tbmain.*, tbcliente.nome_ou_fantasia AS cliente_nome, tbusuario.usuario AS usuario_nome, COALESCE(baixa_fk.baixas_qtd, baixa_doc.baixas_qtd, 0) AS baixas_qtd, COALESCE(baixa_fk.ultima_baixa_em, baixa_doc.ultima_baixa_em) AS ultima_baixa_em, COALESCE(baixa_fk.id_ultima_baixa, baixa_doc.id_ultima_baixa) AS id_ultima_baixa', false);
        $this->db->from('negativacao AS tbmain');
        $this->db->join('cliente AS tbcliente', 'tbcliente.id_cliente = tbmain.id_cliente_fk', 'left');
        $this->db->join('usuario AS tbusuario', 'tbusuario.id_usuario = tbmain.id_usuario_fk', 'left');
        $this->db->join('(SELECT id_negativacao_fk, COUNT(*) AS baixas_qtd, MAX(criado_em) AS ultima_baixa_em, MAX(id_negativacao_baixa) AS id_ultima_baixa FROM negativacao_baixa WHERE id_negativacao_fk IS NOT NULL GROUP BY id_negativacao_fk) AS baixa_fk', 'baixa_fk.id_negativacao_fk = tbmain.id_negativacao', 'left');
        $this->db->join('(SELECT id_cliente_fk, cpf_cnpj, COUNT(*) AS baixas_qtd, MAX(criado_em) AS ultima_baixa_em, MAX(id_negativacao_baixa) AS id_ultima_baixa FROM negativacao_baixa GROUP BY id_cliente_fk, cpf_cnpj) AS baixa_doc', 'baixa_doc.id_cliente_fk = tbmain.id_cliente_fk AND baixa_doc.cpf_cnpj = tbmain.cpf_cnpj', 'left');
        $this->db->where('tbmain.id_negativacao', (int) $id_negativacao);
        return $this->db->get();
    }

    public function retornar_baixas_da_negativacao($negativacao){
        $this->db->from('negativacao_baixa AS tbmain');
        $this->db->group_start();
        $this->db->where('tbmain.id_negativacao_fk', $negativacao->id_negativacao);
        $this->db->or_group_start();
        $this->db->where('tbmain.id_negativacao_fk IS NULL', null, false);
        $this->db->where('tbmain.id_cliente_fk', $negativacao->id_cliente_fk);
        $this->db->where('tbmain.cpf_cnpj', $negativacao->cpf_cnpj);
        $this->db->group_end();
        $this->db->group_end();
        $this->db->order_by('tbmain.id_negativacao_baixa', 'DESC');
        return $this->db->get();
    }

    public function retornar_auditorias($referencia_tipo, $referencia_id){
        if(!$this->db->table_exists('adm_auditoria')){
            return $this->db->query('SELECT 1 WHERE 0');
        }

        $this->db->from('adm_auditoria');
        $this->db->where('referencia_tipo', $referencia_tipo);
        $this->db->where('referencia_id', (int) $referencia_id);
        $this->db->order_by('id_adm_auditoria', 'DESC');
        $this->db->limit(100);
        return $this->db->get();
    }}