<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

class Boleto extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('boleto_model', 'boleto');
        $this->parameters['title'] = 'Boleto';
        $this->parameters['title_window'] = 'Boleto';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'boletos'), true);
        array_push($this->parameters['breadcrumb'], array('boleto', 'Boletos'));
    }

    public function Index(){
        $boletos = $this->boleto->retornar_por_mes()->result();

        $this->parameters['menu'] = $this->load_menu('boletos');
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'gerenciar', 'boletos' => $boletos), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function mes(){
        $ano = $this->uri->segment(3);
        $mes = $this->uri->segment(4);

        $boletos = $this->boleto->retornar_de_mes($ano,$mes)->result();

        $retorno = array('apagar'=>array(),'pagos'=>array(),'vencidos'=>array(),'cancelados'=>array());

        foreach ($boletos as $index => $boleto) {
            if($boleto->pago==1){
                array_push($retorno['pagos'],$boleto);
            }else{
                if($boleto->data_vencimento>date('Y-m-d')){
                    array_push($retorno['apagar'],$boleto);
                }else{
                    array_push($retorno['vencidos'],$boleto);
                }
            }
        }

        $this->parameters['menu'] = $this->load_menu('boletos');
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'mes', 'boletos' => $retorno), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function geral(){
        $boletos = $this->boleto->retornar_ultimos(30)->result();

        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'gerenciar', 'boletos' => $boletos), true);
        $this->load->view('templates/main_new', $this->parameters);
    }
    
    public function pagos(){
        $qtd_meses = 3;
        $fim = date('Y-m-d');
        $inicio = date('Y-m-d',strtotime($fim.' -'.$qtd_meses.' months'));
        $boletos = $this->boleto->retornar_pagos($inicio,$fim)->result();

        $retorno = array();
        $retorno_mes = array();
        foreach($boletos as $index => $boleto):
            $dia = str_replace("-","_",$boleto->data_pagamento);
            $mes = str_replace("-","_",date('m-Y',strtotime($boleto->data_pagamento)));
            if(!isset($retorno[$dia])) $retorno[$dia] = array('total'=>0,'boletos'=>array());
            if(!isset($retorno_mes[$mes])) $retorno_mes[$mes] = array('valor'=>0,'qtd'=>0);

            $retorno[$dia]['total'] += $boleto->valor_pago;
            array_push($retorno[$dia]['boletos'],$boleto);

            $retorno_mes[$mes]['valor'] += $boleto->valor_pago;
            $retorno_mes[$mes]['qtd']++;
        endforeach;

        $this->parameters['menu'] = $this->load_menu('boletos');
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'pagos', 'dias' => $retorno, 'meses'=>$retorno_mes), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function cadastrar(){

        $this->form_validation->set_rules('id_cliente_fk', 'Cliente', 'required');
        $this->form_validation->set_rules('boleto_descricao', 'Descrição', 'required');
        $this->form_validation->set_rules('data_vencimento', 'Vencimento', 'required');
        $this->form_validation->set_rules('valor_boleto', 'Valor do Boleto', 'required');

        if($this->form_validation->run()==TRUE) {
            $id_cliente = $this->input->post('id_cliente_fk');
            $vencimento = data_db($this->input->post('data_vencimento'),false);
            $valor = only_numbers($this->input->post('valor_boleto'))/100;

            $outros = array();
            $outros['descricao_boleto'] = $this->input->post('boleto_descricao');

			$this->load->model('boletoV3_model','boletov3');
			if($this->boletov3->newBoleto($id_cliente, $valor, $vencimento, $outros)){
            //if($this->boleto->criar($id_cliente,$valor,$vencimento,$outros)){
                $ultimo_boleto = $this->boleto->retornar_ultimo_boleto()->row();
                redirect('boleto/visualizar/'.$ultimo_boleto->hash);
            }else{
                set_msg('Ocorreu um erro ao gerar o boleto.');
            }
        }

        $this->load->model('cliente_model','cliente');
        $clientes = $this->cliente->retornar_todos_array();
        $this->parameters['menu'] = $this->load_menu('boletos');
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'cadastrar','clientes'=>$clientes), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function geracao_em_massa(){
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'geracao_em_massa'), true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    public function envio_por_email(){
        $boletos = $this->boleto->retornar_ultimos(150)->result();

        $retorno = array('apagar'=>array(),'pagos'=>array(),'vencidos'=>array(),'cancelados'=>array());

        foreach ($boletos as $index => $boleto) {
            if($boleto->pago==1){
                array_push($retorno['pagos'],$boleto);
            }else{
                if($boleto->data_vencimento>date('Y-m-d')){
                    array_push($retorno['apagar'],$boleto);
                }else{
                    array_push($retorno['vencidos'],$boleto);
                }
            }
        }

		$this->parameters['menu'] = $this->load_menu('boletos');
		$this->parameters['pg_title'] = '<i class="fa fa-envelope"></i> Envio de E-mails';
		$this->parameters['pg_subtitle'] = 'Envio geral de boletos por e-mail';
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'envio_por_email', 'boletos' => $retorno), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function visualizar(){
        $hash = $this->verificar_parametro(3,'Não foi informado ','boleto');

        $boleto = $this->boleto->retornar_hash($hash)->row();
        $cliente = $this->boleto->retornar_cliente($boleto->id_cliente_fk)->row();
        $conta = $this->boleto->retornar_conta($boleto->id_conta_banco)->row();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/boleto',array('boleto'=>$boleto,'conta'=>$conta,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function enviar_email(){
        $hash = $this->verificar_parametro(3,'Não foi informado ','boleto');
        $this->load->model('cliente_model','cliente');

        $boleto = $this->boleto->retornar_hash($hash)->row();
        $conta = $this->boleto->retornar_conta($boleto->id_conta_banco)->row();
        $cliente= $this->cliente->retornar($boleto->id_cliente_fk)->row();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/boleto',array('boleto'=>$boleto,'conta'=>$conta,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['nome_arquivo'] = $hash;
        $pdf['senha'] = null;
        $this->load->view('components/pdf_save',$pdf);

        //$this->load->helper('phpmailer');
		$this->load->helper('phpmail');

        $from = 'boletos@redemaiscredito.com.br';
        $nome = 'Rede Mais Crédito';
        $to = $cliente->email;
        //$cc = array('gigiomangia@hotmail.com','caiof.dourado@gmail.com');
		//$to = "caiof.dourado@gmail.com";
        $cc = array('gigiomangia@hotmail.com');

        $assunto = 'Boleto Rede Mais Crédito';
        $corpo = 'Prezado Cliente,<br><br>Segue em anexo o seu boleto da Rede Mais Crédito.';

        $anexo = array(
            array('caminho'=>FCPATH.'tmp/'.$hash.'.pdf','nome'=>'Boleto Rede Mais Crédito')
        );

        $retorno_email = enviar_email($from,$to,$assunto,$corpo,$nome,$cc,$anexo);
        unlink(FCPATH.'tmp/'.$hash.'.pdf');

        if($retorno_email['status']==='ok'){
            set_msg('E-mail Enviado com sucesso!.','sucesso');
        }else{
            set_msg('Ocorreu um erro ao enviar o E-mail. ['.$retorno_email['retorno'].']');
        }
        redirect('boleto');
    }

    public function enviar_email_ajax(){
        $hash = $this->verificar_parametro(3,'Não foi informado ','boleto');
        $this->load->model('cliente_model','cliente');

        $boleto = $this->boleto->retornar_hash($hash)->row();
        $conta = $this->boleto->retornar_conta($boleto->id_conta_banco)->row();
        $cliente= $this->cliente->retornar($boleto->id_cliente_fk)->row();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/boleto',array('boleto'=>$boleto,'conta'=>$conta,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['nome_arquivo'] = $hash;
        $pdf['senha'] = null;
        $this->load->view('components/pdf_save',$pdf);

        //$this->load->helper('phpmail');
		$this->load->helper('phpmail');

        //$from = 'boletos@redemaiscredito.com.br';
		$from = 'boleto@redemaiscredito.com.br';
        $nome = 'Rede Mais Crédito';
        $to = $cliente->email;
		//$to = "caiof.dourado@gmail.com";
		//$cc = array('gigiomangia@hotmail.com');
		$cc = null;

        $assunto = 'Boleto Rede Mais Crédito';
        $corpo = 'Prezado Cliente,<br><br>Segue em anexo o seu boleto da Rede Mais Crédito.';

        $anexo = array(
            array('caminho'=>FCPATH.'tmp/'.$hash.'.pdf','nome'=>'Boleto Rede Mais Crédito')
        );

		$retorno_email = enviar_email($from,$to,$assunto,$corpo,$nome,$cc,$anexo);

		//$retorno_email = enviar_email_sendgrid($to, $assunto, $corpo, $anexo);
        unlink(FCPATH.'tmp/'.$hash.'.pdf');

        if($retorno_email['status'] === 'ok')
            echo json_encode(array('status'=>'sucesso','msg'=>'E-mail Enviado com sucesso!'));
        else
            echo json_encode(array('status'=>'erro','msg'=>'Ocorreu um erro ao enviar o E-mail.'));
        exit;

    }

	public function remessa(){
		$data = date('Y-m-d');

		if($_GET!=null){
			$data = data_db($_GET['data'],false);
		}

		$boletos = $this->boleto->by_data($data)->result();

		if($_POST!=null){
			$this->load->model('remessa2_model','remessa');
			$this->load->model('sistema_model','sistema');
			$conta = $this->boleto->retornar_conta(1)->row();
			$remessa = $this->remessa->iniciar($conta);

			$id_boletos = $_POST['id_boletos'];
			$qtd = 1;
			foreach($id_boletos as $i):
				$boleto = $this->boleto->retornar($i)->row();
				$remessa->ecrever_lote_de_boleto($boleto, $qtd);
				$qtd++;
			endforeach;
			$remessa->baixar();
		}

		$this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'remessa', 'boletos' => $boletos), true);
		$this->load->view('templates/maing', $this->parameters);
	}

    public function baixar_remessa_hoje(){
        $this->load->helper('download');
        $this->load->model('remessa_model','remessa');
        $this->remessa->escrever_trailler();
        $hoje = date('Y-m-d');
        //$hoje = '2019-11-24';
        $arquivo = base_url().'remessas/remessa_seg_rastreadores_'.str_replace('-','_',$hoje).'.REM';
        force_download('remessa_'.date('dmY'), $arquivo);
    }

    public function retorno(){
        $this->form_validation->set_rules('confirm', 'Confirmação de Retorno', 'required');

        $retorno = null;
        if($this->form_validation->run()==TRUE) {
            if($_FILES['arquivo']['tmp_name']!=NULL) {
                $arquivo = fopen($_FILES['arquivo']['tmp_name'], 'rb');
                $retorno = array();

                // Leitura das duas primeiras linhas que são o Header do arquivo
                fgets($arquivo);fgets($arquivo);

                while(!feof($arquivo)):
                    $linha1 = fgets($arquivo);
                    $tipo_retorno = substr($linha1,15,2);
                    if($tipo_retorno!=" "&&$tipo_retorno!="  "&&$tipo_retorno!=false){
                        $linha2 = fgets($arquivo);
                        $boleto_atual = new stdClass();
                        $boleto_atual->codigo_retorno = substr($linha1,15,2);
                        $boleto_atual->nosso_numero = intval(substr($linha1,37,10));
                        $boleto_atual->id = intval(substr($linha1,58,15));
                        $boleto_atual->vencimento = data_8digitos(substr($linha1,73,8));
                        $boleto_atual->valor = intval(substr($linha1,81,15))/100;
                        $boleto_atual->cpf_cnpj = intval(substr($linha1,133,15));
                        $boleto_atual->nome = substr($linha1,148,40);
                        $boleto_atual->acrescimos = intval(substr($linha2,17,15))/100;
                        $boleto_atual->valor_pago = intval(substr($linha2,77,15))/100;
                        $boleto_atual->valor_liquido = intval(substr($linha2,92,15))/100;
                        $boleto_atual->data_ocorrencia = data_8digitos(substr($linha2,137,8));

                        if($this->atualizar_boleto_retorno($boleto_atual)){
                            array_push($retorno,$boleto_atual);
                        }
                    }
                endwhile;

                fclose($arquivo);
            }
        }

        $this->parameters['menu'] = $this->load_menu('boletos');
        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'retorno','retorno'=>$retorno), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function bloquear_inadimplentes(){

    }

    public function atualizar_boleto_retorno($boleto=null){
        if($boleto!=null){
            $dados = array();
            $dados['codigo_retorno'] = $boleto->codigo_retorno;
            if($boleto->valor_pago>0){
                $dados['pago'] = 1;
                $dados['valor_pago'] = $boleto->valor_pago;
                $dados['valor_liquido'] = $boleto->valor_liquido;
                $dados['data_pagamento'] = $boleto->data_ocorrencia;
            }else{
                $dados['baixado'] = 1;
                $dados['valor_pago'] = $boleto->valor_pago;
                $dados['valor_liquido'] = $boleto->valor_liquido;
                $dados['data_pagamento'] = $boleto->data_ocorrencia;
            }

            if($this->boleto->alterar($boleto->id,$dados)){
                return true;
            }else{
                return false;
            }
        }
    }

    public function baixar(){
        $hash = $this->verificar_parametro(3,'Não foi informado ','boleto');
        $boleto = $this->boleto->retornar_hash($hash)->row();

        $this->form_validation->set_rules('data_pagamento', 'Data do Pagamento', 'required');
        $this->form_validation->set_rules('valor_pago', 'Valor Pago', 'required|only_numbers');
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        if($this->form_validation->run()==TRUE) {
            if($this->input->post('senha')!=adm_env('BOLETO_BAIXA_PASSWORD', '')){
                set_msg('A senha gerencial esta incorreta.');
                redirect(current_url());
            }

            $dados = array();
            $dados['data_pagamento'] = data_db($this->input->post('data_pagamento'),false);
            $dados['valor_pago'] = $this->input->post('valor_pago')/100;
            $dados['pago'] = 1;

            if($this->boleto->alterar($boleto->id_boleto,$dados)){
                set_msg('Boleto Atualizado com sucesso!','sucesso');
            }else{
                set_msg('Ocorreu um erro ao atualizar o Boleto.');
            }
            redirect(current_url());
        }

        $this->parameters['content'] = $this->load->view('screens/boleto', array('content' => 'baixar', 'boleto' => $boleto), true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    /*
    public function teste(){
        $this->load->helper('phpmailer');
        $from = 'redemaiscredito@gmail.com';
        $nome = 'Rede Mais Crédito';
        $to = 'caiof.dourado@gmail.com';

        $assunto = 'Boleto Rede Mais Crédito';
        $corpo = 'Prezado Cliente,<br><br>Segue em anexo o seu boleto da Rede Mais Crédito.';
        $retorno_email = enviar_email($from,$to,$assunto,$corpo,$nome);
        var_dump($retorno_email);
        exit;
    }
    */

	public function teste_mail(){
		$this->load->helper('phpmail');
		enviar_email();
	}
}
