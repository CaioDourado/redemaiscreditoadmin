<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operacao extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->guard_admin();
        $this->load->model('operacao_model', 'operacao');
        $this->parameters['title'] = 'Operacao';
        $this->parameters['title_window'] = 'Operacao';
        $this->parameters['menu'] = $this->load_menu('padrao_novo');
        array_push($this->parameters['breadcrumb'], array('operacao', 'Operacao'));
    }

    public function index(){
        $inicio = $this->input->get('inicio');
        $fim = $this->input->get('fim');

        if($inicio == null) $inicio = date('Y-m-d', strtotime('-7 days'));
        if($fim == null) $fim = date('Y-m-d');

        $filtros = array(
            'inicio' => $inicio,
            'fim' => $fim,
            'slug' => $this->input->get('slug'),
            'fornecedor' => $this->input->get('fornecedor'),
            'tipo_erro' => $this->input->get('tipo_erro')
        );

        $this->parameters['pg_title'] = '<i class="fa fa-heartbeat"></i> Operacao';
        $this->parameters['pg_subtitle'] = 'Saude dos fornecedores, tentativas e tempos das consultas.';
        $this->parameters['content'] = $this->load->view('screens/operacao', array(
            'content' => 'index',
            'filtros' => $filtros,
            'resumo' => $this->operacao->resumo_metricas($filtros)->result(),
            'erros' => $this->operacao->resumo_erros($filtros)->result(),
            'ultimas' => $this->operacao->ultimas_tentativas($filtros, 100)->result(),
            'slugs' => $this->operacao->slugs_metricas()->result(),
            'fornecedores' => $this->operacao->fornecedores_metricas()->result()
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    private function guard_admin(){
        if($this->session->userdata('logado') == null){
            set_msg('Sessao expirada. Entre novamente.');
            redirect('inicio');
        }
    }
}
