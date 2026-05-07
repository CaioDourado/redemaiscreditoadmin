<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->load->model('usuario_model','usuario');
        $this->parameters['title'] = 'Usuário';
        $this->parameters['title_window'] = 'Usuário';
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'usuarios'),true);
        array_push($this->parameters['breadcrumb'],array('usuario','Usuário'));
    }

    public function index(){
        array_push($this->parameters['breadcrumb'],array('usuario','Gerenciar'));
        $this->parameters['title'] .= ' - Gerenciar';
        $this->parameters['title_window'] .= ' - Gerenciar';
        $this->parameters['content'] = $this->load->view('screens/usuario',array('content'=>'manage','users'=>array()),true);
        $this->load->view('templates/main_new',$this->parameters);
    }

    public function perfil(){

    }

    public function cadastrar(){
        //$this->load->model('consultor','consultor');

        //$consultores = $this->consultor->retornar_array();

        array_push($this->parameters['breadcrumb'],array('usuario/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/usuario',array('content'=>'cadastrar','consultores'=>array()),true);
        $this->load->view('templates/main_new',$this->parameters);
    }

    public function alterar(){

    }

    public function excluir(){

    }

    public function redefinir_senha(){
        $id_usuario = $this->verificar_parametro(3,'Não foi informado um Usuário válido','cliente');
        $usuario = $this->usuario->retornar($id_usuario)->row();

        $dados = array('senha'=>md5('123456'));

        if($this->usuario->alterar($id_usuario,$dados)){
            set_msg('Usuário alterado com sucesso!','sucesso');
        }else{
            set_msg('Ocorreu um erro ao alterar o usuário.');
        }

        redirect('inicio');
    }
}