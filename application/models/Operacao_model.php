<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operacao_model extends CI_Model {
    public function top3_consultas_clientes($filtros){
        $metricas = $this->metricas_por_slug_fornecedor($filtros);

        $this->db->select('c.slug AS consulta_slug, c.nome AS consulta_nome, c.venda, c.id_grupo_consulta_fk, c.ordem');
        $this->db->select('fc.id_fornecedor_consulta, fc.nome AS fornecedor_consulta_nome, fc.custo');
        $this->db->select('f.slug AS fornecedor, f.nome AS fornecedor_nome');
        $this->db->from('consulta c');
        $this->db->join('fornecedor_consulta fc', 'fc.slug = c.slug', 'inner');
        $this->db->join('fornecedor f', 'f.id_fornecedor = fc.id_fornecedor_fk', 'left');
        $this->db->where('c.status', 1);
        $this->db->where('c.venda >', 0);
        $this->db->order_by('c.id_grupo_consulta_fk', 'ASC');
        $this->db->order_by('c.ordem', 'ASC');
        $this->db->order_by('c.nome', 'ASC');
        $this->db->order_by('fc.custo', 'ASC');
        $fornecedores = $this->db->get()->result();

        $consultas = array();
        foreach($fornecedores as $item){
            $slug = $item->consulta_slug;
            if(!isset($consultas[$slug])){
                $consultas[$slug] = new stdClass();
                $consultas[$slug]->slug = $slug;
                $consultas[$slug]->nome = $item->consulta_nome;
                $consultas[$slug]->venda = $item->venda;
                $consultas[$slug]->fornecedores = array();
            }

            if(count($consultas[$slug]->fornecedores) >= 3) continue;

            $fornecedor_slug = $item->fornecedor != null ? $item->fornecedor : $item->id_fornecedor_consulta;
            $chave = $slug.'|'.$fornecedor_slug;
            $metrica = isset($metricas[$chave]) ? $metricas[$chave] : null;
            $consultas[$slug]->fornecedores[] = $this->montar_provider_top3($item, $metrica);
        }

        return array_values($consultas);
    }

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

    private function metricas_por_slug_fornecedor($filtros){
        $this->aplicar_filtros_metricas($filtros);
        $this->db->select('slug, fornecedor');
        $this->db->select('COUNT(*) AS tentativas', false);
        $this->db->select('SUM(CASE WHEN valido = 1 THEN 1 ELSE 0 END) AS sucessos', false);
        $this->db->select('SUM(CASE WHEN valido = 0 THEN 1 ELSE 0 END) AS falhas', false);
        $this->db->select('SUM(CASE WHEN tipo_erro = "TIMEOUT" THEN 1 ELSE 0 END) AS timeouts', false);
        $this->db->select('ROUND(AVG(tempo_ms), 0) AS media_ms', false);
        $this->db->select('MAX(criado_em) AS ultima_tentativa', false);
        $this->db->group_by(array('slug', 'fornecedor'));
        $rows = $this->db->get('consulta_fornecedor_metric')->result();

        $metricas = array();
        foreach($rows as $row){
            $metricas[$row->slug.'|'.$row->fornecedor] = $row;
        }
        return $metricas;
    }

    private function montar_provider_top3($item, $metrica){
        $provider = new stdClass();
        $provider->fornecedor = $item->fornecedor;
        $provider->fornecedor_nome = $item->fornecedor_nome != null ? $item->fornecedor_nome : $item->fornecedor;
        $provider->custo = $item->custo;
        $provider->tentativas = $metrica != null ? (int)$metrica->tentativas : 0;
        $provider->sucessos = $metrica != null ? (int)$metrica->sucessos : 0;
        $provider->falhas = $metrica != null ? (int)$metrica->falhas : 0;
        $provider->timeouts = $metrica != null ? (int)$metrica->timeouts : 0;
        $provider->media_ms = $metrica != null ? (int)$metrica->media_ms : null;
        $provider->ultima_tentativa = $metrica != null ? $metrica->ultima_tentativa : null;
        $provider->sucesso_percentual = $provider->tentativas > 0 ? round(($provider->sucessos / $provider->tentativas) * 100, 1) : null;
        $provider->classe = $this->classe_saude_provider($provider);
        $provider->rotulo = $this->rotulo_saude_provider($provider);
        return $provider;
    }

    private function classe_saude_provider($provider){
        if($provider->tentativas <= 0) return 'warning';
        if($provider->sucesso_percentual < 70 || $provider->media_ms > 10000) return 'danger';
        if($provider->sucesso_percentual < 90 || $provider->media_ms > 7000 || $provider->timeouts > 0) return 'warning';
        return 'success';
    }

    private function rotulo_saude_provider($provider){
        if($provider->tentativas <= 0) return 'Sem dados';
        if($provider->classe == 'danger') return 'Crítico';
        if($provider->classe == 'warning') return 'Atenção';
        return 'Saudável';
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
