<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faturamento extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('faturamento_model', 'faturamento');
        $this->parameters['title'] = 'Faturamento';
        $this->parameters['title_window'] = 'Faturamento';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'faturamento'), true);
        array_push($this->parameters['breadcrumb'], array('faturamento', 'Faturamento'));
    }

    public function gerar_faturamento(){

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

        $this->form_validation->set_rules('dia_vcto', 'Dia de Vencimento', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->model('cliente_model','cliente');

            $dia_start = array('15'=>'5','30'=>'20');
            $dia_vcto = $this->input->post('dia_vcto');
            $dia_fat = $dia_start[$dia_vcto];

            $inicio = date('Y-m-d',strtotime(date('Y-m').'-'.$dia_fat.'-1 month')).' 00:00:00';
            $fim = date('Y-m').'-'.$dia_fat.' 23:59:59';

            $clientes = $this->faturamento->retornar_clientes_faturamento($dia_vcto)->result();
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

            $ids_franquia = array();
            foreach($faturamento as $index => $linha):
                if(array_key_exists($linha->id_cliente_fk,$faturas)){
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
                }
            endforeach;

            foreach($faturas as $index => $fatura):
                // Verifica se o consumo foi maior que a franquia e adiciona o valor do consumo ao valor da fatura
				if($fatura->tipo==="Completa"){
                	if($fatura->consumo>$fatura->franquia) $fatura->valor = money($fatura->valor+($fatura->consumo-$fatura->franquia));
				}else{
					if((float)$fatura->consumo>(float)$fatura->franquia) {
						echo 'Consumo Maior<br>';
						$fatura->valor = $fatura->mensalidade + abs($fatura->consumo - $fatura->franquia);
					}else{
						$fatura->valor = $fatura->mensalidade;
					}
				}

				if($fatura->tipo==="Pró-rata"){
					echo $fatura->id_cliente.' | '.$fatura->valor.' | '.$fatura->consumo.' | '.$fatura->franquia.' | '.$fatura->mensalidade.'<br>';
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

                $id_fatura = $this->inserir_fatura($fatura);
                foreach($fatura->itens as $i => $item_now):
                   $this->inserir_fatura_item($item_now,$id_fatura);
                endforeach;
            endforeach;

            set_msg('Faturas Geradas com sucesso!','sucesso');
            redirect(current_url());
        }


        $dias_faturamento = array('15'=>'Dia 15','30'=>'Dia 30 (Ou Último dia do mes)');
        $this->parameters['content'] = $this->load->view('screens/faturamento',array('content'=>'gerar_faturamento','dias'=>$dias_faturamento),true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    public function gerar_faturamento_novo(){
        $this->load->model('cliente_model','cliente');

        $dia_start = array('15'=>'5','30'=>'20');
        $dia_vcto = 15;
        $dia_fat = $dia_start[$dia_vcto];

        $inicio = date('Y-m-d',strtotime(date('Y-m').'-'.$dia_fat.'-1 month')).' 00:00:00';
        $fim = date('Y-m').'-'.$dia_fat.' 23:59:59';

        $clientes = $this->faturamento->retornar_clientes_faturamento($dia_vcto)->result();
        $faturamento = $this->faturamento->retornar_gerar_faturamento_consultas($inicio,$fim,$dia_vcto)->result();

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

        var_dump($faturas);
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

    public function index(){
        $dia_faturamento = $this->uri->segment(3);
        if($dia_faturamento==null) $dia_faturamento = 20;

        $faturamento_consultas = $this->faturamento->retornar_faturamento_consultas_atual($dia_faturamento)->result();
        $faturamento_negativacoes = $this->faturamento->retornar_faturamento_negativacoes_atual($dia_faturamento)->result();
        $faturamento_negativacoes_baixa = $this->faturamento->retornar_faturamento_negativacoes_baixa_atual($dia_faturamento)->result();
        $faturamento_cartas = $this->faturamento->retornar_faturamento_carta_atual($dia_faturamento)->result();

        $faturamento_geral = array();
        foreach($faturamento_consultas as $index => $fat_consulta):
            if(!isset($faturamento_geral[$fat_consulta->id_cliente_fk])){
                $faturamento_geral[$fat_consulta->id_cliente_fk] = array();
                $faturamento_geral[$fat_consulta->id_cliente_fk]['nome'] = $fat_consulta->nome_ou_fantasia;
                $faturamento_geral[$fat_consulta->id_cliente_fk]['razao_social'] = $fat_consulta->razao_social;
                $faturamento_geral[$fat_consulta->id_cliente_fk]['mensalidade'] = $fat_consulta->mensalidade;
                $faturamento_geral[$fat_consulta->id_cliente_fk]['franquia'] = $fat_consulta->franquia;
                $faturamento_geral[$fat_consulta->id_cliente_fk]['valor'] = 0;
                $faturamento_geral[$fat_consulta->id_cliente_fk]['custo'] = 0;
            }

            $faturamento_geral[$fat_consulta->id_cliente_fk]['valor'] += $fat_consulta->valor;
            $faturamento_geral[$fat_consulta->id_cliente_fk]['custo'] += $fat_consulta->custo;
        endforeach;
        foreach($faturamento_negativacoes as $index => $item):
            if(!isset($faturamento_geral[$item->id_cliente_fk])){
                $faturamento_geral[$item->id_cliente_fk] = array();
                $faturamento_geral[$item->id_cliente_fk]['nome'] = $item->nome_ou_fantasia;
                $faturamento_geral[$item->id_cliente_fk]['razao_social'] = $item->razao_social;
                $faturamento_geral[$item->id_cliente_fk]['mensalidade'] = $item->mensalidade;
                $faturamento_geral[$item->id_cliente_fk]['franquia'] = $item->franquia;
                $faturamento_geral[$item->id_cliente_fk]['valor'] = 0;
                $faturamento_geral[$item->id_cliente_fk]['custo'] = 0;
            }

            $faturamento_geral[$item->id_cliente_fk]['valor'] += $item->valor;
            $faturamento_geral[$item->id_cliente_fk]['custo'] += $item->custo;
        endforeach;
        foreach($faturamento_negativacoes_baixa as $index => $item):
            if(!isset($faturamento_geral[$item->id_cliente_fk])){
                $faturamento_geral[$item->id_cliente_fk] = array();
                $faturamento_geral[$item->id_cliente_fk]['nome'] = $item->nome_ou_fantasia;
                $faturamento_geral[$item->id_cliente_fk]['razao_social'] = $item->razao_social;
                $faturamento_geral[$item->id_cliente_fk]['mensalidade'] = $item->mensalidade;
                $faturamento_geral[$item->id_cliente_fk]['franquia'] = $item->franquia;
                $faturamento_geral[$item->id_cliente_fk]['valor'] = 0;
                $faturamento_geral[$item->id_cliente_fk]['custo'] = 0;
            }

            $faturamento_geral[$item->id_cliente_fk]['valor'] += $item->valor;
            $faturamento_geral[$item->id_cliente_fk]['custo'] += $item->custo;
        endforeach;
        foreach($faturamento_cartas as $index => $item):
            if(!isset($faturamento_geral[$item->id_cliente_fk])){
                $faturamento_geral[$item->id_cliente_fk] = array();
                $faturamento_geral[$item->id_cliente_fk]['nome'] = $item->nome_ou_fantasia;
                $faturamento_geral[$item->id_cliente_fk]['razao_social'] = $item->razao_social;
                $faturamento_geral[$item->id_cliente_fk]['mensalidade'] = $item->mensalidade;
                $faturamento_geral[$item->id_cliente_fk]['franquia'] = $item->franquia;
                $faturamento_geral[$item->id_cliente_fk]['valor'] = 0;
                $faturamento_geral[$item->id_cliente_fk]['custo'] = 0;
            }

            $faturamento_geral[$item->id_cliente_fk]['valor'] += $item->valor;
            $faturamento_geral[$item->id_cliente_fk]['custo'] += $item->custo;
        endforeach;

        foreach($faturamento_geral as $index => $item):
            if($index<10){
                unset($faturamento_geral[$index]);
            }else{
                $faturamento_geral[$index]['valor_final'] = $item['mensalidade'];
                if($item['valor']>$item['franquia']) $faturamento_geral[$index]['valor_final'] = $item['mensalidade']+($item['valor']-$item['franquia']);
            }
        endforeach;

        $this->parameters['content'] = $this->load->view('screens/faturamento',
            array(
                'content' => 'atual',
                'faturamento_consultas' => $faturamento_consultas,
                'faturamento_negativacoes' => $faturamento_negativacoes,
                'faturamento_negativacoes_baixa' => $faturamento_negativacoes_baixa,
                'faturamento_cartas' => $faturamento_cartas,
                'faturamento_geral' => $faturamento_geral
            )
        , true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    public function parcial(){
        $this->load->model('cliente_model','cliente');

        $clientes = $this->cliente->retornar_todos_ordenado()->result();

        $this->parameters['content'] = $this->load->view('screens/faturamento',array('content'=>'parcial','clientes'=>$clientes),true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    public function geracao(){
        $id_cliente = $this->uri->segment(3);

        $this->load->model('cliente_model','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();

        $this->parameters['content'] = $this->load->view('screens/faturamento',array('content'=>'geracao','cliente'=>$cliente),true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }
}
