<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cache_consulta_model extends CI_Model {
    private $tabela = 'consulta_cache';

    public function estrutura_disponivel(){
        return $this->db->table_exists($this->tabela)
            && $this->db->field_exists('documento', $this->tabela)
            && $this->db->field_exists('atualizado_em', $this->tabela);
    }

    public function localizar_por_documento($documento){
        if(!$this->estrutura_disponivel() || $documento==='') return array();

        $this->db->select('cache.id_consulta_cache, cache.slug, cache.fornecedor,
            cache.documento_tipo, cache.documento, cache.status_http,
            cache.usado_qtd, cache.ultimo_uso_em, cache.criado_em,
            cache.atualizado_em, cache.expira_em, consulta.nome AS consulta_nome');
        $this->db->from($this->tabela.' AS cache');
        $this->db->join('consulta', 'consulta.slug = cache.slug', 'left');
        $this->db->where('cache.documento', $documento);
        $this->db->order_by('cache.atualizado_em', 'DESC');
        $this->db->order_by('cache.id_consulta_cache', 'DESC');
        return $this->db->get()->result();
    }

    public function excluir_por_documento($documento){
        if(!$this->estrutura_disponivel() || $documento===''){
            return array('ok'=>false, 'quantidade'=>0);
        }

        $this->db->trans_begin();
        $this->db->where('documento', $documento);
        $excluido = $this->db->delete($this->tabela);
        $quantidade = (int) $this->db->affected_rows();

        if(!$excluido || $this->db->trans_status()===FALSE){
            $this->db->trans_rollback();
            return array('ok'=>false, 'quantidade'=>0);
        }

        $this->db->trans_commit();
        return array('ok'=>true, 'quantidade'=>$quantidade);
    }
}
