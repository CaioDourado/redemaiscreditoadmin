<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faturamento_cli extends CI_Controller{
    private $log_file;
    private $lock_file;

    public function __construct(){
        parent::__construct();

        if(!$this->input->is_cli_request()){
            show_404();
            exit;
        }

        $this->load->model('faturamento_model', 'faturamento');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('franquia_model', 'franquia');
        $this->load->model('boletoV3_model', 'boletov3');
        $this->load->model('adminauditoria_model', 'adminauditoria');

        $this->log_file = APPPATH.'logs/faturamento_cli_'.date('Ymd').'.log';
        $this->lock_file = APPPATH.'logs/faturamento_cli.lock';
    }

    public function gerar(){
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $dia_param = $this->uri->segment(3);
        $competencia_param = $this->uri->segment(4);
        $modo_param = $this->uri->segment(5);
        $extra_param = $this->uri->segment(6);

        $modo = $this->resolver_modo($dia_param, $competencia_param, $modo_param, $extra_param);
        $competencia = $this->resolver_competencia($dia_param, $competencia_param, $modo_param, $extra_param);
        $dia_vcto = $this->resolver_dia_vencimento($dia_param);
        $gerar_boletos = $this->resolver_gerar_boletos($dia_param, $competencia_param, $modo_param, $extra_param);

        if($dia_vcto===null){
            $this->log('Hoje nao e dia automatico de faturamento. Nada a fazer.');
            $this->out('Hoje nao e dia automatico de faturamento. Nada a fazer.');
            return;
        }

        if(!$this->criar_lock()){
            $this->log('Execucao cancelada: ja existe outro faturamento em andamento.');
            $this->out('Execucao cancelada: ja existe outro faturamento em andamento.');
            return;
        }

        try{
            $resultado = $this->executar_faturamento($dia_vcto, $competencia, $modo, $gerar_boletos);
            $this->log('Resumo: '.$this->json($resultado));
            $this->out($this->json($resultado));
        }catch(Exception $e){
            $this->log('ERRO GERAL: '.$e->getMessage());
            $this->auditar('faturamento', 'gerar_faturamento_cli', 'erro', 'EXCEPTION', $e->getMessage(), null, null, array(
                'dia_vencimento' => $dia_vcto,
                'competencia' => $competencia,
                'modo' => $modo,
                'boletos' => $gerar_boletos ? 1 : 0
            ));
            $this->out('ERRO GERAL: '.$e->getMessage());
        }

        $this->remover_lock();
    }

    public function boleto(){
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $id_fatura = (int)$this->uri->segment(3);
        $modo = $this->uri->segment(4)==='execute' ? 'execute' : 'dry-run';
        $tipo = $this->uri->segment(5)==='franquia' ? 'franquia' : 'matriz';

        if($id_fatura<=0){
            $this->out('Informe o id da fatura. Ex: php index.php faturamento_cli boleto 14656 dry-run');
            return;
        }

        $resumo = $this->novo_resumo_boletos();
        $resumo['modo'] = $modo;
        $resumo['tipo'] = $tipo;
        $resumo['fatura'] = $id_fatura;

        if($tipo==='franquia'){
            $fatura = $this->retornar_fatura_franquia_por_id($id_fatura);
            if($fatura===null){
                $resumo['erros']++;
                $this->log('ERRO boleto teste franquia: fatura nao encontrada. fatura='.$id_fatura);
                $this->auditar('boleto', 'boleto_cli_teste_franquia', 'erro', 'FATURA_NAO_ENCONTRADA', 'Fatura de franquia nao encontrada.', 'adm_franquia_fatura', $id_fatura);
                $this->out($this->json($resumo));
                return;
            }

            $franquia = $this->retornar_franquia_por_id($fatura->id_franquia_fk);
            if($franquia===null){
                $resumo['erros']++;
                $this->log('ERRO boleto teste franquia: franquia nao encontrada. fatura='.$id_fatura.' franquia='.$fatura->id_franquia_fk);
                $this->auditar('boleto', 'boleto_cli_teste_franquia', 'erro', 'FRANQUIA_NAO_ENCONTRADA', 'Franquia nao encontrada para gerar boleto.', 'adm_franquia_fatura', $id_fatura, array('id_franquia'=>$fatura->id_franquia_fk));
                $this->out($this->json($resumo));
                return;
            }

            $resumo['candidatas'] = 1;
            $this->gerar_boleto_fatura_franquia($fatura, $franquia, $modo, $resumo);
            $this->out($this->json($resumo));
            return;
        }

        $fatura = $this->retornar_fatura_matriz_por_id($id_fatura);
        if($fatura===null){
            $resumo['erros']++;
            $this->log('ERRO boleto teste matriz: fatura nao encontrada. fatura='.$id_fatura);
            $this->auditar('boleto', 'boleto_cli_teste_matriz', 'erro', 'FATURA_NAO_ENCONTRADA', 'Fatura nao encontrada.', 'fatura', $id_fatura);
            $this->out($this->json($resumo));
            return;
        }

        $resumo['candidatas'] = 1;
        $this->gerar_boleto_fatura_matriz($fatura, $modo, $resumo);
        $this->out($this->json($resumo));
    }

    private function executar_faturamento($dia_vcto, $competencia, $modo, $gerar_boletos){
        $config_matriz = $this->retornar_configuracao_faturamento_matriz();
        $periodo_matriz = $this->montar_periodo($dia_vcto, $competencia, $config_matriz->tipo_faturamento);

        $this->log('Iniciando faturamento. dia_vcto='.$dia_vcto.' competencia='.$competencia.' modo='.$modo.' boletos='.($gerar_boletos ? 'sim' : 'nao').' tipo_matriz='.$config_matriz->tipo_faturamento.' inicio='.$periodo_matriz['inicio'].' fim='.$periodo_matriz['fim']);

        $resultado = array(
            'modo' => $modo,
            'dia_vencimento' => $dia_vcto,
            'competencia' => $competencia,
            'boletos_ativados' => $gerar_boletos ? 1 : 0,
            'matriz' => $this->gerar_faturas_matriz($dia_vcto, $competencia, $modo, $periodo_matriz, $config_matriz),
            'franquias' => $this->gerar_faturas_franquias($dia_vcto, $competencia, $modo),
            'boletos' => array('matriz'=>array(), 'franquias'=>array())
        );

        if($gerar_boletos){
            $resultado['boletos']['matriz'] = $this->gerar_boletos_matriz($periodo_matriz, $dia_vcto, $modo);
            $resultado['boletos']['franquias'] = $this->gerar_boletos_franquias($competencia, $dia_vcto, $modo);
        }

        return $resultado;
    }

    private function gerar_faturas_matriz($dia_vcto, $competencia, $modo, $periodo, $config_faturamento){
        $clientes = $this->faturamento->retornar_clientes_faturamento($dia_vcto, $competencia)->result();
        $faturamento = $this->faturamento->retornar_gerar_faturamento($periodo['inicio'].' 00:00:00', $periodo['fim'].' 23:59:59', $dia_vcto, $competencia)->result();
        $faturas = $this->montar_faturas($clientes, $faturamento, $periodo, $dia_vcto);

        $resultado = $this->novo_resumo_faturas($modo, $periodo, $config_faturamento->tipo_faturamento);
        $resultado['clientes'] = count($faturas);

        foreach($faturas as $fatura){
            $this->somar_resumo_fatura($resultado, $fatura);

            if($this->fatura_existe($fatura)){
                $resultado['duplicadas']++;
                $this->log('Matriz duplicada ignorada. cliente='.$fatura->id_cliente.' inicio='.$fatura->inicio.' fim='.$fatura->fim.' vencimento='.$fatura->vencimento);
                continue;
            }

            if($modo==='dry-run'){
                $this->log('DRY-RUN matriz cliente='.$fatura->id_cliente.' valor='.$fatura->valor.' consumo='.$fatura->consumo.' itens='.count($fatura->itens));
                continue;
            }

            $id_fatura = $this->gravar_fatura($fatura);
            if($id_fatura){
                $resultado['geradas']++;
                $resultado['ids'][] = $id_fatura;
            }else{
                $resultado['erros']++;
            }
        }

        return $this->fechar_resumo($resultado);
    }

    private function gerar_faturas_franquias($dia_vcto, $competencia, $modo){
        $resultado = array('total'=>0, 'geradas'=>0, 'duplicadas'=>0, 'erros'=>0, 'valor_total'=>0, 'clientes_total'=>0, 'clientes_valor'=>0, 'itens_total'=>0, 'ids'=>array(), 'detalhes'=>array());
        $franquias = $this->retornar_franquias_faturamento();

        foreach($franquias as $franquia){
            $tipo = in_array($franquia->tipo_faturamento, array('05a05','06a05')) ? $franquia->tipo_faturamento : '05a05';
            $periodo = $this->montar_periodo($dia_vcto, $competencia, $tipo);
            $fatura = $this->montar_fatura_franquia($franquia, $periodo, $dia_vcto);
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
                $this->log('DRY-RUN franquia='.$fatura->id_franquia.' valor='.$fatura->valor.' itens='.count($fatura->itens));
                $resultado['detalhes'][] = array('id_franquia'=>$fatura->id_franquia, 'valor'=>round($fatura->valor,2), 'clientes_qtd'=>(int)$fatura->clientes_qtd, 'clientes_valor'=>round($fatura->clientes_valor,2), 'itens'=>count($fatura->itens));
                continue;
            }

            $id_fatura = $this->gravar_fatura_franquia($fatura);
            if($id_fatura){
                $resultado['geradas']++;
                $resultado['ids'][] = $id_fatura;
            }else{
                $resultado['erros']++;
            }
        }

        $resultado['valor_total'] = round($resultado['valor_total'], 2);
        $resultado['clientes_valor'] = round($resultado['clientes_valor'], 2);
        return $resultado;
    }

    private function montar_faturas($clientes, $faturamento, $periodo, $dia_vcto){
        $faturas = array();

        foreach($clientes as $linha){
            $fatura = new stdClass();
            $fatura->id_cliente = $linha->id_cliente;
            $fatura->valor = 0;
            $fatura->qtd_dias_faturamento = 30;
            $fatura->vencimento = $this->montar_vencimento($periodo['competencia'], $dia_vcto);
            $fatura->debito = (float)$linha->debito;
            $fatura->credito = (float)$linha->credito;
            $fatura->inicio = $periodo['inicio'];
            $fatura->fim = $periodo['fim'];
            $fatura->itens = array();
            $fatura->consumo = 0;

            $dias_faturamento = $this->calcular_dias_faturamento_cliente($linha->criado_em, $periodo);
            if($dias_faturamento<=0){
                continue;
            }

            $fatura->qtd_dias_faturamento = $dias_faturamento;
            if($dias_faturamento>=30){
                $fatura->nome = 'Mensalidade + Consumo ('.ucfirst(get_mes(date('m', strtotime($periodo['competencia'].'-01')))).')';
                $fatura->tipo = 'Completa';
                $fatura->mensalidade = (float)$linha->mensalidade;
                $fatura->franquia = (float)$linha->franquia;
                $fatura->valor = (float)$linha->mensalidade;
            }else{
                $fatura->nome = 'Pro-rata '.date('d/m/Y',strtotime($fatura->inicio)).' a '.date('d/m/Y',strtotime($fatura->fim));
                $fatura->tipo = 'Pro-rata';
                $fatura->mensalidade = round(((float)$linha->mensalidade/30)*$dias_faturamento, 2);
                $fatura->franquia = round(((float)$linha->franquia/30)*$dias_faturamento, 2);
                $fatura->valor = $fatura->mensalidade;
            }

            $faturas[$linha->id_cliente] = $fatura;
        }

        foreach($faturamento as $linha){
            if(!array_key_exists($linha->id_cliente_fk, $faturas)){
                continue;
            }

            $item = new stdClass();
            $item->id_cliente_fk = $linha->id_cliente_fk;
            $item->nome = $linha->nome;
            if($linha->nome=='+ Credito Pefin + Varejo' || $linha->nome=='+ CrÃƒÂ©dito Pefin + Varejo') $item->nome = '+ Credito Pefin';
            if($linha->nome=='+ Credito Pefin + Varejo PJ' || $linha->nome=='+ CrÃƒÂ©dito Pefin + Varejo PJ') $item->nome = '+ Credito Pefin PJ';
            $item->descricao = $linha->entrada;
            $item->grupo = $linha->grupo;
            $item->valor = (float)$linha->valor;
            $item->data = $linha->data;

            $faturas[$linha->id_cliente_fk]->consumo += (float)$linha->valor;
            $faturas[$linha->id_cliente_fk]->itens[] = $item;
        }

        foreach($faturas as $fatura){
            if((float)$fatura->consumo>(float)$fatura->franquia){
                $fatura->valor = round((float)$fatura->valor + ((float)$fatura->consumo - (float)$fatura->franquia), 2);
            }

            if((float)$fatura->debito>0){
                $fatura->valor = round((float)$fatura->valor + (float)$fatura->debito, 2);
            }

            if((float)$fatura->credito>0){
                if((float)$fatura->credito > (float)$fatura->valor){
                    $fatura->credito_novo_cliente = round((float)$fatura->credito - (float)$fatura->valor, 2);
                    $fatura->credito = $fatura->valor;
                    $fatura->valor = 0;
                }else{
                    $fatura->credito_novo_cliente = 0;
                    $fatura->valor = round((float)$fatura->valor - (float)$fatura->credito, 2);
                }
            }else{
                $fatura->credito_novo_cliente = 0;
            }
        }

        return $faturas;
    }

    private function montar_fatura_franquia($franquia, $periodo, $dia_vcto){
        $id_franquia = (int)$franquia->id_franquia;
        $itens = array();
        $total = 0;

        $grupos = array(
            array('grupo'=>'consultas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result()),
            array('grupo'=>'consultas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_novas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result()),
            array('grupo'=>'veicular', 'rows'=>$this->faturamento->retornar_faturamento_franquia_consultas_veicular_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result()),
            array('grupo'=>'cartas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_cartas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result()),
            array('grupo'=>'negativacoes', 'rows'=>$this->faturamento->retornar_faturamento_franquia_negativacoes_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result()),
            array('grupo'=>'baixas', 'rows'=>$this->faturamento->retornar_faturamento_franquia_baixas_resumido($id_franquia, $periodo['inicio'], $periodo['fim'], $dia_vcto, $periodo['competencia'])->result())
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

        $qtd_clientes = (int)$this->franquia->franquia_qtd_clientes($id_franquia, $periodo['fim'], $dia_vcto, $periodo['competencia']);
        $valor_cliente = (float)$franquia->mensalidade;

        $fatura = new stdClass();
        $fatura->id_franquia = $id_franquia;
        $fatura->nome = 'Mensalidade Referente a '.ucwords(get_mes(date('m',strtotime($periodo['inicio'])))).' de '.date('Y', strtotime($periodo['fim']));
        $fatura->inicio = $periodo['inicio'];
        $fatura->fim = $periodo['fim'];
        $fatura->vencimento = $this->montar_vencimento($periodo['competencia'], $dia_vcto);
        $fatura->clientes_qtd = $qtd_clientes;
        $fatura->clientes_valor = round($qtd_clientes * $valor_cliente, 2);
        $fatura->valor = round($total + $fatura->clientes_valor, 2);
        $fatura->credito = 0;
        $fatura->debito = 0;
        $fatura->itens = $itens;
        $fatura->franquia = $franquia;
        return $fatura;
    }

    private function gerar_boletos_matriz($periodo, $dia_vcto, $modo){
        $resumo = $this->novo_resumo_boletos();
        $faturas = $this->retornar_faturas_matriz_para_boleto($periodo, $dia_vcto);
        $resumo['candidatas'] = count($faturas);

        foreach($faturas as $fatura){
            $this->gerar_boleto_fatura_matriz($fatura, $modo, $resumo);
        }

        return $resumo;
    }

    private function gerar_boletos_franquias($competencia, $dia_vcto, $modo){
        $resumo = $this->novo_resumo_boletos();
        $franquias = $this->retornar_franquias_faturamento();

        foreach($franquias as $franquia){
            if((int)$franquia->gerar_boleto_matriz!==1){
                continue;
            }
            $tipo = in_array($franquia->tipo_faturamento, array('05a05','06a05')) ? $franquia->tipo_faturamento : '05a05';
            $periodo = $this->montar_periodo($dia_vcto, $competencia, $tipo);
            $faturas = $this->retornar_faturas_franquia_para_boleto($franquia->id_franquia, $periodo, $dia_vcto);
            $resumo['candidatas'] += count($faturas);

            foreach($faturas as $fatura){
                $this->gerar_boleto_fatura_franquia($fatura, $franquia, $modo, $resumo);
            }
        }

        return $resumo;
    }

    private function gerar_boleto_fatura_matriz($fatura, $modo, &$resumo){
        if((float)$fatura->valor<=0){
            $resumo['ignoradas']++;
            return;
        }

        if(isset($fatura->id_boleto_fk) && $fatura->id_boleto_fk!==null && $fatura->id_boleto_fk!==''){
            $resumo['ignoradas']++;
            $this->log('Boleto matriz ignorado: fatura ja possui boleto. fatura='.$fatura->id_fatura.' boleto='.$fatura->id_boleto_fk);
            return;
        }

        if($modo==='dry-run'){
            $resumo['dry_run']++;
            $this->log('DRY-RUN boleto matriz fatura='.$fatura->id_fatura.' cliente='.$fatura->id_cliente_fk.' valor='.$fatura->valor);
            return;
        }

        try{
            $outros = array('descricao_boleto'=>$fatura->nome);
            $resultado = $this->boletov3->newBoletoResult($fatura->id_cliente_fk, $fatura->valor, $fatura->vencimento, $outros, 0);
        }catch(Exception $e){
            $resultado = array('success'=>false, 'erro'=>'EXCEPTION', 'mensagem'=>$e->getMessage());
            $this->auditar('boleto', 'boleto_matriz', 'erro', 'EXCEPTION', $e->getMessage(), 'fatura', $fatura->id_fatura);
        }

        if($resultado['success']){
            $this->db->where('id_fatura', $fatura->id_fatura);
            $this->db->update('fatura', array('faturado'=>1, 'id_boleto_fk'=>$resultado['id_boleto'], 'hash_boleto'=>$resultado['hash']));
            $resumo['gerados']++;
            $resumo['ids'][] = $resultado['id_boleto'];
            $this->log('Boleto matriz gerado. fatura='.$fatura->id_fatura.' boleto='.$resultado['id_boleto']);
            return;
        }

        $resumo['erros']++;
        $this->log('ERRO boleto matriz fatura='.$fatura->id_fatura.' erro='.$resultado['erro'].' mensagem='.$resultado['mensagem']);
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
            $this->auditar('boleto', 'boleto_franquia', 'erro', 'EXCEPTION', $e->getMessage(), 'adm_franquia_fatura', $fatura->id_adm_franquia_fatura);
        }

        if($resultado['success']){
            $this->db->where('id_adm_franquia_fatura', $fatura->id_adm_franquia_fatura);
            $this->db->update('adm_franquia_fatura', array('faturado'=>1, 'id_boleto_fk'=>$resultado['id_boleto'], 'hash_boleto'=>$resultado['hash']));
            $resumo['gerados']++;
            $resumo['ids'][] = $resultado['id_boleto'];
            $this->log('Boleto franquia gerado. fatura='.$fatura->id_adm_franquia_fatura.' boleto='.$resultado['id_boleto']);
            return;
        }

        $resumo['erros']++;
        $this->log('ERRO boleto franquia fatura='.$fatura->id_adm_franquia_fatura.' erro='.$resultado['erro'].' mensagem='.$resultado['mensagem']);
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

    private function calcular_dias_faturamento_cliente($criado_em, $periodo){
        $inicio_periodo = strtotime($periodo['inicio']);
        $fim_periodo = strtotime($periodo['fim']);
        $criado = strtotime(date('Y-m-d', strtotime($criado_em)));

        if($criado===false){
            return 30;
        }
        if($criado <= $inicio_periodo){
            return 30;
        }
        if($criado > $fim_periodo){
            return 0;
        }
        return (int)floor(($fim_periodo - $criado) / 86400) + 1;
    }

    private function gravar_fatura($fatura){
        $this->db->trans_begin();

        try{
            $id_fatura = $this->proximo_id('fatura', 'id_fatura');
            $dados = array(
                'id_fatura' => $id_fatura,
                'id_cliente_fk' => $fatura->id_cliente,
                'nome' => $fatura->nome,
                'inicio' => $fatura->inicio,
                'fim' => $fatura->fim,
                'tipo' => $fatura->tipo,
                'mensalidade' => $fatura->mensalidade,
                'franquia' => $fatura->franquia,
                'consumo' => $fatura->consumo,
                'valor' => $fatura->valor,
                'vencimento' => $fatura->vencimento,
                'credito' => $fatura->credito,
                'debito' => $fatura->debito
            );

            $this->db->insert('fatura', $dados);

            foreach($fatura->itens as $item){
                $this->db->insert('fatura_item', array(
                    'id_fatura_item' => $this->proximo_id('fatura_item', 'id_fatura_item'),
                    'id_fatura_fk' => $id_fatura,
                    'id_cliente_fk' => $item->id_cliente_fk,
                    'nome' => $item->nome,
                    'descricao' => $item->descricao,
                    'grupo' => $item->grupo,
                    'valor' => $item->valor,
                    'data' => $item->data
                ));
            }

            if((float)$fatura->debito>0){
                $this->cliente->alterar($fatura->id_cliente, array('debito'=>0));
            }

            if((float)$fatura->credito>0){
                $this->cliente->registrar_protocolo(array(
                    'id_cliente_fk' => $fatura->id_cliente,
                    'titulo' => 'Protocolo Automatico',
                    'descricao' => 'Credito aplicado automaticamente na fatura '.$fatura->nome.'. Credito restante: R$ '.$fatura->credito_novo_cliente
                ));
                $this->cliente->alterar($fatura->id_cliente, array('credito'=>$fatura->credito_novo_cliente));
            }

            if($this->db->trans_status()===false){
                throw new Exception('Falha ao gravar fatura no banco.');
            }

            $this->db->trans_commit();
            $this->log('Gerada fatura='.$id_fatura.' cliente='.$fatura->id_cliente.' valor='.$fatura->valor.' consumo='.$fatura->consumo.' itens='.count($fatura->itens));
            return $id_fatura;
        }catch(Exception $e){
            $this->db->trans_rollback();
            $this->log('ERRO cliente='.$fatura->id_cliente.' '.$e->getMessage());
            return false;
        }
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

            foreach($fatura->itens as $item){
                $item['id_adm_fraqnuia_fatura_item'] = $this->proximo_id('adm_franquia_fatura_item', 'id_adm_fraqnuia_fatura_item');
                $item['id_fatura_fk'] = $id_fatura;
                $this->db->insert('adm_franquia_fatura_item', $item);
            }

            if($this->db->trans_status()===false){
                throw new Exception('Falha ao gravar fatura de franquia no banco.');
            }

            $this->db->trans_commit();
            $this->log('Gerada fatura franquia='.$id_fatura.' franquia='.$fatura->id_franquia.' valor='.$fatura->valor.' itens='.count($fatura->itens));
            return $id_fatura;
        }catch(Exception $e){
            $this->db->trans_rollback();
            $this->log('ERRO franquia='.$fatura->id_franquia.' '.$e->getMessage());
            return false;
        }
    }

    private function retornar_franquias_faturamento(){
        if(!$this->db->field_exists('auto_faturar', 'franquia_configuracao')){
            $this->log('Franquias ignoradas: coluna franquia_configuracao.auto_faturar nao existe.');
            return array();
        }

        $this->db->select('f.*, c.*');
        $this->db->from('franquia f');
        $this->db->join('franquia_configuracao c', 'c.id_franquia_fk = f.id_franquia', 'left');
        $this->db->where('f.status', 1);
        $this->db->where('f.id_franquia >', 0);
        $this->db->where('c.auto_faturar', 1);
        return $this->db->get()->result();
    }

    private function retornar_faturas_matriz_para_boleto($periodo, $dia_vcto){
        $this->db->select('f.*');
        $this->db->from('fatura f');
        $this->db->join('cliente c', 'c.id_cliente = f.id_cliente_fk', 'inner');
        $this->db->where('f.inicio', $periodo['inicio']);
        $this->db->where('f.fim', $periodo['fim']);
        $this->db->where('f.vencimento', $this->montar_vencimento($periodo['competencia'], $dia_vcto));
        $this->db->where('f.valor >', 0);
        $this->db->where('f.id_boleto_fk IS NULL', null, false);
        $this->db->where('c.id_franquia_fk', 0);
        return $this->db->get()->result();
    }

    private function retornar_fatura_matriz_por_id($id_fatura){
        $this->db->where('id_fatura', (int)$id_fatura);
        $this->db->limit(1);
        return $this->db->get('fatura')->row();
    }

    private function retornar_fatura_franquia_por_id($id_fatura){
        $this->db->where('id_adm_franquia_fatura', (int)$id_fatura);
        $this->db->limit(1);
        return $this->db->get('adm_franquia_fatura')->row();
    }

    private function retornar_franquia_por_id($id_franquia){
        $this->db->select('f.*, c.*');
        $this->db->from('franquia f');
        $this->db->join('franquia_configuracao c', 'c.id_franquia_fk = f.id_franquia', 'left');
        $this->db->where('f.id_franquia', (int)$id_franquia);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    private function retornar_faturas_franquia_para_boleto($id_franquia, $periodo, $dia_vcto){
        $this->db->where('id_franquia_fk', $id_franquia);
        $this->db->where('inicio', $periodo['inicio']);
        $this->db->where('fim', $periodo['fim']);
        $this->db->where('vencimento', $this->montar_vencimento($periodo['competencia'], $dia_vcto));
        $this->db->where('valor >', 0);
        $this->db->where('id_boleto_fk IS NULL', null, false);
        return $this->db->get('adm_franquia_fatura')->result();
    }

    private function fatura_existe($fatura){
        $this->db->where('id_cliente_fk', $fatura->id_cliente);
        $this->db->where('inicio', $fatura->inicio);
        $this->db->where('fim', $fatura->fim);
        $this->db->where('vencimento', $fatura->vencimento);
        return $this->db->count_all_results('fatura') > 0;
    }

    private function fatura_franquia_existe($fatura){
        $this->db->where('id_franquia_fk', $fatura->id_franquia);
        $this->db->where('inicio', $fatura->inicio);
        $this->db->where('fim', $fatura->fim);
        $this->db->where('vencimento', $fatura->vencimento);
        return $this->db->count_all_results('adm_franquia_fatura') > 0;
    }

    private function proximo_id($tabela, $campo){
        $this->db->select_max($campo, 'max_id');
        $row = $this->db->get($tabela)->row();
        return $row && $row->max_id ? ((int)$row->max_id + 1) : 1;
    }

    private function resolver_dia_vencimento($dia_param){
        $dia_hoje = (int)date('d');
        if($dia_param===null || $dia_param==='' || $dia_param==='auto'){
            if($dia_hoje===6) return 15;
            if($dia_hoje===21) return 30;
            return null;
        }
        $dia = (int)$dia_param;
        if($dia===5) return 15;
        if($dia===20) return 30;
        if($dia===15 || $dia===30) return $dia;
        return null;
    }

    private function resolver_competencia(){
        foreach(func_get_args() as $valor){
            if($this->parece_competencia($valor)) return $valor;
        }
        return date('Y-m');
    }

    private function resolver_modo(){
        foreach(func_get_args() as $param){
            if($param==='dry-run') return 'dry-run';
            if($param==='execute') return 'execute';
        }
        return 'execute';
    }

    private function resolver_gerar_boletos(){
        $params = func_get_args();
        foreach($params as $param){
            if($param==='sem-boletos' || $param==='no-boletos') return false;
            if($param==='boletos' || $param==='com-boletos') return true;
        }
        return true;
    }

    private function parece_competencia($valor){
        return is_string($valor) && preg_match('/^[0-9]{4}\-[0-9]{2}$/', $valor);
    }

    private function retornar_configuracao_faturamento_matriz(){
        $config = new stdClass();
        $config->tipo_faturamento = '05a05';
        $config->gerar_boleto_matriz = 0;

        if(!$this->db->field_exists('tipo_faturamento', 'franquia_configuracao')){
            return $config;
        }

        $this->db->select('tipo_faturamento, gerar_boleto_matriz');
        $this->db->where('id_franquia_fk', 0);
        $this->db->limit(1);
        $row = $this->db->get('franquia_configuracao')->row();

        if($row!==null){
            if(in_array($row->tipo_faturamento, array('05a05', '06a05'))){
                $config->tipo_faturamento = $row->tipo_faturamento;
            }
            $config->gerar_boleto_matriz = (int)$row->gerar_boleto_matriz;
        }
        return $config;
    }

    private function montar_periodo($dia_vcto, $competencia, $tipo_faturamento='05a05'){
        $dia_start = array(15=>5, 30=>20);
        $dia_fat = $dia_start[(int)$dia_vcto];
        $inicio = date('Y-m-d', strtotime($competencia.'-'.$dia_fat.' -1 month'));
        if($tipo_faturamento==='06a05'){
            $inicio = date('Y-m-d', strtotime($inicio.' +1 day'));
        }
        return array(
            'competencia' => $competencia,
            'tipo_faturamento' => $tipo_faturamento,
            'inicio' => $inicio,
            'fim' => date('Y-m-d', strtotime($competencia.'-'.$dia_fat))
        );
    }

    private function montar_vencimento($competencia, $dia_vcto){
        $ultimo_dia = (int)date('t', strtotime($competencia.'-01'));
        $dia = min((int)$dia_vcto, $ultimo_dia);
        return $competencia.'-'.str_pad($dia, 2, '0', STR_PAD_LEFT);
    }

    private function novo_resumo_faturas($modo, $periodo, $tipo_faturamento){
        return array(
            'modo' => $modo,
            'tipo_faturamento' => $tipo_faturamento,
            'inicio' => $periodo['inicio'],
            'fim' => $periodo['fim'],
            'clientes' => 0,
            'geradas' => 0,
            'duplicadas' => 0,
            'erros' => 0,
            'valor_total' => 0,
            'valor_completa' => 0,
            'valor_prorata' => 0,
            'faturas_completas' => 0,
            'faturas_prorata' => 0,
            'consumo_total' => 0,
            'itens_total' => 0,
            'ids' => array()
        );
    }

    private function somar_resumo_fatura(&$resultado, $fatura){
        $resultado['valor_total'] += (float)$fatura->valor;
        if($fatura->tipo==='Pro-rata'){
            $resultado['valor_prorata'] += (float)$fatura->valor;
            $resultado['faturas_prorata']++;
        }else{
            $resultado['valor_completa'] += (float)$fatura->valor;
            $resultado['faturas_completas']++;
        }
        $resultado['consumo_total'] += (float)$fatura->consumo;
        $resultado['itens_total'] += count($fatura->itens);
    }

    private function fechar_resumo($resultado){
        foreach(array('valor_total','valor_completa','valor_prorata','consumo_total') as $campo){
            $resultado[$campo] = round($resultado[$campo], 2);
        }
        return $resultado;
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
