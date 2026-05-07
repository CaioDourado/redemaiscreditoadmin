<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Arquivo extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('arquivo_model', 'arquivo');
        $this->parameters['title'] = 'Arquivos';
        $this->parameters['title_window'] = 'Arquivo';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'arquivo'), true);
        array_push($this->parameters['breadcrumb'], array('arquivo', 'Arquivos'));
    }

    public function index(){
        $arquivos = $this->arquivo->retornar_todos()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-file"></i> Arquivos';
        $this->parameters['pg_subtitle'] = 'Visualize e baixe os arquivos do sistema.';

        $this->parameters['menu'] = $this->load_menu('arquivo');
        $this->parameters['content'] = $this->load->view('screens/arquivo',array('content'=>'index','arquivos'=>$arquivos),true);
        $this->load->view('templates/maing',$this->parameters);
    }
}