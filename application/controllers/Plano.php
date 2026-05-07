<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Plano extends ControllerAuth {

    public function __construct(){
        parent::__construct();
        $this->load->model('plano_model','plano');
        $this->parameters['title'] = 'Plano';
        $this->parameters['title_window'] = 'Plano';
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'planos'),true);
        array_push($this->parameters['breadcrumb'],array('plano','Planos'));
    }

    public function Index(){
        $planos = $this->plano->retornar_todos()->result();

        $this->parameters['content'] = $this->load->view('screens/plano',array('content'=>'gerenciar','planos'=>$planos),true);
        $this->load->view('templates/main_new',$this->parameters);
    }

    public function cadastrar(){
        $tipos_planos = array('1'=>'Consulmo Mínimo','2'=>'Mensalidade','3'=>'Pré-Pago');

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('status', 'Cidade', 'required');
        $this->form_validation->set_rules('tipo', 'UF', 'required');
        $this->form_validation->set_rules('mensalidade', 'UF', 'required|only_numbers');
        $this->form_validation->set_rules('licenca', 'UF', 'required|only_numbers');
        $this->form_validation->set_rules('negativacao', 'UF', 'required|only_numbers');
        $this->form_validation->set_rules('tarifa_bancaria', 'UF', 'required|only_numbers');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','status','tipo'),$this->input->post());
            $dados['mensalidade'] = $this->input->post('mensalidade')/100;
            $dados['licenca'] = $this->input->post('licenca')/100;
            $dados['negativacao'] = $this->input->post('negativacao')/100;
            $dados['tarifa_bancaria'] = $this->input->post('tarifa_bancaria')/100;
            if($this->plano->inserir($dados)){
                set_msg('Plano Cadastrado com sucesso!','sucesso');
                redirect('plano');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar o Plano.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('plano/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/plano',array('content'=>'cadastrar','tipos'=>$tipos_planos),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
}