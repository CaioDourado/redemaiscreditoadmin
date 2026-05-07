<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pesquisa extends ControllerAuth {

    public function __construct(){
        parent::__construct();
        $this->load->model('pesquisa_model','pesquisa');
        $this->parameters['title'] = 'Pesquisa';
        $this->parameters['title_window'] = 'Pesquisa de Cliente';
        $this->load_menu('padrao_novo');
        array_push($this->parameters['breadcrumb'],array('cliente','Pesquisa'));
    }

    public function index(){
        $retorno = null;
        $pesquisa = '';

        if(isset($_GET)){
            $pesquisa = $this->input->get('pesquisa');
            $retorno = $this->pesquisa->by_name_cpf_cnpj($pesquisa);
            if($retorno!=null) $retorno = $retorno->result();
        }

        $this->parameters['pg_title'] = '<i class="fa fa-search"></i> Pesquisa';
        $this->parameters['pg_subtitle'] = 'Pesquisa de Clientes pelo Nome, CPF ou CNPJ.';

        $this->parameters['content'] = $this->load->view('screens/pesquisa', array('content' => 'index', 'retorno' => $retorno, 'pesquisa' => $pesquisa), true);
        $this->load->view('templates/maing', $this->parameters);
    }
}