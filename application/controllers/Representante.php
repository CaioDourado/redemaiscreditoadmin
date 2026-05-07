<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Representante extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->load->model('representante_model','representante');
    }

    public function index(){
        $representantes = $this->representante->retornar_todos()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-suitcase"></i> Representantes';
        $this->parameters['pg_subtitle'] = 'Gerenciamento de Representantes e Alteração de valores';

        //$this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'gerenciar','representantes'=>$representantes),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function cadastrar(){
        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Cadastro de Representante';
        $this->parameters['pg_subtitle'] = 'Insira os dados para cadastrar o reprsentante';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'cadastrar'),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function alterar(){
        $id_representante = $this->verificar_parametro(3,'Não foi informado um representante válido','representante');
        $representante = $this->representante->retornar($id_representante)->row();

        $this->form_validation->set_rules('nome_ou_fantasia', 'Nome', 'required');
        $this->form_validation->set_rules('cpf_cnpj', 'CPF ou CNPJ', 'required|only_numbers');
        $this->form_validation->set_rules('data_nascimento', 'Data de Nascimento', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'only_numbers');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required');
        $this->form_validation->set_rules('numero', 'Numero', 'required');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required');
        $this->form_validation->set_rules('uf', 'UF', 'required');
        $this->form_validation->set_rules('cep', 'CEP', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->model('cliente_model','cliente');
            $nome = strtoupper($this->input->post('nome_ou_fantasia'));
            $dados = elements(array('nome_ou_fantasia','cpf_cnpj','data_nascimento','email','telefone','celular','logradouro','numero','complemento','bairro','cidade','uf','cep'),$this->input->post());
            $dados['consultor_tipo'] = $this->input->post('consultor_tipo');
            $dados['consultor_custo'] = only_numbers($this->input->post('consultor_custo'))/100;
            $dados['consultor'] = 1;
            $dados['id_consultor_fk'] = 0;
            $dados['status'] = 1;
            $dados['tipo_pessoa'] = 1;
            $dados['id_plano_fk'] = 1;
            $dados['nome_proprietario'] = $this->input->post('nome_ou_fantasia');
            $dados['razao_social'] = $this->input->post('nome_ou_fantasia');
            $dados['cpf_proprietario'] = $this->input->post('cpf_cnpj');

            $dados['data_nascimento'] = data_db($this->input->post('data_nascimento'),false);
            $dados['data_nascimento_proprietario'] = data_db($this->input->post('data_nascimento'),false);
            $dados['mensalidade'] = 0;
            $dados['franquia'] = 0;
            $dados['limite_consulta_valor'] = 300;

            if($this->cliente->alterar($id_representante,$dados)){
                set_msg('Alteração efetuada com sucesso!','sucesso');
                redirect('representante/alterar/'.$id_representante);
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar o Representante.');
                redirect(current_url());
            }
        }

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Alteração de Representante';
        $this->parameters['pg_subtitle'] = 'Insira os dados para alterar o reprsentante';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'alterar','representante'=>$representante),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function valores(){
        $this->load->model('consulta_model','consulta');
        $consultas = $this->consulta->retornar_todos_com_custo()->result();
        $consultas_representantes = $this->representante->retornar_consultas_representantes_array();

        $this->form_validation->set_rules('teste', 'Teste', 'required');
        if($this->form_validation->run()==TRUE) {
            $consultas_selecionadas = $this->input->post('id_consulta');
            $custos = $this->input->post('custo');
            $fixos = $this->input->post('fixo');

            $this->representante->remover_consultor_consultas();
            if(count($consultas_selecionadas)>0):
                foreach($consultas_selecionadas as $index => $consulta):
                    $dados = array();
                    $dados['id_consulta_fk'] = $consulta;
                    $dados['custo'] = only_numbers($custos[$index])/100;
                    $dados['fixo'] = only_numbers($fixos[$index])/100;

                    $this->representante->adiconar_valor_consulta($dados);
                endforeach;
            endif;

            set_msg('Valores Cadastradas com sucesso','sucesso');
            redirect(current_url());
        }

        $this->parameters['pg_title'] = '<i class="fa fa-suitcase"></i> Valores de Representante';
        $this->parameters['pg_subtitle'] = 'Controle de valores para todos os representantes no sistema.';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'valores' ,'consultas'=>$consultas,'cr'=>$consultas_representantes),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function clientes(){
        $id_representante = $this->verificar_parametro(3,'Não foi informado um representante válido','representante');
        $representante = $this->representante->retornar($id_representante)->row();

        $clientes = $this->representante->retornar_clientes($id_representante)->result();
        $status = array('0'=>'Cancelados','1'=>'Ativos','2'=>'Bloqueados por Inadimplencia');

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Clientes de Representante';
        $this->parameters['pg_subtitle'] = 'Veja e gerencie todos os clientes do representante informado.';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'clientes','representante'=>$representante,'clientes'=>$clientes,'status'=>$status),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function financeiro(){
        $id_representante = $this->verificar_parametro(3,'Não foi informado um representante válido','representante');
        $representante = $this->representante->retornar($id_representante)->row();

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Financeiro de '.$representante->nome_ou_fantasia;
        $this->parameters['pg_subtitle'] = 'Veja os dados financeiros do representante.';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'financeiro','representante'=>$representante,'id_representante'=>$id_representante),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function adicionar_psv(){
        $id_representante = $this->verificar_parametro(3,'Não foi informado um representante válido','representante');
        $representante = $this->representante->retornar($id_representante)->row();

        $this->form_validation->set_rules('mes', 'Mês', 'required');
        $this->form_validation->set_rules('ano', 'Ano', 'required');
        $this->form_validation->set_rules('tipo_fat', 'Tipo de Faturamento', 'required');

        if($this->form_validation->run()==TRUE) {
            $clients = $this->representante->retornar_clientes($id_representante)->result();
            $clients_seted = array();

            $custo_p_cliente = $this->input->post('valor_p_cliente');
            $custo_p_cliente = str_replace(",",".",$custo_p_cliente);
            $mes = $this->input->post('mes');
            $ano = $this->input->post('ano');
            //$fim = $ano.'-'.$mes.'-19 23:59:59';
            //$inicio = date('Y-m-d H:i:s', strtotime($fim.' -1 month'));
            $inicio = date('Y-m').'-01 00:00:00';
            $fim = date('Y-m-t').' 23:59:59';
            $boletos = $this->representante->retornar_boletos_fat($id_representante, $inicio, $fim)->result();
            $ids_fats = array();
            $faturas = array();
            $total = 0;
            foreach($boletos as $i => $b):
                array_push($ids_fats, $b->fatura_id);
                array_push($clients_seted, $b->id_cliente_fk);
                $faturas[$b->fatura_id] = array('cliente'=>$b->nome_sacado,'valor'=>$b->fatura_valor,'custo_cliente'=>floatval($custo_p_cliente),'custo'=> 0,'final' => 0);
            endforeach;
            $fatura_itens = $this->representante->retornar_fatura_itens($ids_fats)->result();
            foreach($fatura_itens as $i => $f):
                switch($f->grupo):
                    case 'negativacao':
                            $faturas[$f->id_fatura_fk]['custo'] += 8.45;
                            $fatura_itens[$i]->custo = 8.45;
                        break;
                    case 'carta':
                            $faturas[$f->id_fatura_fk]['custo'] += 1.85;
                            $fatura_itens[$i]->custo = 1.85;
                        break;
                    default:
                            $faturas[$f->id_fatura_fk]['custo'] += $f->custo;
                        break;
                endswitch;
            endforeach;

            foreach($clients as $i => $c):
                if(!in_array($c->id_cliente, $clients_seted)){
                    $faturas[$c->id_cliente] = array('cliente'=>$c->nome_ou_fantasia,'valor'=>0,'custo_cliente'=>floatval($custo_p_cliente),'custo'=> 0,'final' => 0);
                }
            endforeach;

            foreach($faturas as $i => $f):
                $final = $f['valor'] - $f['custo'] - $f['custo_cliente'];
                $faturas[$i]['final'] = $final;
                $total += $final;
            endforeach;

            $pdf = array();
            $pdf['conteudo'] = $this->load->view('components/fatura_psv', array('cliente'=>$representante,'valor'=>$total,'inicio'=>$inicio,'fim'=>$fim,'clientes'=>$faturas,'consumo'=>$fatura_itens) ,true);
            $pdf['titulo'] = 'Boleto - Rede Mais Credito';
            $pdf['senha'] = null;
            $this->load->view('components/pdf',$pdf);
            exit;
        }

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Geração de PSV '.$representante->nome_ou_fantasia;
        $this->parameters['pg_subtitle'] = 'Criação de PSV de consultor/representante';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'adicionar_psv','representante'=>$representante,'id_representante'=>$id_representante),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function adicionar_boleto(){
        $id_representante = $this->verificar_parametro(3,'Não foi informado um representante válido','representante');
        $representante = $this->representante->retornar($id_representante)->row();

        $this->form_validation->set_rules('mes', 'Mês', 'required');
        $this->form_validation->set_rules('ano', 'Ano', 'required');
        if($this->form_validation->run()==TRUE) {
                $ano = $this->input->post('ano');
                $mes = $this->input->post('mes');
                $valor_p_cliente = $this->input->post('valor_p_cliente');
                $tipo_fat = $this->input->post('tipo_fat');
        }

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Criação de Boleto de '.$representante->nome_ou_fantasia;
        $this->parameters['pg_subtitle'] = 'Criação de boleto com dados de consumo do consultor/representante';

        $this->parameters['content'] = $this->load->view('screens/representante',array('content'=>'adicionar_boleto','representante'=>$representante,'id_representante'=>$id_representante),true);
        $this->load->view('templates/maing',$this->parameters);
    }
}