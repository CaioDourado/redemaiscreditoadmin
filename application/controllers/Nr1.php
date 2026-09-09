<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nr1 extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->load->model('nr1_model', 'nr1');
        $this->parameters['title'] = 'NR-1';
        $this->parameters['title_window'] = 'NR-1';
        $this->parameters['menu'] = $this->load_menu('padrao_novo');
        array_push($this->parameters['breadcrumb'], array('nr1', 'NR-1'));
    }

    public function index(){
        $this->garantir_estrutura();
        $origem = $this->input->get('origem');
        if(!in_array($origem, array('matriz', 'franquia'), true)) $origem = '';

        $clientes = $this->nr1->listar_clientes($origem);
        $resumo = array('total' => count($clientes), 'matriz' => 0, 'franquia' => 0, 'vidas' => 0);
        foreach($clientes as $cliente){
            if((int) $cliente->id_franquia_fk > 0) $resumo['franquia']++;
            else $resumo['matriz']++;
            $resumo['vidas'] += (int) $cliente->quantidade_vidas;
        }

        $this->parameters['pg_title'] = '<i class="fa fa-shield"></i> NR-1';
        $this->parameters['pg_subtitle'] = 'Clientes com contratação confirmada, origem e acompanhamento da solução NR-1.';
        $this->parameters['content'] = $this->load->view('screens/nr1', array(
            'content' => 'index',
            'clientes' => $clientes,
            'origem' => $origem,
            'resumo' => $resumo,
            'status_labels' => $this->status_labels()
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function editar(){
        $this->garantir_estrutura();
        $id_cliente = (int) $this->uri->segment(3);
        if($id_cliente <= 0){
            set_msg('Cliente não informado.');
            redirect('nr1');
        }

        $cliente = $this->nr1->buscar_cliente($id_cliente);
        if($cliente === null){
            set_msg('Cliente não encontrado.');
            redirect('nr1');
        }

        if(strtoupper((string) $this->input->server('REQUEST_METHOD')) === 'POST'){
            $this->form_validation->set_rules('nome_ou_fantasia', 'Nome', 'required');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if($this->form_validation->run() === TRUE){
                $dados_cliente = array(
                    'nome_ou_fantasia' => strtoupper(trim((string) $this->input->post('nome_ou_fantasia', true))),
                    'email' => trim((string) $this->input->post('email', true)),
                    'telefone' => trim((string) $this->input->post('telefone', true)),
                    'celular' => trim((string) $this->input->post('celular', true)),
                    'nr1' => $this->input->post('nr1') ? 1 : 0
                );
                $salvo = $this->nr1->atualizar_cliente($id_cliente, $dados_cliente);

                if($cliente->id_nr1_ivi_empresa !== null){
                    $status = $this->input->post('status', true);
                    if(!array_key_exists($status, $this->status_labels())){
                        set_msg('Status da solicitação inválido.');
                        redirect('nr1/editar/'.$id_cliente);
                    }
                    $observacao = trim((string) $this->input->post('observacao', true));
                    if(strlen($observacao) > 2000) $observacao = substr($observacao, 0, 2000);
                    $marcar_contratado = $status === 'contratado' && $cliente->contratado_em === null;
                    $salvo = $this->nr1->atualizar_solicitacao($cliente->id_nr1_ivi_empresa, array(
                        'status' => $status,
                        'observacao' => $observacao !== '' ? $observacao : null
                    ), $marcar_contratado) && $salvo;
                }

                if($salvo) set_msg('Dados do NR-1 atualizados com sucesso.', 'sucesso');
                else set_msg('Não foi possível atualizar os dados do NR-1.');
                redirect('nr1');
            }
        }

        array_push($this->parameters['breadcrumb'], array('nr1/editar/'.$id_cliente, 'Editar '.$cliente->nome_ou_fantasia));
        $this->parameters['pg_title'] = '<i class="fa fa-pencil"></i> Editar NR-1';
        $this->parameters['pg_subtitle'] = $cliente->nome_ou_fantasia;
        $this->parameters['content'] = $this->load->view('screens/nr1', array(
            'content' => 'editar',
            'cliente' => $cliente,
            'status_labels' => $this->status_labels()
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    private function garantir_estrutura(){
        if(!$this->nr1->estrutura_disponivel()){
            show_error('A estrutura do NR-1 ainda não foi instalada no banco de dados.', 503);
        }
    }

    private function status_labels(){
        return array(
            'novo' => 'Novo',
            'em_contato' => 'Em contato',
            'treinamento' => 'Treinamento',
            'negociacao' => 'Negociação',
            'contratado' => 'Contratado',
            'modificado' => 'Modificado',
            'cancelado' => 'Cancelado'
        );
    }
}
