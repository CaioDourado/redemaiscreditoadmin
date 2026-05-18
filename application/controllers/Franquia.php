<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Franquia extends ControllerAuth{

    public function __construct(){
        parent::__construct();
        $this->load->model('franquia_model','franquia');
        $this->parameters['title'] = 'Franquia';
    }

    public function index(){
        $franquias = $this->franquia->retornar_franquias()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-home"></i> Franquia';
        $this->parameters['pg_subtitle'] = 'Gerenciamento de Franquias';

        $this->parameters['menu'] = $this->load_menu('franquia');
        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'index', 'franquias' => $franquias), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function perfil(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }
        $franquia = $this->franquia->retornar_franquia($id_franquia)->row();
        $unidades = $this->franquia->retornar_unidades($id_franquia)->result();
        $boletos = array();
        if($franquia!=null&&$franquia->cnpj!=null){
            $boletos = $this->franquia->retornar_boletos_por_cnpj($franquia->cnpj)->result();
        }

        $this->parameters['pg_title'] = '<i class="fa fa-user"></i> Perfil de Franquia';
        $this->parameters['pg_subtitle'] = 'Dados base, unidades, boletos e faturas.';

        $bt_block = anchor('franquia/bloqueio/'.$id_franquia.'/1','<i class="fa fa-ban"></i> Bloquear',array('class'=>'btg'));
        if($franquia->status==0){
            $bt_block = anchor('franquia/bloqueio/'.$id_franquia.'/0','<i class="fa fa-ban"></i> Desbloquear',array('class'=>'btg'));
        }

        /*
        $this->parameters['pg_bts'] = array(
            anchor('franquia/alterar/'.$id_franquia,'<i class="fa fa-pencil"></i> Alterar',array('class'=>'btg')),
            anchor('franquia/cadastrar_unidade/'.$id_franquia,'<i class="fa fa-plus"></i> Unidade',array('class'=>'btg')),
            anchor('franquia/valores_e_precos/'.$id_franquia,'<i class="fa fa-money"></i> Valores e Preços',array('class'=>'btg')),
            anchor('franquia/gerar_faturamento/'.$id_franquia,'<i class="fa fa-arrow-right"></i> Gerar Faturamento',array('class'=>'btg')),
            anchor('franquia/clientes/'.$id_franquia,'<i class="fa fa-users"></i> Clientes',array('class'=>'btg')),
            $bt_block
        );
        */

        $this->parameters['menu'] = $this->load_menu('franquia');
        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'perfil', 'franquia'=>$franquia, 'unidades'=>$unidades, 'boletos'=>$boletos, 'id_franquia'=>$id_franquia), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function cadastrar(){
        $this->form_validation->set_rules('nome_ou_fantasia', 'Nome Fantasia', 'required');
        $this->form_validation->set_rules('razao_social', 'Razão Social', 'required');
        $this->form_validation->set_rules('cnpj', 'CNPJ', 'required');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome_ou_fantasia','razao_social','cnpj'),$this->input->post());
            if($this->franquia->inserir_franquia($dados))
                set_msg('Franquia Inserida com sucesso.','success');
            else
                set_msg('Ocorreu um erro ao inserir a Franquia.');

            redirect(current_url());
        }

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Cadastrar Franquia';
        $this->parameters['pg_subtitle'] = '';

        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'cadastrar'), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function alterar(){

    }

    public function perfil_unidade(){
        $id_unidade = $this->uri->segment(3);
        if($id_unidade==null){
            set_msg('Não foi informada uma Unidade.');
            redirect('franquia');
        }
        $unidade = $this->franquia->retornar_unidade($id_unidade)->row();

        $clientes = array();

        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'perfil_unidade','unidade' => $unidade, 'clientes'=>$clientes), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function cadastrar_unidade(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }
        $franquia = $this->franquia->retornar_franquia($id_franquia)->row();

        $this->form_validation->set_rules('nome', 'Nome da Unidade', 'required');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome'),$this->input->post());
            $dados['id_franquia_fk'] = $id_franquia;
            if($this->franquia->inserir_unidade($dados))
                set_msg('Unidade Criada com sucesso.','success');
            else
                set_msg('Ocorreu um erro ao criar a Unidade.');

            redirect(current_url());
        }

        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'cadastrar_unidade', 'franquia'=>$franquia), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function valores_e_precos(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }
        $this->load->model('consulta_model','consulta');
        $franquia = $this->franquia->retornar_franquia($id_franquia)->row();
        $consultas = $this->consulta->retornar_todos()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-user"></i> Valores e preços de Franquia';
        $this->parameters['pg_subtitle'] = 'Gerencie os valores base e de venda das consultas e negativações da franquia.';

        $this->parameters['menu'] = $this->load_menu('franquia');
        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'valores_e_precos', 'franquia'=>$franquia, 'consultas'=>$consultas), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function gerar_faturamento(){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);

        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }

        $this->form_validation->set_rules('dia_vcto', 'Dia de Vencimento', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->model('cliente_model','cliente');
            $this->load->model('faturamento_model','faturamento');

            $dia_start = array('15'=>'5','30'=>'20');
            $dia_vcto = $this->input->post('dia_vcto');
            $dia_fat = $dia_start[$dia_vcto];

            $inicio = date('Y-m-d',strtotime(date('Y-m').'-'.$dia_fat.'-1 month')).' 00:00:00';
            $fim = date('Y-m').'-'.$dia_fat.' 23:59:59';

            $clientes = $this->faturamento->retornar_clientes_faturamento_franquia($dia_vcto,$id_franquia)->result();
            $faturamento = $this->faturamento->retornar_gerar_faturamento($inicio,$fim,$dia_vcto)->result();

            $faturas = array();

            foreach($clientes as $index => $linha):
                $fatura_atual = new stdClass();
                $fatura_atual->id_cliente = $linha->id_cliente;
                $fatura_atual->valor = 0;
                $fatura_atual->qtd_dias_faturamento = 30;
                $fatura_atual->vencimento = date('Y-m').'-'.$dia_vcto;
                $fatura_atual->debito = $linha->debito;
                $fatura_atual->credito = $linha->credito;
                $diferenca_dias = intval(abs(strtotime($linha->criado_em)-strtotime(date('Y-m-d H:i:s')))/86400);
                if($diferenca_dias>30){
                    $fatura_atual->inicio = date('Y-m-d',strtotime(date('Y-m').'-'.$dia_fat.'-1 month'));
                    $fatura_atual->fim = date('Y-m').'-'.$dia_fat;
                    $fatura_atual->nome = 'Mensalidade + Consumo ('.ucfirst(get_mes(date('m'))).')';
                    $fatura_atual->tipo = 'Completa';
                    $fatura_atual->mensalidade = $linha->mensalidade;
                    $fatura_atual->franquia = $linha->franquia;
                    $fatura_atual->valor = $linha->mensalidade;
                    $fatura_atual->consumo = 0;
                }else{
                    $fatura_atual->inicio = date('Y-m-d',strtotime($linha->criado_em));
                    $fatura_atual->fim = date('Y-m').'-'.$dia_fat;
                    $fatura_atual->nome = 'Pró-rata '.date('d/m/Y',strtotime($fatura_atual->inicio)).' à '.date('d/m/Y',strtotime($fatura_atual->fim));
                    $fatura_atual->tipo = 'Pró-rata';
                    $fatura_atual->mensalidade = dinheiro(($linha->mensalidade/30)*$diferenca_dias);
                    $fatura_atual->franquia = dinheiro(($linha->franquia/30)*$diferenca_dias);
                    $fatura_atual->valor = $fatura_atual->mensalidade;
                    $fatura_atual->consumo = 0;
                }
                $fatura_atual->itens = array();
                $faturas[$linha->id_cliente] = $fatura_atual;
            endforeach;

            foreach($faturamento as $index => $linha):
                if(array_key_exists($linha->id_cliente_fk,$faturas)):
                    $fatura_item_atual = new stdClass();
                    $fatura_item_atual->id_cliente_fk = $linha->id_cliente_fk;
                    $fatura_item_atual->nome = $linha->nome;
                    if($linha->nome=="+ Crédito Pefin + Varejo") $fatura_item_atual->nome = "+ Crédito Pefin";
                    if($linha->nome=="+ Crédito Pefin + Varejo PJ") $fatura_item_atual->nome = "+ Crédito Pefin PJ";
                    $fatura_item_atual->descricao = $linha->entrada;
                    $fatura_item_atual->grupo = $linha->grupo;
                    $fatura_item_atual->valor = $linha->valor;
                    $fatura_item_atual->data = $linha->data;
                    $faturas[$linha->id_cliente_fk]->consumo += $linha->valor;
                    array_push($faturas[$linha->id_cliente_fk]->itens,$fatura_item_atual);
                endif;
            endforeach;

            foreach($faturas as $index => $fatura):
                // Verifica se o consumo foi maior que a franquia e adiciona o valor do consumo ao valor da fatura
				$mens = (float)$fatura->mensalidade;
				$cons = (float)$fatura->consumo;
				$franq = (float)$fatura->franquia;

                if($fatura->consumo>$fatura->franquia){
					$fatura->valor = money($mens+($cons-$franq));
				}else{
					$fatura->valor = money($mens);
				}
                // Verifica se o cliente possui debitos, e adiciona na fatura.
                if($fatura->debito>0){
                    $fatura->valor = money($fatura->valor + $fatura->debito);
                    $this->cliente->alterar($fatura->id_cliente,array('debito'=>0));
                }
                // Verifica se o cliente possui créditos, e adiciona na fatura.
                if($fatura->credito>0){
                    //echo 'Credito: '.$fatura->credito.'|'.$fatura->id_cliente.'|'.$fatura->valor;
                    $att_cliente = $fatura->credito;
                    if($fatura->credito > $fatura->valor){
                        $att_cliente = $fatura->credito - $fatura->valor;
                        $fatura->credito = $fatura->valor;
                        $fatura->valor = money(0);
                        $protocolo = 'Inserido Crédito na fatura '.$fatura->nome.' no Valor de R$ '.$fatura->valor.' e atualizado novo crédito para R$ '.$att_cliente;
                    }else{
                        $att_cliente = 0;
                        $fatura->valor = money($fatura->valor - $fatura->credito);
                        $protocolo = 'Finalizado Crédito na fatura '.$fatura->nome.' no valor de '.$fatura->credito.'.';
                    }
                    $this->cliente->registrar_protocolo(array('id_cliente_fk'=>$fatura->id_cliente,'titulo'=>'Protocolo Automatico','descricao'=>$protocolo));
                    $this->cliente->alterar($fatura->id_cliente,array('credito'=>$att_cliente));
                }

				if($fatura->id_cliente)
				$lista = [843, 848, 849, 855, 853, 852, 845, 854, 840, 838, 844];

				if (!in_array($fatura->id_cliente, $lista)) {
					$id_fatura = $this->inserir_fatura($fatura);
					foreach ($fatura->itens as $i => $item_now):
						$this->inserir_fatura_item($item_now,$id_fatura);
					endforeach;
				}
            endforeach;
			exit;

            set_msg('Faturas Geradas com sucesso!','sucesso');
            redirect(current_url());
        }


        $this->parameters['pg_title'] = '<i class="fa fa-arrow-right"></i> Geração de Faturamento';
        $this->parameters['pg_subtitle'] = 'Selecione o dia e clique em enviar para gerar o faturamento para o dia especificado.';

        $this->parameters['menu'] = $this->load_menu('franquia');
        $dias_faturamento = array('15'=>'Dia 15','30'=>'Dia 30 (Ou Último dia do mes)');
        $this->parameters['content'] = $this->load->view('screens/franquia',array('content'=>'gerar_faturamento','id_franquia'=>$id_franquia,'dias'=>$dias_faturamento),true);
        $this->load->view('templates/maing', $this->parameters);
    }

    private function inserir_fatura($fatura){
        $dados = array();
        $dados['id_cliente_fk'] = $fatura->id_cliente;
        $dados['nome']          = $fatura->nome;
        $dados['inicio']        = $fatura->inicio;
        $dados['fim']           = $fatura->fim;
        $dados['tipo']          = $fatura->tipo;
        $dados['mensalidade']   = $fatura->mensalidade;
        $dados['franquia']      = $fatura->franquia;
        $dados['consumo']       = $fatura->consumo;
        $dados['valor']         = $fatura->valor;
        $dados['vencimento']    = $fatura->vencimento;
        $dados['credito']       = $fatura->credito;
        $dados['debito']        = $fatura->debito;

        $this->faturamento->inserir_fatura($dados);
        return $this->faturamento->id_ultima_fatura();
    }

    private function inserir_fatura_item($item,$id_fatura){
        $dados = array();
        $dados['id_fatura_fk']  = $id_fatura;
        $dados['id_cliente_fk'] = $item->id_cliente_fk;
        $dados['nome']          = $item->nome;
        $dados['descricao']     = $item->descricao;
        $dados['grupo']         = $item->grupo;
        $dados['valor']         = $item->valor;
        $dados['data']          = $item->data;

        $this->faturamento->inserir_fatura_item($dados);
    }

    public function clientes(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }

        $this->load->model('cliente_model','cliente');
        $clientes = $this->cliente->retornar_todos_ordenado_gerenciar_franquia($id_franquia)->result();
        $status = array('0'=>'Cancelados','1'=>'Ativos','2'=>'Bloqueados por Inadimplencia');

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Clientes de Franquia';
        $this->parameters['pg_subtitle'] = 'Gerenciamento de Clientes de Franquia';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/franquia',array('content'=>'clientes','clientes'=>$clientes,'status'=>$status),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function franquia_fatura(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }

        $total = 0;
        $inicio = '2022-03-05';
        $fim = '2022-04-05';
        $valor_cliente = 16.80;
        $this->load->model('faturamento_model','faturamento');
        $franquia = $this->faturamento->retornar_franquia_dados($id_franquia)->row();
        $consultas = $this->faturamento->retornar_faturamento_franquia_consultas_resumido($id_franquia, $inicio, $fim)->result();
        $veiculares = $this->faturamento->retornar_faturamento_franquia_consultas_veicular_resumido($id_franquia, $inicio, $fim)->result();
        $cartas = $this->faturamento->retornar_faturamento_franquia_cartas_resumido($id_franquia, $inicio, $fim)->result();
        $negativoces = $this->faturamento->retornar_faturamento_franquia_negativacoes_resumido($id_franquia, $inicio, $fim)->result();
        $qtd_clientes = 13;

        foreach($consultas as $i => $c): $total += $c->custo; endforeach;
        foreach($veiculares as $i => $c): $total += $c->custo; endforeach;
        foreach($cartas as $i => $c): $total += $c->custo; endforeach;
        foreach($negativoces as $i => $c): $total += $c->custo; endforeach;
        $total += ($qtd_clientes*$valor_cliente);

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/fatura_franquia_pdf',array('franquia'=>$franquia,'inicio'=>$inicio,'fim'=>$fim,'consultas'=>$consultas,'veiculares'=>$veiculares,'cartas'=>$cartas, 'negativacoes'=>$negativoces,'qtd_clientes'=>$qtd_clientes,'valor'=>$total,'valor_cliente'=>$valor_cliente),true);
        $pdf['titulo'] = 'Franquia Fatura RMC';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function relatorio_por_cliente(){
        $id_franquia = $this->uri->segment(3);
        $inicio = '2021-11-05 00:00:00';
        $fim = '2021-12-05 23:59:59';
        $this->load->model('cliente_model','cliente');
        $this->load->model('faturamento_model','faturamento');
        $franquia = $this->faturamento->retornar_franquia_dados($id_franquia)->row();
        $consultas = $this->cliente->consultas_por_cliente($id_franquia, $inicio, $fim)->result();
        $veiculares = $this->cliente->veiculares_por_cliente($id_franquia,$inicio,$fim)->result();
        $negativacoes = $this->cliente->negativacoes_por_cliente($id_franquia, $inicio, $fim)->result();
        $baixas = $this->cliente->baixas_por_cliente($id_franquia, $inicio, $fim)->result();
        $cartas = $this->cliente->cartas_por_cliente($id_franquia, $inicio, $fim)->result();
        $totais = array(
            'consultas'=>array('qtd'=>0,'custo'=>0,'venda'=>0,'lucro'=>0),
            'veiculares'=>array('qtd'=>0,'custo'=>0,'venda'=>0,'lucro'=>0),
            'negativacoes'=>array('qtd'=>0,'custo'=>0,'venda'=>0,'lucro'=>0),
            'cartas'=>array('qtd'=>0,'custo'=>0,'venda'=>0,'lucro'=>0),
            'baixas'=>array('qtd'=>0,'custo'=>0,'venda'=>0,'lucro'=>0));
        $total = array();

        foreach($consultas as $i => $c):
            $totais['consultas']['qtd'] += $c->qtd;
            $totais['consultas']['custo'] += $c->custo;
            if($c->consultor==0) $totais['consultas']['venda'] += $c->venda;
            if($c->consultor==0) $totais['consultas']['lucro'] += $c->venda - $c->custo; else $totais['consultas']['lucro'] -= $c->custo;
        endforeach;

        foreach($veiculares as $i => $c):
            $totais['veiculares']['qtd'] += $c->qtd;
            $totais['veiculares']['custo'] += $c->custo;
            if($c->consultor==0) $totais['veiculares']['venda'] += $c->venda;
            if($c->consultor==0) $totais['veiculares']['lucro'] += $c->venda - $c->custo; else $totais['veiculares']['lucro'] -= $c->custo;
        endforeach;

        foreach($negativacoes as $i => $c):
            $totais['negativacoes']['qtd'] += $c->qtd;
            $totais['negativacoes']['custo'] += $c->custo;
            if($c->consultor==0) $totais['negativacoes']['venda'] += $c->venda;
            if($c->consultor==0) $totais['negativacoes']['lucro'] += $c->venda - $c->custo; else $totais['consultas']['lucro'] -= $c->custo;
        endforeach;

        foreach($baixas as $i => $c):
            $totais['baixas']['qtd'] += $c->qtd;
            $totais['baixas']['custo'] += $c->custo;
            if($c->consultor==0) $totais['baixas']['venda'] += $c->venda;
            if($c->consultor==0) $totais['baixas']['lucro'] += $c->venda - $c->custo; else $totais['consultas']['lucro'] -= $c->custo;
        endforeach;

        foreach($cartas as $i => $c):
            $totais['cartas']['qtd'] += $c->qtd;
            $totais['cartas']['custo'] += $c->custo;
            if($c->consultor==0) $totais['cartas']['venda'] += $c->venda;
            if($c->consultor==0) $totais['cartas']['lucro'] += $c->venda - $c->custo; else $totais['consultas']['lucro'] -= $c->custo;
        endforeach;

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/detalhamento_franquia_pdf',array('inicio'=>$inicio,'fim'=>$fim,'franquia'=>$franquia,'consultas'=>$consultas,'veiculares'=>$veiculares,'negativacoes'=>$negativacoes,'baixas'=>$baixas,'cartas'=>$cartas,'totais'=>$totais,'total'=>$total),true);
        $pdf['titulo'] = 'Detalhamento Franquia RMC';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function gerar_fatura(){
        $id_franquia = $this->uri->segment(3);
        if($id_franquia==null){
            set_msg('Não foi informada uma franquia.');
            redirect('franquia');
        }
        $franquia = $this->franquia->retornar_franquia($id_franquia)->row();
        $faturas = $this->franquia->retornar_faturas($id_franquia)->result();

        $this->form_validation->set_rules('valor_individual', 'Valor Individual', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->model('faturamento_model','faturamento');
            $total = 0;
            $itens = array();
            $inicio = data_db($this->input->post('data_inicial'), false);
            $fim = data_db($this->input->post('data_final'), false);
            $valor_cliente = only_numbers($this->input->post('valor_individual'))/100;

            $franquia = $this->faturamento->retornar_franquia_dados($id_franquia)->row();

            $consultas = $this->faturamento->retornar_faturamento_franquia_consultas_resumido($id_franquia, $inicio, $fim)->result();
			$consultas_novas = $this->faturamento->retornar_faturamento_franquia_consultas_novas_resumido($id_franquia, $inicio, $fim)->result();
            $veiculares = $this->faturamento->retornar_faturamento_franquia_consultas_veicular_resumido($id_franquia, $inicio, $fim)->result();
            $cartas = $this->faturamento->retornar_faturamento_franquia_cartas_resumido($id_franquia, $inicio, $fim)->result();
            $negativoces = $this->faturamento->retornar_faturamento_franquia_negativacoes_resumido($id_franquia, $inicio, $fim)->result();
			$baixas = $this->faturamento->retornar_faturamento_franquia_baixas_resumido($id_franquia, $inicio, $fim)->result();
            $qtd_clientes = $this->franquia->franquia_qtd_clientes($id_franquia, $fim);

            foreach($consultas as $i => $c): $total += $c->custo; $itens = $this->add_item_array($itens, $c, "consultas"); endforeach;
            foreach($consultas_novas as $i => $cn): $total += $cn->custo; $itens = $this->add_item_array($itens, $cn, "consultas"); endforeach;
            foreach($veiculares as $i => $c): $total += $c->custo; $itens = $this->add_item_array($itens, $c, "veicular"); endforeach;
            foreach($cartas as $i => $c): $total += $c->custo; $itens = $this->add_item_array($itens, $c, "cartas"); endforeach;
            foreach($negativoces as $i => $c):
				//$total += $c->custo;
				$total += ($c->und * $c->qtd);
				$itens = $this->add_item_array($itens, $c, "negativacoes");
			endforeach;
            foreach($baixas as $i => $c): $total += $c->custo; $itens = $this->add_item_array($itens, $c, "baixas"); endforeach;

            $dados_fatura = array();
            $dados_fatura['id_franquia_fk'] = $id_franquia;
            $dados_fatura['nome'] = 'Mensalidade Referente a '.ucwords(get_mes(date('m',strtotime($inicio)))).' de '.date('Y');
            $dados_fatura['inicio'] = $inicio;
            $dados_fatura['fim'] = $fim;
            $dados_fatura['clientes_qtd'] = $qtd_clientes;
            $dados_fatura['clientes_valor'] = $qtd_clientes * $valor_cliente;
            $dados_fatura['valor'] = $total + $dados_fatura['clientes_valor'];
            $dados_fatura['vencimento'] = date('Y-m').'-20';

            /*
            foreach($itens as $i => $it):
                echo '['.$it['grupo'].']'.$it['nome'].'<br>';
            endforeach;
            */

            $this->franquia->inserir_adm_franquia_fatura($dados_fatura);
            $id_fatura = $this->franquia->id_ultima_fatura();

            foreach($itens as $i => $item):
                $item['id_fatura_fk'] = $id_fatura;
                $this->franquia->inserir_fatura_item($item);
            endforeach;

            set_msg("Fatura criada com sucesso!","successo");
            redirect(current_url());
        }

        $this->parameters['pg_title'] = '<i class="fa fa-file"></i> Franquia - Gerar Fatura';
        $this->parameters['pg_subtitle'] = 'Geração de Fatura para Cobrança';

        $this->parameters['menu'] = $this->load_menu('franquia');
        $this->parameters['content'] = $this->load->view('screens/franquia', array('content' => 'gerar_fatura', 'id_franquia'=>$id_franquia, 'franquia' => $franquia, 'faturas' => $faturas), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function fatura_visualizar(){
        $id_fatura = $this->uri->segment(3);
        if($id_fatura==null){
            set_msg('Não foi informada uma Fatura.');
            redirect('franquia');
        }

        $this->load->model('faturamento_model','faturamento');

        $fatura = $this->franquia->retornar_fatura($id_fatura)->row();
        $franquia = $this->faturamento->retornar_franquia_dados($fatura->id_franquia_fk)->row();
        $fatura_itens = $this->franquia->retornar_fatura_itens($id_fatura)->result();

        $consultas = array();
        $veiculares = array();
        $negativacoes = array();
        $cartas = array();
        $baixas = array();

        foreach($fatura_itens as $i => $item):
            switch($item->grupo):
                case 'consultas': array_push($consultas, $item); break;
                case 'veicular': array_push($veiculares, $item); break;
                case 'negativacoes': array_push($negativacoes, $item); break;
                case 'cartas': array_push($cartas, $item); break;
                case 'baixas': array_push($baixas, $item); break;
            endswitch;
        endforeach;

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/fatura_franquia_pdf',array('franquia'=>$franquia,'fatura'=>$fatura,'itens'=>$fatura_itens,
            'consultas'=>$consultas,'veiculares'=>$veiculares,'negativacoes'=>$negativacoes,'baixas'=>$baixas,'cartas'=>$cartas),true);
        $pdf['titulo'] = 'Franquia Fatura RMC';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    private function add_item_array($arr, $c, $grupo){
        $item = array();
        $item['grupo'] = $grupo;
        $item['nome'] = $c->nome;
        $item['qtd'] = $c->qtd;
        $item['und'] = $c->und;
		if($grupo=="negativacoes"){
        	$item['total'] = ($c->qtd * $c->und);
		}else{
        	$item['total'] = $c->custo;
		}
        array_push($arr, $item);
        return $arr;
    }
}
