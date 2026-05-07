<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Relatorio extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('relatorio_model', 'relatorio');
        $this->parameters['title'] = 'Relatórios';
        $this->parameters['title_window'] = 'Relatório';
        $this->parameters['menu'] = $this->load_menu('padrao_novo');
        array_push($this->parameters['breadcrumb'], array('relatorio', 'Relatórios'));
    }

    public function index(){
        $dados = $this->relatorio->relatorio_clientes_base()->row();

        $this->parameters['pg_title'] = '<i class="fa fa-bar-chart"></i> Relatório Geral';
        $this->parameters['pg_subtitle'] = 'Dados em Gráficos e relatórios';

        $this->parameters['content'] = $this->load->view('screens/relatorio', array('content' => 'index','dados'=>$dados), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function fornecedores(){
        $this->form_validation->set_rules('inicio', 'Data Inicial', 'required');
        $this->form_validation->set_rules('fim', 'Data Final', 'required');

        $relatorio = null;
        $resumo = array();
        $fim = date('Y-m-d');
        $inicio = date('Y-m-d', strtotime($fim.' - 1 month'));

        if($this->form_validation->run()==TRUE) {
            $inicio = data_db($this->input->post('inicio'),false);
            $fim = data_db($this->input->post('fim'),false);

            $relatorio = $this->relatorio->relatorio_consultas_fornecedores($inicio, $fim)->result();
            $veicular = $this->relatorio->relatorio_veiculares_fornecedores($inicio, $fim)->result();
            $negativacoes = $this->relatorio->relatorio_negativacoes_fornecedores($inicio, $fim)->result();

            foreach($relatorio as $i => $l){
                if(!isset($resumo[$l->fornecedor])){ $resumo[$l->fornecedor] = new stdClass(); $resumo[$l->fornecedor]->qtd = 0; $resumo[$l->fornecedor]->media = 0; $resumo[$l->fornecedor]->total = 0; }
                $resumo[$l->fornecedor]->qtd += $l->qtd;
                $resumo[$l->fornecedor]->total += $l->total;
            }

            foreach($veicular as $i => $l){
                if(!isset($resumo[$l->fornecedor])){ $resumo[$l->fornecedor] = new stdClass(); $resumo[$l->fornecedor]->qtd = 0; $resumo[$l->fornecedor]->media = 0; $resumo[$l->fornecedor]->total = 0; }
                $resumo[$l->fornecedor]->qtd += $l->qtd;
                $resumo[$l->fornecedor]->total += $l->total;
            }

            foreach($negativacoes as $i => $l){
                if(!isset($resumo[$l->fornecedor])){ $resumo[$l->fornecedor] = new stdClass(); $resumo[$l->fornecedor]->qtd = 0; $resumo[$l->fornecedor]->media = 0; $resumo[$l->fornecedor]->total = 0; }
                $resumo[$l->fornecedor]->qtd += $l->qtd;
                $resumo[$l->fornecedor]->total += $l->total;
            }
        }

        $this->parameters['pg_title'] = '<i class="fa fa-bar-chart"></i> Relatório de Fornecedores';
        $this->parameters['pg_subtitle'] = 'Demonstra os dados de consultas agrupados por fornecedores, mostrando gastos e venda.';

        $this->parameters['content'] = $this->load->view('screens/relatorio', array('content' => 'fornecedores', 'inicio'=>$inicio, 'fim'=> $fim, 'relatorio' => $relatorio, 'veicular'=> $veicular, 'negativacoes' => $negativacoes, 'resumo'=>$resumo), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function pagamentos(){
        $this->parameters['pg_title'] = '<i class="fa fa-money"></i> Relatório de Pagamentos';
        $this->parameters['pg_subtitle'] = 'Relatório Mostrando os pagamentos por mês, separado em franquia e Matriz';

        $this->parameters['content'] = $this->load->view('screens/relatorio', array('content' => 'pagamentos','relatorio'=>null), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function abertura_clientes(){
        $this->parameters['pg_title'] = '<i class="fa fa-user"></i> Relatório de Aberturas de Clientes';
        $this->parameters['pg_subtitle'] = 'Relatório Mostrando a abertura de clientes durantes o meses.';

        $this->parameters['content'] = $this->load->view('screens/relatorio', array('content' => 'aberturas_clientes','relatorio'=>null), true);
        $this->load->view('templates/maing', $this->parameters);
    }
}