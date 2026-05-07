<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Portifolio extends ControllerAuth{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('portifolio_model', 'portifolio');
		$this->parameters['title'] = 'Portifolio';
		$this->parameters['title_window'] = 'Portifolio';
		$this->parameters['menu'] = $this->load_menu('padrao_novo');
		$this->parameters['breadcrumb'][] = array('portifolio', 'Portifolio');
	}

	public function index(){
		$consultas = $this->portifolio->retornar_todos()->result();

		$this->parameters['pg_title'] = '<i class="fa fa-list"></i> Portifolio';
		$this->parameters['pg_subtitle'] = 'Gerenciamento de consultas disponiveis no Portifólio';
		$this->parameters['content'] = $this->load->view('screens/portifolio',array('content'=>'gerenciar','consultas'=>$consultas),true);
		$this->load->view('templates/maing',$this->parameters);
	}

	public function alterar(){
		$id_portifolio = $this->verificar_parametro(3,'Não foi informado um portifólio válido','portifolio');
		$consulta = $this->portifolio->retornar($id_portifolio)->row();
		$slugs = $this->portifolio->get_slugs()->result();
		$montagem_now = json_decode($consulta->montagem, true);
		$auxiliares_now = json_decode($consulta->auxiliares, true);
		if($auxiliares_now===null){ $auxiliares_now = array(); }

		$this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('descricao', 'Descrição', 'required');
		$this->form_validation->set_rules('montagem[]', 'Montagem da Consulta', 'required');
		$this->form_validation->set_rules('valor', 'Valor de Venda', 'required|only_numbers');
		$this->form_validation->set_rules('valor_ge', 'Valor de GE', 'required|only_numbers');
		$this->form_validation->set_rules('valor_prepago', 'Valor de Pré Pago', 'required|only_numbers');
		$this->form_validation->set_rules('franquia_valor', 'Valor de Franquia', 'only_numbers');

		if($this->form_validation->run()==TRUE) {
			$dados = elements(array('status','franquia_status','nome','categoria','tipo','slug','template','view','input','input_form','descricao','resumo'),$this->input->post());
			$dados['montagem'] = json_encode($this->input->post('montagem'));
			$dados['auxiliares'] = "[]";
			if(isset($_POST['auxiliares'])){
				$dados['auxiliares'] = json_encode($this->input->post('auxiliares'));
			}
			$dados['valor'] = ($this->input->post('valor')/100);
			$dados['valor_ge'] = ($this->input->post('valor_ge')/100);
			$dados['valor_prepago'] = ($this->input->post('valor_prepago')/100);
			$dados['franquia_valor'] = ($this->input->post('franquia_valor')/100);

			if($this->portifolio->alterar($id_portifolio,$dados)){
				set_msg('Consulta Alterada com sucesso!','sucesso');
				redirect(current_url());
			}else{
				set_msg('Ocorreu um erro na hora de alterar a Consulta.');
				redirect(current_url());
			}
		}

		$this->parameters['pg_title'] = '<i class="fa fa-pencil"></i> Portifolio - Alterar';
		$this->parameters['pg_subtitle'] = 'Alteração de Consulta de Portifolio';
		$this->parameters['content'] = $this->load->view('screens/portifolio',array('content'=>'alterar','consulta'=>$consulta,'slugs'=>$slugs,'montagem'=>$montagem_now,'auxiliares'=>$auxiliares_now),true);
		$this->load->view('templates/maing',$this->parameters);
	}
}
