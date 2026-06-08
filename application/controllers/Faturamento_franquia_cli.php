<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faturamento_franquia_cli extends CI_Controller{
    private $log_file;
    private $lock_file;
    private $mostrar_logs = false;

    public function __construct(){
        parent::__construct();

        if(!$this->input->is_cli_request()){
            show_404();
            exit;
        }

        $this->load->model('faturamento_model', 'faturamento');
        $this->load->model('boletoV3_model', 'boletov3');
        $this->load->model('adminauditoria_model', 'adminauditoria');

        $this->log_file = APPPATH.'logs/faturamento_franquia_cli_'.date('Ymd').'.log';
        $this->lock_file = APPPATH.'logs/faturamento_franquia_cli.lock';
    }

    public function gerar(){
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $params = $this->uri->segment_array();
        $params = array_values(array_slice($params, 2));

        $competencia = $this->resolver_competencia($params);
        $modo = $this->resolver_modo($params);
        $gerar_boletos = $this->resolver_gerar_boletos($params);
        $vencimento_dia = $this->resolver_vencimento($params);
        $this->mostrar_logs = $this->resolver_mostrar_logs($params);

        if(!$this->tem_competencia_manual($params) && (int)date('d')!==7){
            $this->out('Hoje nao e dia automatico de faturamento administrativo de franquias. Nada a fazer.');
            return;
        }

        if(!$this->criar_lock()){
            $this->log('Execucao cancelada: ja existe outro faturamento de franquias em andamento.');
            $this->out('Execucao cancelada: ja existe outro faturamento de franquias em andamento.');
            return;
        }

        try{
            $this->log('Iniciando faturamento adm franquias. competencia='.$competencia.' modo='.$modo.' boletos='.($gerar_boletos ? 'sim' : 'nao').' vencimento='.$this->montar_vencimento($competencia, $vencimento_dia));

            $faturas = $this->gerar_faturas_franquias($competencia, $vencimento_dia, $modo);
            $boletos = $gerar_boletos ? $this->gerar_boletos_franquias($competencia, $vencimento_dia, $modo) : $this->novo_resumo_boletos();

            $resultado = array(
                'modo' => $modo,
                'competencia' => $competencia,
                'vencimento' => $this->montar_vencimento($competencia, $vencimento_dia),
                'boletos_ativados' => $gerar_boletos ? 1 : 0,
                'franquias' => $faturas,
                'boletos' => $boletos
            );

            $this->log('Resumo: '.$this->json($resultado));
            $this->out($this->json($resultado));
        }catch(Exception $e){
            $this->log('ERRO GERAL: '.$e->getMessage());
            $this->auditar('faturamento', 'gerar_faturamento_adm_franquia_cli', 'erro', 'EXCEPTION', $e->getMessage(), null, null, array(
                'competencia' => $competencia,
                'modo' => $modo,
                'boletos' => $gerar_boletos ? 1 : 0
            ));
            $this->out('ERRO GERAL: '.$e->getMessage());
        }

        $this->remover_lock();
    }

    private function gerar_faturas_franquias($competencia, $vencimento_dia, $modo){
        $resultado = array('total'=>0, 'geradas'=>0, 'duplicadas'=>0, 'erros'=>0, 'valor_total'=>0, 'clientes_total'=>0, 'clientes_valor'=>0, 'itens_total'=>0, 'ids'=>array(), 'detalhes'=>array());
        $franquias = $this->retornar_franquias_adm_faturamento();

        foreach($franquias as $franquia){
            $periodo = $this->montar_periodo_adm($competencia, $franquia);
            $fatura = $this->montar_fatura_franquia($franquia, $periodo, $vencimento_dia);
            $resultado['total']++;
            $resultado['valor_total'] += (float)$fatura->valor;
            $resultado['clientes_total'] += (int)$fatura->clientes_qtd;
            $resultado['clientes_valor'] += (float)$fatura->clientes_valor;
            $resultado['itens_total'] += count($fatura->itens);

            if($this->fatura_franquia_existe($fatura)){
                $resultado['duplicadas']++;
                $this->log('Franquia duplicada ignorada. franquia='.$fatura->id_franquia.' inicio='.$fatura->inicio.' fim='.$fatura->fim.' vencimento='.$fatura->vencimento);
                continue;
            }

            if($modo==='dry-run'){
                $this->log('DRY-RUN franquia='.$fatura->id_franquia.' tipo='.$periodo['tipo_faturamento'].' inicio='.$fatura->inicio.' fim='.$fatura->fim.' valor='.$fatura->valor.' clientes='.$fatura->clientes_qtd.' itens='.count($fatura->itens));
                $resultado['detalhes'][] = array('id_franquia'=>$fatura->id_franquia, 'tipo_faturamento'=>$periodo['tipo_faturamento'], 'inicio'=>$fatura->inicio, 'fim'=>$fatura->fim, 'valor'=>round($fatura->valor,2), 'clientes_qtd'=>(int)$fatura->clientes_qtd, 'clientes_valor'=>round($fatura->clientes_valor,2), 'itens'=>count($fatura->itens));
                continue;
            }

            $id_fatura = $this->gravar_fatura_franquia($fatura);
            if($id_fatura){
                $resultado['geradas']++;
                $resultado['ids'][] = $id_fatura;
                $this->log('fatura adm franquia '.$fatura->id_franquia.' - '.$id_fatura.' - success');
            }else{
                $resultado['erros']++;
                $this->log('fatura adm franquia '.$fatura->id_franquia.' - error');
            }
        }

        $resultado['valor_total'] = round($resultado['valor_total'], 2);
        $resultado['clientes_valor'] = round($resultado['clientes_valor'], 2);
        return $resultado;
    }

    private function montar_fatura_franquia($franquia, $periodo, $vencimento_dia){
        $id_franquia = (int)$franquia->id_franquia;
        $itens = array();
        $total = 0;

        $grupos = array(
            array('grupo'=>'consultas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result()),
            array('grupo'=>'consultas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_novas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result()),
            array('grupo'=>'veicular', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_veicular_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result()),
            array('grupo'=>'cartas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_cartas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result()),
            array('grupo'=>'negativacoes', 'rows'=>$this->faturamento->retornar_faturamento_franquia_negativacoes_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result()),
            array('grupo'=>'baixas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_baixas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], null, $periodo['competencia'])->result())
        );

        foreach($grupos as $grupo){
            foreach($grupo['rows'] as $row){
                if(!isset($row->qtd) || (int)$row->qtd<=0){
                    continue;
                }
                $item = array();
                $item['grupo'] = $grupo['grupo'];
                $item['nome'] = $row->nome;
                $item['qtd'] = (int)$row->qtd;
                $item['und'] = (float)$row->und;
                $item['total'] = $grupo['grupo']==='negativacoes' ? ((float)$row->und * (int)$row->qtd) : (float)$row->custo;
                $total += $item['total'];
                $itens[] = $item;
            }
        }

        $qtd_clientes = $this->contar_clientes_ativos_franquia($id_franquia, $periodo['fim']);
        $valor_cliente = isset($franquia->valor_cliente_adm) && $franquia->valor_cliente_adm!==null ? (float)$franquia->valor_cliente_adm : 28.65;

        $fatura = new stdClass();
        $fatura->id_franquia = $id_franquia;
        $fatura->nome = 'Faturamento Administrativo '.ucwords(get_mes(date('m', strtotime($periodo['fim'])))).' de '.date('Y', strtotime($periodo['fim']));
        $fatura->inicio = $periodo['inicio'];
        $fatura->fim = $periodo['fim'];
        $fatura->vencimento = $this->montar_vencimento($periodo['competencia'], $vencimento_dia);
        $fatura->clientes_qtd = $qtd_clientes;
        $fatura->clientes_valor = round($qtd_clientes * $valor_cliente, 2);
        $fatura->valor = round($total + $fatura->clientes_valor, 2);
        $fatura->credito = 0;
        $fatura->debito = 0;
        $fatura->itens = $itens;
        $fatura->franquia = $franquia;
        return $fatura;
    }

    private function gerar_boletos_franquias($competencia, $vencimento_dia, $modo){
        $resumo = $this->novo_resumo_boletos();
        $franquias = $this->retornar_franquias_adm_faturamento();

        foreach($franquias as $franquia){
            $periodo = $this->montar_periodo_adm($competencia, $franquia);
            $faturas = $this->retornar_faturas_franquia_para_boleto($franquia->id_franquia, $periodo, $vencimento_dia);
            $resumo['candidatas'] += count($faturas);

            foreach($faturas as $fatura){
                $this->gerar_boleto_fatura_franquia($fatura, $franquia, $modo, $resumo);
            }
        }

        return $resumo;
    }

    private function gerar_boleto_fatura_franquia($fatura, $franquia, $modo, &$resumo){
        if((float)$fatura->valor<=0){
            $resumo['ignoradas']++;
            return;
        }

        if(isset($fatura->id_boleto_fk) && $fatura->id_boleto_fk!==null && $fatura->id_boleto_fk!==''){
            $resumo['ignoradas']++;
            $this->log('Boleto franquia ignorado: fatura ja possui boleto. fatura='.$fatura->id_adm_franquia_fatura.' boleto='.$fatura->id_boleto_fk);
            return;
        }

        if($modo==='dry-run'){
            $resumo['dry_run']++;
            $this->log('DRY-RUN boleto franquia fatura='.$fatura->id_adm_franquia_fatura.' franquia='.$fatura->id_franquia_fk.' valor='.$fatura->valor);
            return;
        }

        try{
            $pagador = $this->montar_pagador_franquia($franquia);
            $outros = array('descricao_boleto'=>$fatura->nome, 'descricao_boleto2'=>'Fatura de franquia #'.$fatura->id_adm_franquia_fatura);
            $resultado = $this->boletov3->newBoletoPessoaResult($pagador, $fatura->valor, $fatura->vencimento, $outros, 0);
        }catch(Exception $e){
            $resultado = array('success'=>false, 'erro'=>'EXCEPTION', 'mensagem'=>$e->getMessage());
            $this->auditar('boleto', 'boleto_adm_franquia', 'erro', 'EXCEPTION', $e->getMessage(), 'adm_franquia_fatura', $fatura->id_adm_franquia_fatura);
        }

        if($resultado['success']){
            $this->db->where('id_adm_franquia_fatura', $fatura->id_adm_franquia_fatura);
            $this->db->update('adm_franquia_fatura', array('faturado'=>1, 'id_boleto_fk'=>$resultado['id_boleto'], 'hash_boleto'=>$resultado['hash']));
            $resumo['gerados']++;
            $resumo['ids'][] = $resultado['id_boleto'];
            $this->log('boleto adm franquia '.$fatura->id_franquia_fk.' - '.$fatura->id_adm_franquia_fatura.' - success');
            return;
        }

        $resumo['erros']++;
        $this->log('ERRO boleto adm franquia fatura='.$fatura->id_adm_franquia_fatura.' erro='.$resultado['erro'].' mensagem='.$resultado['mensagem']);
    }

    private function gravar_fatura_franquia($fatura){
        $this->db->trans_begin();

        try{
            $id_fatura = $this->proximo_id('adm_franquia_fatura', 'id_adm_franquia_fatura');
            $this->db->insert('adm_franquia_fatura', array(
                'id_adm_franquia_fatura' => $id_fatura,
                'id_franquia_fk' => $fatura->id_franquia,
                'nome' => $fatura->nome,
                'inicio' => $fatura->inicio,
                'fim' => $fatura->fim,
                'valor' => $fatura->valor,
                'vencimento' => $fatura->vencimento,
                'credito' => $fatura->credito,
                'debito' => $fatura->debito,
                'clientes_qtd' => $fatura->clientes_qtd,
                'clientes_valor' => $fatura->clientes_valor
            ));

            $fatura_itens = array();
            $proximo_item_id = $this->proximo_id('adm_franquia_fatura_item', 'id_adm_fraqnuia_fatura_item');
            foreach($fatura->itens as $item){
                $item['id_adm_fraqnuia_fatura_item'] = $proximo_item_id++;
                $item['id_fatura_fk'] = $id_fatura;
                $fatura_itens[] = $item;
            }

            if(!empty($fatura_itens)){
                $this->db->insert_batch('adm_franquia_fatura_item', $fatura_itens);
            }

            if($this->db->trans_status()===false){
                throw new Exception('Falha ao gravar fatura de franquia no banco.');
            }

            $this->db->trans_commit();
            return $id_fatura;
        }catch(Exception $e){
            $this->db->trans_rollback();
            $this->log('ERRO franquia='.$fatura->id_franquia.' '.$e->getMessage());
            return false;
        }
    }

    private function contar_clientes_ativos_franquia($id_franquia, $fim){
        $this->db->where('id_franquia_fk', (int)$id_franquia);
        $this->db->where('consultor', 0);
        $this->db->where('status', 1);
        $this->db->where('id_cliente >', 4);
        $this->db->where('criado_em <=', $fim.' 23:59:59');
        return (int)$this->db->count_all_results('cliente');
    }

    private function retornar_franquias_adm_faturamento(){
        $this->db->select('f.*, c.*');
        $this->db->from('franquia f');
        $this->db->join('franquia_configuracao c', 'c.id_franquia_fk = f.id_franquia', 'left');
        $this->db->where('f.status', 1);
        $this->db->where('f.id_franquia >', 0);
        return $this->db->get()->result();
    }

    private function retornar_faturas_franquia_para_boleto($id_franquia, $periodo, $vencimento_dia){
        $this->db->where('id_franquia_fk', $id_franquia);
        $this->db->where('inicio', $periodo['inicio']);
        $this->db->where('fim', $periodo['fim']);
        $this->db->where('vencimento', $this->montar_vencimento($periodo['competencia'], $vencimento_dia));
        $this->db->where('valor >', 0);
        $this->db->where('id_boleto_fk IS NULL', null, false);
        return $this->db->get('adm_franquia_fatura')->result();
    }

    private function fatura_franquia_existe($fatura){
        $this->db->where('id_franquia_fk', $fatura->id_franquia);
        $this->db->where('inicio', $fatura->inicio);
        $this->db->where('fim', $fatura->fim);
        $this->db->where('vencimento', $fatura->vencimento);
        return $this->db->count_all_results('adm_franquia_fatura') > 0;
    }

    private function montar_pagador_franquia($franquia){
        $pagador = new stdClass();
        $pagador->id_cliente = null;
        $pagador->codigo_sacado = 900000 + (int)$franquia->id_franquia;
        $pagador->nome_ou_fantasia = $franquia->nome_ou_fantasia ? $franquia->nome_ou_fantasia : $franquia->nome;
        $pagador->cpf_cnpj = $franquia->cpf_cnpj ? $franquia->cpf_cnpj : $franquia->cnpj;
        $pagador->logradouro = $franquia->logradouro;
        $pagador->numero = $franquia->numero;
        $pagador->complemento = $franquia->complemento;
        $pagador->bairro = $franquia->bairro;
        $pagador->cidade = $franquia->cidade;
        $pagador->uf = $franquia->uf;
        $pagador->cep = $franquia->cep;
        $pagador->email = $franquia->email;
        return $pagador;
    }

    private function montar_periodo_adm($competencia, $franquia=null){
        $tipo_faturamento = '05a05';
        if($franquia && isset($franquia->tipo_faturamento) && in_array($franquia->tipo_faturamento, array('05a05', '06a05'))){
            $tipo_faturamento = $franquia->tipo_faturamento;
        }

        $inicio = date('Y-m-d', strtotime($competencia.'-05 -1 month'));
        if($tipo_faturamento==='06a05'){
            $inicio = date('Y-m-d', strtotime($inicio.' +1 day'));
        }

        return array(
            'competencia' => $competencia,
            'tipo_faturamento' => $tipo_faturamento,
            'inicio' => $inicio,
            'fim' => date('Y-m-d', strtotime($competencia.'-05'))
        );
    }

    private function montar_vencimento($competencia, $dia_vencimento){
        $ultimo_dia = (int)date('t', strtotime($competencia.'-01'));
        $dia = min((int)$dia_vencimento, $ultimo_dia);
        return $competencia.'-'.str_pad($dia, 2, '0', STR_PAD_LEFT);
    }

    private function resolver_competencia($params){
        foreach($params as $valor){
            if($this->parece_competencia($valor)){
                return $valor;
            }
        }
        return date('Y-m');
    }

    private function resolver_modo($params){
        foreach($params as $param){
            if($param==='dry-run') return 'dry-run';
            if($param==='execute') return 'execute';
        }
        return 'execute';
    }

    private function resolver_gerar_boletos($params){
        foreach($params as $param){
            if($param==='sem-boletos' || $param==='no-boletos') return false;
            if($param==='boletos' || $param==='com-boletos') return true;
        }
        return true;
    }

    private function resolver_vencimento($params){
        foreach($params as $param){
            if(strpos($param, 'vencimento=')===0){
                $dia = (int)substr($param, 11);
                return $dia > 0 ? $dia : 20;
            }
        }
        return 20;
    }

    private function resolver_mostrar_logs($params){
        foreach($params as $param){
            if($param==='logs' || $param==='log') return true;
        }
        return false;
    }

    private function tem_competencia_manual($params){
        foreach($params as $valor){
            if($this->parece_competencia($valor)){
                return true;
            }
        }
        return false;
    }

    private function parece_competencia($valor){
        return is_string($valor) && preg_match('/^\d{4}\-\d{2}$/', $valor);
    }

    private function proximo_id($tabela, $campo){
        $this->db->select_max($campo, 'max_id');
        $row = $this->db->get($tabela)->row();
        return $row && $row->max_id ? ((int)$row->max_id + 1) : 1;
    }

    private function novo_resumo_boletos(){
        return array('candidatas'=>0, 'gerados'=>0, 'ignoradas'=>0, 'erros'=>0, 'dry_run'=>0, 'ids'=>array());
    }

    private function criar_lock(){
        if(file_exists($this->lock_file)){
            $idade = time() - filemtime($this->lock_file);
            if($idade < 7200){
                return false;
            }
        }
        file_put_contents($this->lock_file, date('Y-m-d H:i:s'));
        return true;
    }

    private function remover_lock(){
        if(file_exists($this->lock_file)){
            @unlink($this->lock_file);
        }
    }

    private function log($mensagem){
        $linha = '['.date('Y-m-d H:i:s').'] '.$mensagem.PHP_EOL;
        file_put_contents($this->log_file, $linha, FILE_APPEND);
        if($this->mostrar_logs){
            $this->out($mensagem);
        }
    }

    private function out($mensagem){
        echo $mensagem.PHP_EOL;
    }

    private function json($dados){
        return json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    private function auditar($area, $acao, $status, $erro=null, $mensagem=null, $referencia_tipo=null, $referencia_id=null, $contexto=null, $retorno=null){
        if(!isset($this->adminauditoria)){
            return;
        }

        $this->adminauditoria->registrar(array(
            'area' => $area,
            'acao' => $acao,
            'status' => $status,
            'referencia_tipo' => $referencia_tipo,
            'referencia_id' => $referencia_id,
            'erro' => $erro,
            'mensagem' => $mensagem,
            'contexto' => $contexto,
            'retorno' => $retorno
        ));
    }
}
