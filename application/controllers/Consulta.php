<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Consulta extends ControllerAuth{
    private $health_escopo = 'consulta';
    private $health_chave = 'provider_health_blocking';

    public function __construct(){
        parent::__construct();
        $this->load->model('consulta_model', 'consulta');
        $this->load->model('sistema_configuracao_model', 'sistema_configuracao');
        $this->load->model('adminauditoria_model', 'adminauditoria');
        $this->parameters['title'] = 'Consulta';
        $this->parameters['title_window'] = 'Consulta';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'consultas'), true);
        array_push($this->parameters['breadcrumb'], array('consulta', 'Consultas'));
    }

    public function index(){
        $consultas = $this->consulta->retornar_todos()->result();
        $banimento_fornecedores_ativo = $this->sistema_configuracao->booleano(
            $this->health_escopo,
            $this->health_chave,
            true
        );

        $this->parameters['content'] = $this->load->view('screens/consulta',array(
            'content'=>'gerenciar',
            'consultas'=>$consultas,
            'banimento_fornecedores_ativo'=>$banimento_fornecedores_ativo
        ),true);
        $this->load->view('templates/main_new',$this->parameters);
    }

    public function alternar_banimento_fornecedores(){
        if(strtoupper((string) $this->input->server('REQUEST_METHOD'))!=='POST'){
            show_error('Metodo nao permitido.', 405);
        }

        $resultado = $this->sistema_configuracao->alternar_booleano(
            $this->health_escopo,
            $this->health_chave,
            $this->session->userdata('id')
        );

        if(!empty($resultado['ok'])){
            $this->adminauditoria->registrar(array(
                'area'=>'consulta_configuracao',
                'acao'=>'alterar_provider_health_blocking',
                'status'=>'sucesso',
                'referencia_tipo'=>'sistema_configuracao',
                'referencia_id'=>$resultado['id'],
                'mensagem'=>'Banimento automatico de fornecedores '.($resultado['atual'] ? 'ligado.' : 'desligado.'),
                'contexto'=>array(
                    'escopo'=>$this->health_escopo,
                    'chave'=>$this->health_chave,
                    'anterior'=>$resultado['anterior'] ? 1 : 0,
                    'atual'=>$resultado['atual'] ? 1 : 0
                )
            ));

            set_msg(
                'Banimento automatico de fornecedores '.($resultado['atual'] ? 'ligado' : 'desligado').' com sucesso!',
                'sucesso'
            );
        }else{
            $this->adminauditoria->registrar(array(
                'area'=>'consulta_configuracao',
                'acao'=>'alterar_provider_health_blocking',
                'status'=>'erro',
                'erro'=>'CONFIG_UPDATE_FAILED',
                'mensagem'=>isset($resultado['mensagem']) ? $resultado['mensagem'] : 'Falha ao alterar configuracao.',
                'contexto'=>array('escopo'=>$this->health_escopo, 'chave'=>$this->health_chave)
            ));
            set_msg(isset($resultado['mensagem']) ? $resultado['mensagem'] : 'Nao foi possivel alterar a configuracao.');
        }

        redirect('consulta');
    }

    public function cadastrar(){
        $grupos = $this->consulta->retornar_grupos_array();

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('slug', 'Slug', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('id_grupo_consulta_fk', 'Grupo', 'required');
        $this->form_validation->set_rules('descricao', 'Descrição', 'required');
        $this->form_validation->set_rules('venda', 'Valor de Venda', 'required|only_numbers');
        $this->form_validation->set_rules('venda_pre', 'Valor de Venda para Pré-Pago', 'required|only_numbers');
        $this->form_validation->set_rules('franquia', 'Valor de Venda para Franquia', 'only_numbers');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','slug','ordem','icone','status','id_grupo_consulta_fk','descricao','qtd_ge','franquia_check'),$this->input->post());
            $dados['venda'] = $this->input->post('venda')/100;
            $dados['venda_pre'] = $this->input->post('venda_pre')/100;
            $dados['venda_ge'] = $this->input->post('venda_ge')/100;
            $dados['franquia'] = $this->input->post('franquia')/100;
            if($this->consulta->inserir($dados)){
                set_msg('Consulta Cadastrada com sucesso!','sucesso');
                redirect('consulta');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar a Consulta.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('consulta/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/consulta',array('content'=>'cadastrar','grupos'=>$grupos),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }

    public function alterar(){
        $id_consulta = $this->verificar_parametro(3,'Não foi informada uma consulta válida','consulta');
        $consulta = $this->consulta->retornar($id_consulta)->row();
        $grupos = $this->consulta->retornar_grupos_array();

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('slug', 'Slug', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('id_grupo_consulta_fk', 'Grupo', 'required');
        $this->form_validation->set_rules('descricao', 'Descrição', 'required');
        $this->form_validation->set_rules('venda', 'Valor de Venda', 'required|only_numbers');
        $this->form_validation->set_rules('venda_pre', 'Valor de Venda para Pré-Pago', 'required|only_numbers');
        $this->form_validation->set_rules('venda_ge', 'Valor de Altos Valores', 'only_numbers');
		$this->form_validation->set_rules('franquia', 'Valor de Venda para Franquia', 'only_numbers');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','slug','ordem','icone','status','id_grupo_consulta_fk','descricao','qtd_ge','franquia_check'),$this->input->post());
            $dados['venda'] = $this->input->post('venda')/100;
            $dados['venda_pre'] = $this->input->post('venda_pre')/100;
            $dados['venda_ge'] = $this->input->post('venda_ge')/100;
			$dados['franquia'] = $this->input->post('franquia')/100;
            if($this->consulta->alterar($consulta->id_consulta,$dados)){
                set_msg('Consulta Cadastrada com sucesso!','sucesso');
                redirect('consulta');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar a Consulta.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('consulta/alterar/'.$consulta->id_consulta,'Alterar'));
        $this->parameters['title'] .= ' - Alterar';
        $this->parameters['title_window'] .= ' - Alterar';
        $this->parameters['content'] = $this->load->view('screens/consulta',array('content'=>'alterar','consulta'=>$consulta,'grupos'=>$grupos),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }

	public function bloquear(){
		$id_consulta = $this->verificar_parametro(3,'Não foi informada uma consulta válida','consulta');
		$consulta = $this->consulta->retornar($id_consulta)->row();

		if($consulta->status == 1):
			$this->consulta->alterar($consulta->id_consulta,array('status'=>0));
			set_msg('Consulta bloqueada com sucesso!','sucesso');
		else:
			$this->consulta->alterar($consulta->id_consulta,array('status'=>1));
			set_msg('Consulta retornada com sucesso!','sucesso');
		endif;

		redirect("consulta");
	}

    public function teste(){
        $consultas = $this->consulta->retornar_bins()->result();


        echo '<table>';
            echo '<tbody>';
                foreach($consultas as $index => $consulta):
                    $dados = json_decode($consulta->retorno_json);

                    if(isset($dados->BASE_ESTADUAL->CONSULTA_RESULT->CONSULTA_RESPONSE->INFO_XML->RESPOSTA->INFORM_BASE_ESTADUAL_OUTRAS_UFS->RESTRICOES_IMPEDIMENTOS)):
                        $restricoes = $dados->BASE_ESTADUAL->CONSULTA_RESULT->CONSULTA_RESPONSE->INFO_XML->RESPOSTA->INFORM_BASE_ESTADUAL_OUTRAS_UFS->RESTRICOES_IMPEDIMENTOS;
                        if(is_string($restricoes->RESTRICAO1)):
                            echo $consulta->id_consulta_veicular_efetuada.' - '.$restricoes->RESTRICAO1.'<br>';
                        endif;
                    endif;
                endforeach;
            echo '</tbody>';
        echo '</table>';
    }

    public function teste_processo(){
        $consulta = $this->consulta->retornar_consulta_teste(39)->row()->retorno_json;
        $json_consulta = json_decode($consulta);

        $this->load->view('templates/relatorio_teste',array('dados'=>$json_consulta));
    }
}
