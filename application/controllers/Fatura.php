<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fatura extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('fatura_model', 'fatura');
        $this->parameters['title'] = 'fatura';
        $this->parameters['title_window'] = 'Fatura';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'fatura'), true);
        array_push($this->parameters['breadcrumb'], array('fatura', 'Fatura'));
    }

    public function Index(){
        $faturas = $this->fatura->retornar_todos_mes()->result();
        $faturas_adm = $this->fatura->retornar_adm_franquia_todos_mes()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-file-text"></i> Faturas';
        $this->parameters['pg_subtitle'] = 'Tela com as faturas para geraÃ§Ã£o de boletos.';

        $this->parameters['menu'] = $this->load_menu('faturas');
        $this->parameters['content'] = $this->load->view('screens/fatura',array('content'=>'gerenciar','faturas'=>$faturas,'faturas_adm'=>$faturas_adm),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function alterar(){
        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada um fatura para alteraÃ§Ã£o','fatura');
        $fatura = $this->fatura->retornar($id_fatura)->row();
    }

    public function visualizar(){
        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada um fatura para alteraÃ§Ã£o','fatura');
        $fatura = $this->fatura->retornar($id_fatura)->row();
    }

    public function pdf_resumo(){
        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada um fatura para alteraÃ§Ã£o','fatura');
        $this->load->model('cliente_model','cliente');
        $fatura = $this->fatura->retornar($id_fatura)->row();
        $cliente = $this->cliente->retornar($fatura->id_cliente_fk)->row();
        $fatura_itens = $this->fatura->retornar_itens_group($id_fatura)->result();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/fatura_pdf_resumo',array('fatura'=>$fatura,'itens'=>$fatura_itens,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function pdf(){
        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada um fatura para alteraÃ§Ã£o','fatura');
        $this->load->model('cliente_model','cliente');
        $fatura = $this->fatura->retornar($id_fatura)->row();
        $cliente = $this->cliente->retornar($fatura->id_cliente_fk)->row();
        $fatura_itens = $this->fatura->retornar_itens($id_fatura)->result();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/fatura_pdf',array('fatura'=>$fatura,'itens'=>$fatura_itens,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function pdf2(){
        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada um fatura para alteraÃ§Ã£o','fatura');
        $this->load->model('cliente_model','cliente');
        $fatura = $this->fatura->retornar($id_fatura)->row();
        $cliente = $this->cliente->retornar($fatura->id_cliente_fk)->row();
        $fatura_itens = $this->fatura->retornar_itens($id_fatura)->result();

        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/faturav2_pdf',array('fatura'=>$fatura,'itens'=>$fatura_itens,'cliente'=>$cliente),true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function pdf_aux(){
        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/fatura_pdf_aux', null ,true);
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }

    public function fatura_excel(){
        $pdf = array();
        $pdf['titulo'] = 'Boleto - Rede Mais Credito';
        $pdf['senha'] = null;
        $this->load->view('components/fatura_excel',$pdf);
    }

    public function gerar_boleto(){
		//ini_set('display_errors', '1');
		//ini_set('display_startup_errors', '1');
		//error_reporting(E_ALL);

        $id_fatura = $this->verificar_parametro(3,'NÃ£o foi informada uma fatura vÃ¡lida','fatura');
        $fatura = $this->fatura->retornar($id_fatura)->row();

        $id_cliente = $fatura->id_cliente_fk;
        $vencimento = $fatura->vencimento;
        $valor = $fatura->valor;

        $outros = array();
        $outros['descricao_boleto'] = $fatura->nome;

		$this->load->model('boletoV3_model','boletov3');
		$resultado = $this->boletov3->newBoletoResult($id_cliente, $valor, $vencimento, $outros, $this->session->userdata('id'));
		if($resultado['success']){
			$this->fatura->alterar($fatura->id_fatura,array('faturado'=>1,'id_boleto_fk'=>$resultado['id_boleto'],'hash_boleto'=>$resultado['hash']));
			redirect('fatura');
		}else{
			set_msg('Ocorreu um erro ao gerar o boleto: '.$resultado['mensagem']);
		}

		/*
        $this->load->model('boleto_model','boleto');
        if($this->boleto->criar($id_cliente,$valor,$vencimento,$outros)){
            $ultimo_boleto = $this->boleto->retornar_ultimo_boleto()->row();
            $this->fatura->alterar($fatura->id_fatura,array('faturado'=>1,'id_boleto_fk'=>$ultimo_boleto->id_boleto,'hash_boleto'=>$ultimo_boleto->hash));
            redirect('fatura');
        }else{
            set_msg('Ocorreu um erro ao gerar o boleto.');
        }
		*/
    }
}

