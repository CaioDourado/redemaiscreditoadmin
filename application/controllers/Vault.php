<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vault extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->load->model('vault_model','vault');
        $this->parameters['title'] = 'Vault';
        $this->parameters['title_window'] = 'Vault';
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'padrao_novo'),true);
        array_push($this->parameters['breadcrumb'],array('vault','Vault'));
    }

    public function index(){
        $hashs = $this->vault->retornar_todos()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-exclamation-triangle"></i> Vault';
        $this->parameters['pg_subtitle'] = 'Criação de Ambiente Seguro';

        $this->parameters['content'] = $this->load->view('screens/vault',array('content'=>'index','hashs'=>$hashs),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function gerar_codigo(){
        $vn = $this->vault->get_vault_now()->row();
        if($vn==null){
            $dados = array();
            $dados['data'] = date('Y-m-d');
            $senha = random_int(100000, 999999);
            $dados['hash'] = md5($senha);
            $this->vault->inserir($dados);
            set_msg("Vault Criado com a senha: ".$senha,'sucesso');
        }else{
            set_msg("Já Existe um Vault em Vigência.");
        }
        redirect('vault');
    }
}