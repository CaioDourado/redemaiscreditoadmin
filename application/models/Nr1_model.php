<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nr1_model extends CI_Model {
    const COMPANY_TABLE = 'nr1_ivi_empresa';

    public function estrutura_disponivel(){
        return $this->db->field_exists('nr1', 'cliente')
            && $this->db->table_exists(self::COMPANY_TABLE);
    }

    public function listar_clientes($origem = ''){
        $this->db->select('cliente.id_cliente, cliente.nome_ou_fantasia, cliente.razao_social, cliente.cpf_cnpj, cliente.email, cliente.telefone, cliente.celular, cliente.nr1, cliente.id_franquia_fk');
        $this->db->select('franquia.nome_ou_fantasia AS franquia_nome, franquia.razao_social AS franquia_razao_social');
        $this->db->select('empresa.id_nr1_ivi_empresa, empresa.status AS solicitacao_status, empresa.quantidade_vidas, empresa.valor_total, empresa.solicitado_em, empresa.atualizado_em AS solicitacao_atualizada_em');
        $this->db->from('cliente');
        $this->db->join('franquia', 'franquia.id_franquia = cliente.id_franquia_fk', 'left');
        $this->db->join(
            '(SELECT id_cliente_fk, MAX(id_nr1_ivi_empresa) AS id_nr1_ivi_empresa FROM '.self::COMPANY_TABLE." WHERE status = 'contratado' GROUP BY id_cliente_fk) AS ultima_solicitacao",
            'ultima_solicitacao.id_cliente_fk = cliente.id_cliente',
            'inner',
            false
        );
        $this->db->join(self::COMPANY_TABLE.' AS empresa', 'empresa.id_nr1_ivi_empresa = ultima_solicitacao.id_nr1_ivi_empresa', 'inner');

        if($origem === 'matriz') $this->db->where('(cliente.id_franquia_fk IS NULL OR cliente.id_franquia_fk = 0)', null, false);
        if($origem === 'franquia') $this->db->where('cliente.id_franquia_fk >', 0);

        $this->db->order_by('cliente.id_franquia_fk > 0', 'ASC', false);
        $this->db->order_by('franquia.nome_ou_fantasia', 'ASC');
        $this->db->order_by('cliente.nome_ou_fantasia', 'ASC');
        return $this->db->get()->result();
    }

    public function buscar_cliente($id_cliente){
        $this->db->select('cliente.id_cliente, cliente.nome_ou_fantasia, cliente.razao_social, cliente.cpf_cnpj, cliente.email, cliente.telefone, cliente.celular, cliente.nr1, cliente.id_franquia_fk');
        $this->db->select('franquia.nome_ou_fantasia AS franquia_nome, franquia.razao_social AS franquia_razao_social');
        $this->db->select('empresa.*');
        $this->db->from('cliente');
        $this->db->join('franquia', 'franquia.id_franquia = cliente.id_franquia_fk', 'left');
        $this->db->join(
            '(SELECT id_cliente_fk, MAX(id_nr1_ivi_empresa) AS id_nr1_ivi_empresa FROM '.self::COMPANY_TABLE." WHERE status = 'contratado' GROUP BY id_cliente_fk) AS ultima_solicitacao",
            'ultima_solicitacao.id_cliente_fk = cliente.id_cliente',
            'inner',
            false
        );
        $this->db->join(self::COMPANY_TABLE.' AS empresa', 'empresa.id_nr1_ivi_empresa = ultima_solicitacao.id_nr1_ivi_empresa', 'inner');
        $this->db->where('cliente.id_cliente', (int) $id_cliente);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function atualizar_cliente($id_cliente, $dados){
        return $this->db->update('cliente', $dados, array('id_cliente' => (int) $id_cliente));
    }

    public function atualizar_solicitacao($id, $dados, $marcar_contratado = false){
        $this->db->set($dados);
        $this->db->set('status_alterado_em', 'NOW()', false);
        $this->db->set('atualizado_em', 'NOW()', false);
        if($marcar_contratado) $this->db->set('contratado_em', 'NOW()', false);
        $this->db->where('id_nr1_ivi_empresa', (int) $id);
        return $this->db->update(self::COMPANY_TABLE);
    }
}
