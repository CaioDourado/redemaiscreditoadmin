<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operacao_model extends CI_Model {
    public function resumo_metricas($filtros){
        $this->aplicar_filtros_metricas($filtros);
        $this->db->select('slug, fornecedor');
        $this->db->select('COUNT(*) AS tentativas', false);
        $this->db->select('SUM(CASE WHEN valido = 1 THEN 1 ELSE 0 END) AS sucessos', false);
        $this->db->select('SUM(CASE WHEN tipo_erro = "TIMEOUT" THEN 1 ELSE 0 END) AS timeouts', false);
        $this->db->select('SUM(CASE WHEN valido = 0 THEN 1 ELSE 0 END) AS falhas', false);
        $this->db->select('ROUND(AVG(tempo_ms), 0) AS media_ms', false);
        $this->db->select('MAX(tempo_ms) AS maior_ms', false);
        $this->db->select('MAX(criado_em) AS ultima_tentativa', false);
        $this->db->group_by(array('slug', 'fornecedor'));
        $this->db->order_by('falhas', 'DESC');
        $this->db->order_by('media_ms', 'DESC');
        return $this->db->get('consulta_fornecedor_metric');
    }

    public function resumo_erros($filtros){
        $this->aplicar_filtros_metricas($filtros);
        $this->db->select('slug, fornecedor, tipo_erro, erro_origem, http_status');
        $this->db->select('COUNT(*) AS qtd', false);
        $this->db->where('valido', 0);
        $this->db->group_by(array('slug', 'fornecedor', 'tipo_erro', 'erro_origem', 'http_status'));
        $this->db->order_by('qtd', 'DESC');
        $this->db->limit(50);
        return $this->db->get('consulta_fornecedor_metric');
    }

    public function ultimas_tentativas($filtros, $limit = 100){
        $this->aplicar_filtros_metricas($filtros);
        $this->db->order_by('criado_em', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('consulta_fornecedor_metric');
    }

    public function slugs_metricas(){
        $this->db->select('slug');
        $this->db->group_by('slug');
        $this->db->order_by('slug', 'ASC');
        return $this->db->get('consulta_fornecedor_metric');
    }

    public function fornecedores_metricas(){
        $this->db->select('fornecedor');
        $this->db->group_by('fornecedor');
        $this->db->order_by('fornecedor', 'ASC');
        return $this->db->get('consulta_fornecedor_metric');
    }

    private function aplicar_filtros_metricas($filtros){
        $inicio = isset($filtros['inicio']) ? $filtros['inicio'] : null;
        $fim = isset($filtros['fim']) ? $filtros['fim'] : null;

        if($inicio != null) $this->db->where('criado_em >=', $inicio.' 00:00:00');
        if($fim != null) $this->db->where('criado_em <=', $fim.' 23:59:59');
        if(isset($filtros['slug']) && $filtros['slug'] != null) $this->db->where('slug', $filtros['slug']);
        if(isset($filtros['fornecedor']) && $filtros['fornecedor'] != null) $this->db->where('fornecedor', $filtros['fornecedor']);
        if(isset($filtros['tipo_erro']) && $filtros['tipo_erro'] != null) $this->db->where('tipo_erro', $filtros['tipo_erro']);
    }
}
