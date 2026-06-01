<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends ControllerAuth {
    public function __construct(){
        parent::__construct();
        $this->load->model('cliente_model','cliente');
        $this->parameters['title'] = 'Cliente';
        $this->parameters['title_window'] = 'Cliente';
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'clientes'),true);
        array_push($this->parameters['breadcrumb'],array('cliente','Clientes'));
    }
    public function emails(){
        $clientes = $this->cliente->retornar_ativos()->result();
        foreach($clientes as $index => $cliente):
            echo $cliente->email.'<br>';
        endforeach;
        exit;
    }
    public function Index(){
        $clientes = $this->cliente->retornar_todos_ordenado_gerenciar()->result();

        $resumo = array('dias'=>array(),'status'=>array(),'faturamento'=>0);
        $status = array('0'=>'Cancelados','1'=>'Ativos','2'=>'Bloqueados por Inadimplencia');
        foreach($clientes as $index => $cliente):
            if($cliente->dia_vencimento!=null):
                if(!isset($resumo['dias'][$cliente->dia_vencimento])) $resumo['dias'][$cliente->dia_vencimento] = 0;
                if(!isset($resumo['status'][$cliente->status])) $resumo['status'][$cliente->status] = 0;

                $resumo['dias'][$cliente->dia_vencimento]++;
                $resumo['status'][$cliente->status]++;

                if($cliente->status==1) $resumo['faturamento'] += $cliente->mensalidade;
            endif;
        endforeach;

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Clientes';
        $this->parameters['pg_subtitle'] = 'Gerenciamento de Clientes';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gerenciar_novo','clientes'=>$clientes,'resumo'=>$resumo,'status'=>$status),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function gerenciar(){
        $area = null;
        if(isset($_GET['area'])) $area = $_GET['area'];
        if($area==null) redirect('cliente/gerenciar?area=matriz');

        switch($area):
            case 'matriz': $clientes = $this->cliente->retornar_matriz_ativos_ordenado()->result(); break;
            case 'franquias': $clientes = $this->cliente->retornar_franquias_ativos_ordenado()->result(); break;
            case 'representantes': $clientes = $this->cliente->retornar_representantes_ativos_ordenado()->result(); break;
        endswitch;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gerenciar','area'=>$area,'clientes'=>$clientes),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function email_geral(){
        $clientes = $this->cliente->retornar_ativos()->result();

        $this->form_validation->set_rules('titulo', 'Titulo', 'required');
        $this->form_validation->set_rules('mensagem', 'Mensagem', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->helper('phpmailer');

            $from = 'redemaiscredito@gmail.com';
            $nome = 'Rede Mais Crédito';
            $assunto = $this->input->post('titulo');
            $mensagem = $this->input->post('mensagem');

            $dados_retorno = array('sucesso'=>0, 'erro'=>0);

            foreach($clientes as $i => $c):
                $dados_retorno = array('sucesso'=>0, 'erro'=>0);
                if($c->email!=null):
                    $to = $c->email;
                    $cc = array('gigiomangia@hotmail.com','caiof.dourado@gmail.com');
                    if(enviar_email($from, $to, $assunto, $mensagem, $nome, $cc)) $dados_retorno['sucesso']++; else $dados_retorno['erro']++;
                endif;
            endforeach;

            set_msg('E-mails Enviados com sucesso: '.$dados_retorno['sucesso'], 'sucesso');
            set_msg('E-mails sem envio: '.$dados_retorno['erro']);
        }

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> E-mail Geral';
        $this->parameters['pg_subtitle'] = 'Envio de E-mail para todos os clientes ativos no sistema.';
        $this->parameters['menu'] = $this->load_menu('clientes');
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'email_geral','clientes'=>$clientes),true);
        $this->load->view('templates/maingtext',$this->parameters);
    }
    public function gestao_negativacao(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();
        $negativacoes = $this->cliente->retornar_negativacoes($id_cliente)->result();
        foreach($negativacoes as $index => $n):
            $negativacoes[$index]->parametros = json_decode($n->parametros);
        endforeach;

        array_push($this->parameters['breadcrumb'],array('cliente/gestao_negativacao/'.$cliente->id_cliente,'Gestão de Negativações de '.$cliente->nome_ou_fantasia));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['menu'] .= $this->load->view('components/menu',array('menu'=>'mais_opcoes'),true);

        $this->parameters['pg_title'] = '<i class="fa fa-minus-circle"></i> Gestão de Negativações';
        $this->parameters['pg_subtitle'] = $cliente->nome_ou_fantasia;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gestao_negativacao','cliente'=>$cliente,'negativacoes'=>$negativacoes),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function validacao(){
        $clientes = $this->cliente->retornar_todos()->result();
        $retorno = array();
        $estados = retornar_estados();

        foreach($clientes as $index => $cliente):
            $insert = false;
            $cliente->validacao = '';
            if(strlen($cliente->cpf_cnpj)>11){
                if(!valida_cnpj($cliente->cpf_cnpj)){
                    $cliente->validacao .= 'CNPJ informado incorreto.<br>';
                    $insert = true;
                }
            }else{
                if(!valida_cpf($cliente->cpf_cnpj)){
                    $cliente->validacao .= 'CPF informado incorreto.<br>';
                    $insert = true;
                }
            }

            if(strlen($cliente->cep)<8){
                $cliente->validacao .= 'CEP Incorreto ('.$cliente->cep.')<br>';
                $insert = true;
            }

            if(!array_key_exists(strtoupper($cliente->uf),$estados)){
                $cliente->validacao .= 'UF - Incorreto ('.$cliente->uf.')';
                $insert = true;
            }

            if($insert) array_push($retorno,$cliente);
        endforeach;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'validacao','clientes'=>$retorno),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function teste(){
        var_dump(valida_cnpj('16692549000124'));
    }
    public function ultimas_aberturas(){
        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Últimas Aberturas';
        $this->parameters['pg_subtitle'] = 'Dados dos últimos clientes abertos no sistema.';

        $this->parameters['menu'] = $this->load_menu('clientes');


        $clientes = $this->cliente->ultimas_aberturas(200)->result();
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'ultimas_aberturas','clientes'=>$clientes),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function gerenciar_por_area(){
        $clientes = $this->cliente->retornar_agrupado_por_cidade()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Clientes';
        $this->parameters['pg_subtitle'] = 'Gerenciamento de Clientes por Área';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gerenciar_por_area','clientes'=>$clientes),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function clientes_por_area(){
        $cidade = $this->verificar_parametro(3,'Não foi informada uma cidade','cliente');
        $cidade = str_replace('_',' ',$cidade);
        $clientes = $this->cliente->retornar_de_cidade($cidade)->result();

        $this->parameters['pg_title'] = '<i class="fa fa-users"></i> Clientes';
        $this->parameters['pg_subtitle'] = 'Clientes filtrados por Área';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'clientes_por_area','clientes'=>$clientes,'cidade'=>$cidade),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function cadastrar(){
        $this->load->model('plano_model','plano');

        $consultores = $this->cliente->retornar_consultores_array();
        $planos = $this->plano->retornar_todos_array();

        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('tipo_pessoa', 'Tipo de Pessoa', 'required');
        $this->form_validation->set_rules('nome_ou_fantasia', 'Nome', 'required');
        $this->form_validation->set_rules('cpf_cnpj', 'CPF ou CNPJ', 'required|only_numbers');
        $this->form_validation->set_rules('data_nascimento', 'Data de Nascimento', 'required');
        //$this->form_validation->set_rules('documento', 'Documento', 'required|required');
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'only_numbers');
        //$this->form_validation->set_rules('celular', 'celular', 'required|only_numbers');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required');
        $this->form_validation->set_rules('numero', 'Numero', 'required');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required');
        $this->form_validation->set_rules('uf', 'UF', 'required');
        $this->form_validation->set_rules('cep', 'CEP', 'required');

        $this->form_validation->set_rules('mensalidade', 'Mensalidade', 'required|only_numbers');
        $this->form_validation->set_rules('franquia', 'Franquia', 'required|only_numbers');
        $this->form_validation->set_rules('limite_consulta_qtd', 'Limite de Consultas (quantidade)', 'required|only_numbers');
        $this->form_validation->set_rules('limite_consulta_valor', 'Limite de Consultas (valor)', 'required|only_numbers');

        $this->form_validation->set_rules('limite_bloqueio', 'Limite de Bloqueio', 'required|only_numbers');

        if($this->form_validation->run()==TRUE) {
            $nome = strtoupper($this->input->post('nome_ou_fantasia'));
            $dados = elements(array('bloqueavel','nome_proprietario','cpf_proprietario','consultor','status','tipo_pessoa','id_consultor_fk','id_plano_fk','nome_ou_fantasia','razao_social','carta_nome1','carta_nome2','carta_nome3','cpf_cnpj','data_nascimento','documento','email','telefone','celular','logradouro','numero','complemento','bairro','cidade','uf','cep','limite_consulta_qtd','dia_vencimento'),$this->input->post());
            $dados['data_nascimento'] = data_db($this->input->post('data_nascimento'),false);
            $dados['data_nascimento_proprietario'] = data_db($this->input->post('data_nascimento_proprietario'),false);
            $dados['mensalidade'] = $this->input->post('mensalidade')/100;
            $dados['franquia'] = $this->input->post('franquia')/100;
            $dados['limite_consulta_valor'] = $this->input->post('limite_consulta_valor')/100;
            $dados['limite_bloqueio'] = $this->input->post('limite_bloqueio')/100;
            if($this->input->post('id_consultor_fk')==NULL||$this->input->post('id_consultor_fk')=="") $dados['id_consultor_fk'] = 0;
            if($this->cliente->inserir($dados)){
                $id = $this->cliente->retornar_ultimo_id();
                $dados_usuario = $this->cliente->criar_usuario($id,$nome);
                if($dados_usuario!=null)
                    set_msg('<br><b>Usuário: </b> '.$dados_usuario['usuario'].'<br><b>Senha: </b>'.$dados_usuario['senha'],'sucesso');
                else
                    set_msg('Ocorreu um erro na criação de Usuário.');
                redirect('cliente');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar o Cliente.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('cliente/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'cadastrar','consultores'=>$consultores,'planos'=>$planos),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function perfil(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();
        $consultas = $this->cliente->retornar_consultas_efetuadas($id_cliente, 300)->result();
        $veiculares = $this->cliente->retornar_consultas_veiculares($id_cliente)->result();
        $cartas = $this->cliente->retornar_cartas($id_cliente)->result();
        $negativacoes = $this->cliente->retornar_negativacoes($id_cliente)->result();
        $baixas = $this->cliente->retornar_baixas($id_cliente)->result();
        $usuarios = $this->cliente->retornar_usuarios($id_cliente)->result();
        $boletos = $this->cliente->retornar_boletos($id_cliente)->result();
        $faturas = $this->cliente->retornar_faturas($id_cliente)->result();


        foreach($negativacoes as $index => $negativacao):
            if($negativacao->slug=="negativacaoscpcpf"||$negativacao->slug=="negativacaoscpcpj"){
                $negativacao->tipo = 'Varejo';
                if (strpos($negativacao->retorno, 'ERRO') !== false){
                    //echo $negativacao->retorno.'<br>';
                    $negativacao->status = '<i class="fa fa-close text-danger"></i>';
                }else{
                    $negativacao->status = '<i class="fa fa-check text-success"></i>';
                }
            }else{
                $negativacao->tipo = ' Pefin';
                $retorno_json = simplexml_load_string($negativacao->retorno);
                if(isset($retorno_json->inclusao->erro)){
                    $negativacao->status = '<i class="fa fa-close text-danger"></i>';
                }else{
                    $negativacao->status = '<i class="fa fa-check text-success"></i>';
                }
            }
        endforeach;

        array_push($this->parameters['breadcrumb'],array('cliente/perfil/'.$cliente->nome_ou_fantasia,'Perfil de '.$cliente->nome_ou_fantasia));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['menu'] .= $this->load->view('components/menu',array('menu'=>'mais_opcoes'),true);

        $this->parameters['pg_title'] = '<i class="fa fa-user"></i> Perfil de Cliente';
        $this->parameters['pg_subtitle'] = $cliente->nome_ou_fantasia;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'perfil','cliente'=>$cliente,'usuarios'=>$usuarios,'consultas'=>$consultas,'veiculares'=>$veiculares,'cartas'=>$cartas,'negativacoes'=>$negativacoes,'baixas'=>$baixas,'boletos'=>$boletos,'faturas'=>$faturas),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function alterar(){
        $this->load->model('plano_model','plano');

        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();
        $consultores = $this->cliente->retornar_consultores_array();
        $planos = $this->plano->retornar_todos_array();

        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('tipo_pessoa', 'Tipo de Pessoa', 'required');
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

        $this->form_validation->set_rules('mensalidade', 'Mensalidade', 'required|only_numbers');
        $this->form_validation->set_rules('franquia', 'Franquia', 'required|only_numbers');
        $this->form_validation->set_rules('limite_consulta_qtd', 'Limite de Consultas (quantidade)', 'required|only_numbers');
        $this->form_validation->set_rules('limite_consulta_valor', 'Limite de Consultas (valor)', 'required|only_numbers');

		$this->form_validation->set_rules('limite_bloqueio', 'Limite de Bloqueio', 'required|only_numbers');

        if($this->form_validation->run()==TRUE) {
            $nome = strtoupper($this->input->post('nome_ou_fantasia'));
            $dados = elements(array('bloqueavel','nome_proprietario','cpf_proprietario','consultor','status','tipo_pessoa','id_consultor_fk','id_plano_fk','nome_ou_fantasia','razao_social','carta_nome1','carta_nome2','carta_nome3','cpf_cnpj','data_nascimento','documento','email','telefone','celular','logradouro','numero','complemento','bairro','cidade','uf','cep','limite_status','limite_consulta_qtd','dia_vencimento'),$this->input->post());
            $dados['data_nascimento'] = data_db($this->input->post('data_nascimento'),false);
            $dados['data_nascimento_proprietario'] = data_db($this->input->post('data_nascimento_proprietario'),false);
            $dados['mensalidade'] = $this->input->post('mensalidade')/100;
            $dados['franquia'] = $this->input->post('franquia')/100;
            $dados['limite_consulta_valor'] = $this->input->post('limite_consulta_valor')/100;
			$dados['limite_bloqueio'] = $this->input->post('limite_bloqueio')/100;
            if($this->input->post('id_consultor_fk')==NULL||$this->input->post('id_consultor_fk')=="") $dados['id_consultor_fk'] = 0;
            if($this->cliente->alterar($id_cliente,$dados)){
                set_msg('Cliente Alterado com sucesso!','sucesso');
                redirect('cliente/alterar/'.$id_cliente);
            }else{
                set_msg('Ocorreu um erro na hora de alterar o Cliente.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('cliente/alterar/'.$id_cliente,'Alterar'));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'alterar','cliente'=>$cliente,'consultores'=>$consultores,'planos'=>$planos),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function Inativar(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();

        $this->form_validation->set_rules('status', 'Motivo de Inativação', 'required');

        if($this->form_validation->run()==TRUE) {
           if($this->cliente->alterar($id_cliente,array('status'=>$this->input->post('status')))){
               $this->cliente->alterar_usuarios($id_cliente,array('status'=>$this->input->post('status')));
               set_msg('Cliente Inativado com sucesso!','sucesso');
               redirect('cliente');
           }else{
               set_msg('Ocorreu um erro na hora de Inativar o Cliente.');
               redirect(current_url());
           }
        }

        array_push($this->parameters['breadcrumb'],array('cliente/inativar/'.$id_cliente,'Inativar'));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['title'] .= ' - Inativar';
        $this->parameters['title_window'] .= ' - Inativar';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'inativar','cliente'=>$cliente),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function reativar(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();

        if($this->cliente->alterar($id_cliente,array('status'=>1))) {
            $this->cliente->alterar_usuarios($id_cliente, array('status' => '1'));
            set_msg('Cliente Reativado com sucesso!', 'sucesso');
        }else{
            set_msg('Ocorreu um erro ao reativar o cliente.');
        }
        redirect('cliente');
    }
    public function ver_consulta(){
        $id_consulta_efetuada = $this->verificar_parametro('3','Não foi informada uma consulta válida.','cliente');
        $consulta_efetuada = $this->cliente->retornar_consulta_efetuada($id_consulta_efetuada)->row();
        $cliente = $this->cliente->retornar($consulta_efetuada->id_cliente_fk)->row();

        $dados_consulta = simplexml_load_string($consulta_efetuada->retorno);

        $this->load->library('CreditoSPC');

        array_push($this->parameters['breadcrumb'],array('cliente/perfil/'.$cliente->id_cliente,'Perfil de '.$cliente->nome_ou_fantasia));
        array_push($this->parameters['breadcrumb'],array('cliente/ver_consulta/'.$consulta_efetuada->id_consulta_efetuada,'Ver Consulta'));

        $this->parameters['title'] .= ' - Perfil';
        $this->parameters['title_window'] .= ' - Perfil';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'ver_consulta','cliente'=>$cliente,'consulta'=>$dados_consulta),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function produtos_valores(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();

        $this->load->model('consulta_model','consulta');
        $consultas = $this->consulta->retornar_todos()->result();
        $consultas_cliente = $this->cliente->retornar_produto_valores_array($id_cliente);

        $this->form_validation->set_rules('id_cliente', 'Cliente', 'required');

        if($this->form_validation->run()==TRUE) {
            $consultas_selecionadas = $this->input->post('id_consulta');
            $valores = $this->input->post('valor');
            $qtds_ge = $this->input->post('qtd_ge');
            $valores_ge = $this->input->post('valor_ge');
            $this->cliente->remover_cliente_consultas($id_cliente);
            if(count($consultas_selecionadas)>0):
                foreach($consultas_selecionadas as $index => $consulta):
                    $dados = array();
                    $dados['id_cliente_fk'] = $id_cliente;
                    $dados['id_consulta_fk'] = $consulta;
                    $dados['valor'] = only_numbers($valores[$index])/100;
                    $dados['qtd_ge'] = only_numbers($qtds_ge[$index]);
                    $dados['valor_ge'] = only_numbers($valores_ge[$index])/100;
                    $this->cliente->adicionar_consulta($dados);
                endforeach;
            endif;
            set_msg('Consultas Cadastradas com sucesso','sucesso');
            redirect(current_url());
        }

        array_push($this->parameters['breadcrumb'],array('cliente/produtos_valores/'.$cliente->id_cliente,'Produtos e Valor de '.$cliente->nome_ou_fantasia));

        $this->parameters['title'] .= ' - Perfil';
        $this->parameters['title_window'] .= ' - Perfil';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'produtos_e_valores','cliente'=>$cliente,'consultas'=>$consultas,'consultas_cliente'=>$consultas_cliente),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function negativacao_visualizar(){
        $this->load->model('negativacao_model','negativacao');
        $id_negativacao = $this->verificar_parametro(3,'Não foi informada uma negativacação.');
        $negativacao = $this->negativacao->retornar($id_negativacao)->row();
        $dados_parametros = json_decode($negativacao->parametros);

        array_push($this->parameters['breadcrumb'],array('cliente/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Negativação';
        $this->parameters['title_window'] .= ' - Visualizar Negativação';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'visualizar_'.$negativacao->slug,'negativacao'=>$negativacao,'parametros'=>$dados_parametros),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function negativacao_refazer(){
        $this->load->model('negativacao_model','negativacao');
        $id_negativacao = $this->verificar_parametro(3,'Não foi informada uma negativacação.');
        $negativacao = $this->negativacao->retornar($id_negativacao)->row();
		$dados = json_decode($negativacao->parametros);
		$cliente = $this->cliente->retornar($negativacao->id_cliente_fk)->row();

        switch($negativacao->slug):
            case 'negativacaoscpcpf':
                    $this->form_validation->set_rules('cpf', 'CPF', 'required|only_numbers');
                    $this->form_validation->set_rules('nome', 'Nome', 'required');
                    $this->form_validation->set_rules('natureza', 'Natureza', 'required');
                    $this->form_validation->set_rules('vencimento_inicio', 'Vencimento Inicio', 'required');
                    $this->form_validation->set_rules('vencimento_fim', 'Vencimento Fim', 'required');
                    $this->form_validation->set_rules('parcelas', 'Parelas', 'required');
                    $this->form_validation->set_rules('valor', 'Valor', 'required');
                    $this->form_validation->set_rules('contrato', 'Contrato', 'required');
                    $this->form_validation->set_rules('data_nascimento', 'Data Nascimento', 'required');
                    $this->form_validation->set_rules('logradouro', 'Logradouro', 'required');
                    $this->form_validation->set_rules('bairro', 'Bairro', 'required');
                    $this->form_validation->set_rules('cep', 'CEP', 'required');
                    $this->form_validation->set_rules('cidade', 'Cidade', 'required');
                    $this->form_validation->set_rules('uf', 'UF', 'required');
                    if($this->form_validation->run()==TRUE) {
                        $endereco_devedor = $this->separar_logradouro_numero_scpc($this->input->post('logradouro'), $this->input->post('numero'));
                        $parametros = array();
                        $parametros['DEVEDOR_CPF'] = $this->complete(only_numbers($this->input->post('cpf')),11,'left','0');
                        $parametros['DEVEDOR_NOME'] = $this->complete($this->replaceSpecialCarac($this->input->post('nome')),60);
                        $parametros['DEVEDOR_NUMERO'] = $this->scpc_numero_endereco($endereco_devedor['numero']);
                        $parametros['DEVEDOR_COMPLEMENTO'] = $this->input->post('complemento');
                        $parametros['DEVEDOR_ENDERECO'] = $this->complete($this->replaceSpecialCarac($endereco_devedor['logradouro']),40);
                        $parametros['DEVEDOR_BAIRRO'] = $this->complete($this->replaceSpecialCarac($this->input->post('bairro')),30);
                        $parametros['DEVEDOR_CIDADE'] = $this->complete($this->replaceSpecialCarac($this->input->post('cidade')),30);
                        $parametros['DEVEDOR_UF'] = $this->input->post('uf');
                        $parametros['DEVEDOR_NASCIMENTO'] = str_replace('/','',$this->input->post('data_nascimento'));
                        $parametros['DEVEDOR_CEP'] = only_numbers($this->input->post('cep'));
                        $parametros['VALOR'] = str_replace('.','',$this->input->post('valor'));
                        $parametros['VALOR'] = $this->complete(str_replace(',','',$parametros['VALOR']),'11','left','0');
                        $parametros['CONTRATO'] = $this->complete($this->input->post('contrato'),20);
                        $parametros['PARCELAS'] = $this->complete($this->input->post('parcelas'),2,'left','0');
                        $parametros['NATUREZA_OPERACAO'] = $this->input->post('natureza');
                        $parametros['DATA_ATRASO'] = str_replace('/','',$this->input->post('vencimento_inicio'));
                        $parametros['DATA_TERMINO'] = str_replace('/','',$this->input->post('vencimento_fim'));

                        $id_consulta = $this->requisicao_negativacao($parametros, $negativacao->slug,$negativacao,$cliente);
                        set_msg('Sua Negativação foi efetuada com sucesso!','successo');
                        redirect('cliente/perfil/'.$cliente->id_cliente);
                    }
                break;
            case 'negativacaoscpcpj':
                    $this->form_validation->set_rules('cnpj', 'CNPJ', 'required|only_numbers');
                    $this->form_validation->set_rules('razao_social', 'Razão Social', 'required');
                    $this->form_validation->set_rules('natureza', 'Natureza', 'required');
                    $this->form_validation->set_rules('vencimento_inicio', 'Vencimento Inicio', 'required');
                    $this->form_validation->set_rules('vencimento_fim', 'Vencimento Fim', 'required');
                    $this->form_validation->set_rules('parcelas', 'Parelas', 'required');
                    $this->form_validation->set_rules('valor', 'Valor', 'required');
                    $this->form_validation->set_rules('contrato', 'Contrato', 'required');
                    $this->form_validation->set_rules('logradouro', 'Logradouro', 'required');
                    $this->form_validation->set_rules('bairro', 'Bairro', 'required');
                    $this->form_validation->set_rules('cep', 'CEP', 'required');
                    $this->form_validation->set_rules('cidade', 'Cidade', 'required');
                    $this->form_validation->set_rules('uf', 'UF', 'required');
                    if($this->form_validation->run()==TRUE) {
                        $endereco_devedor = $this->separar_logradouro_numero_scpc($this->input->post('logradouro'), $this->input->post('numero'));
                        $parametros = array();
                        $parametros['DEVEDOR_CNPJ'] = $this->complete(only_numbers($this->input->post('cnpj')),14,'left','0');
                        $parametros['DEVEDOR_RAZAO_SOCIAL'] = $this->complete($this->replaceSpecialCarac($this->input->post('razao_social')),60);
                        $parametros['DEVEDOR_NUMERO'] = $this->scpc_numero_endereco($endereco_devedor['numero']);
                        $parametros['DEVEDOR_COMPLEMENTO'] = $this->input->post('complemento');
                        $parametros['DEVEDOR_ENDERECO'] = $this->complete($this->replaceSpecialCarac($endereco_devedor['logradouro']),60);
                        $parametros['DEVEDOR_BAIRRO'] = $this->complete($this->replaceSpecialCarac($this->input->post('bairro')),30);
                        $parametros['DEVEDOR_CIDADE'] = $this->complete($this->replaceSpecialCarac($this->input->post('cidade')),30);
                        $parametros['DEVEDOR_UF'] = $this->input->post('uf');
                        $parametros['DEVEDOR_CEP'] = only_numbers($this->input->post('cep'));
                        $parametros['VALOR'] = str_replace('.','',$this->input->post('valor'));
                        $parametros['VALOR'] = $this->complete(str_replace(',','',$parametros['VALOR']),'14','left','0');
                        $parametros['CONTRATO'] = $this->complete($this->input->post('contrato'),20);
                        $parametros['PARCELAS'] = $this->complete($this->input->post('parcelas'),2,'left','0');
                        $parametros['NATUREZA_OPERACAO'] = $this->input->post('natureza');
                        $parametros['DATA_ATRASO'] = str_replace('/','',$this->input->post('vencimento_inicio'));
                        $parametros['DATA_TERMINO'] = str_replace('/','',$this->input->post('vencimento_fim'));

                        $id_consulta = $this->requisicao_negativacao($parametros, $negativacao->slug,$negativacao,$cliente);
                        set_msg('Sua Negativação foi efetuada com sucesso!','successo');
                        redirect('cliente/perfil/'.$cliente->id_cliente);
                    }
                break;
        endswitch;

        $this->parameters['title'] .= ' - Negativação';
        $this->parameters['title_window'] .= ' - Recriação de Negativação';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'refazer_'.$negativacao->slug,'negativacao'=>$negativacao,'cliente'=>$cliente,'devedor'=>$dados),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function negativacao_baixar(){
        $this->load->model('negativacao_model','negativacao');
        $id_negativacao = $this->verificar_parametro(3,'Não foi informada uma negativacação.');
        $negativacao = $this->negativacao->retornar($id_negativacao)->row();
        $dados_parametros = json_decode($negativacao->parametros);

        if(!empty($_POST)):
            switch($negativacao->slug):
                case 'negativacaoscpcpf':
                        $baixa = $this->negativacao->retornar_baixa('baixascpcpf')->row();

                        $negativacao_parametros = json_decode($negativacao->parametros);

                        $parametros_url = array();
                        $parametros_url['USUARIO'] = $baixa->usuario;
                        $parametros_url['SENHA'] = $baixa->senha;
                        $parametros_url['CNPJ_CREDOR'] = $this->complete($negativacao_parametros->CNPJ_CREDOR,15);
                        $parametros_url['CONTRATO'] = $this->complete($negativacao_parametros->CONTRATO,20);
                        $parametros_url['NOME'] = $this->complete($this->replaceSpecialCarac($negativacao_parametros->DEVEDOR_NOME),60);
                        $parametros_url['DATA_NASCIMENTO'] = $this->complete($negativacao_parametros->DEVEDOR_NASCIMENTO,8);
                        $parametros_url['CPF'] = $this->complete($negativacao_parametros->DEVEDOR_CPF,11);

                        $url_preparada = $this->prepara_url($baixa->requisicao, $parametros_url);
						echo $url_preparada;
						echo '<br><br>';

                        $retorno_principal = file_get_contents($url_preparada);

                        echo $retorno_principal;

                        exit;
                    break;
                case 'negativacaoscpcpj':
                        $baixa = $this->negativacao->retornar_baixa('baixascpcpj')->row();

                        $negativacao_parametros = json_decode($negativacao->parametros);

                        $parametros_url = array();
                        $parametros_url['USUARIO'] = $baixa->usuario;
                        $parametros_url['SENHA'] = $baixa->senha;
                        $parametros_url['CNPJ_CREDOR'] = $this->complete($negativacao_parametros->CNPJ_CREDOR,14);
                        $parametros_url['CONTRATO'] = $this->complete($negativacao_parametros->CONTRATO,20);
                        $parametros_url['RAZAO_SOCIAL'] = $this->complete($this->replaceSpecialCarac($negativacao_parametros->DEVEDOR_RAZAO_SOCIAL),60);
                        $parametros_url['CNPJ'] = $this->complete($negativacao_parametros->DEVEDOR_CNPJ,14,'0');
                        $parametros_url['VALOR'] = $this->complete($negativacao_parametros->VALOR,14,'0');

                        $url_preparada = $this->prepara_url($baixa->requisicao, $parametros_url);
						echo $url_preparada;
						echo '<br><br>';

                        $retorno_principal = file_get_contents($url_preparada);

                        echo $retorno_principal;

                        exit;
                    break;
            endswitch;
        endif;

        $this->parameters['title'] .= ' - Baixar';
        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'baixar_'.$negativacao->slug,'negativacao'=>$negativacao,'parametros'=>$dados_parametros),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function gerar_faturamento(){
        $id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
        $cliente = $this->cliente->retornar($id_cliente)->row();
        $faturas = $this->cliente->retornar_faturas($id_cliente)->result();

        $this->form_validation->set_rules('mes', 'Mes', 'required');

        if($this->form_validation->run()==TRUE) {
            $this->load->model("faturamento_model",'faturamento');
            $dia_start = array('15'=>'5','30'=>'20');
            $dia_vcto = $cliente->dia_vencimento;
            $dia_fat = $dia_start[$dia_vcto];
            $mes = $this->input->post('mes');
            $ano = $this->input->post('ano');

            $inicio = date('Y-m-d',strtotime($ano.'-'.$mes.'-'.$dia_fat.'-1 month')).' 00:00:00';
            $fim = $ano.'-'.$mes.'-'.$dia_fat.' 23:59:59';

            $faturamento = $this->faturamento->retornar_gerar_faturamento_individual($id_cliente,$inicio, $fim)->result();

            $fatura_atual = new stdClass();
            $fatura_atual->id_cliente = $id_cliente;
            $fatura_atual->valor = 0;
            $fatura_atual->qtd_dias_faturamento = 30;
            $fatura_atual->vencimento = $ano.'-'.$mes.'-'.$dia_vcto;
            $fatura_atual->debito = 0;
            $fatura_atual->credito = 0;
            $fatura_atual->inicio = $inicio;
            $fatura_atual->fim = $fim;
            $fatura_atual->nome = 'Mensalidade + Consumo ('.ucfirst(get_mes(date($mes))).')';
            $fatura_atual->tipo = 'Completa';
            $fatura_atual->mensalidade = $cliente->mensalidade;
            $fatura_atual->franquia = $cliente->franquia;
            $fatura_atual->valor = $cliente->mensalidade;
            $fatura_atual->consumo = 0;
            $fatura_atual->itens = array();

            foreach($faturamento as $index => $linha):
                $fatura_item_atual = new stdClass();
                $fatura_item_atual->id_cliente_fk = $linha->id_cliente_fk;
                $fatura_item_atual->nome = $linha->nome;
                if($linha->nome=="+ Crédito Pefin + Varejo") $fatura_item_atual->nome = "+ Crédito Pefin";
                if($linha->nome=="+ Crédito Pefin + Varejo PJ") $fatura_item_atual->nome = "+ Crédito Pefin PJ";
                $fatura_item_atual->descricao = $linha->entrada;
                $fatura_item_atual->grupo = $linha->grupo;
                $fatura_item_atual->valor = $linha->valor;
                $fatura_item_atual->data = $linha->data;
                $fatura_atual->consumo += $linha->valor;
                array_push($fatura_atual->itens,$fatura_item_atual);
            endforeach;

            if($fatura_atual->consumo>$fatura_atual->franquia) $fatura_atual->valor = money($fatura_atual->valor+($fatura_atual->consumo-$fatura_atual->franquia));

            $id_fatura = $this->inserir_fatura($fatura_atual);
            foreach($fatura_atual->itens as $i => $item_now):
                $this->inserir_fatura_item($item_now,$id_fatura);
            endforeach;

            redirect(current_url());
        }

        array_push($this->parameters['breadcrumb'],array('cliente/perfil/'.$cliente->nome_ou_fantasia,'Perfil de '.$cliente->nome_ou_fantasia));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['menu'] .= $this->load->view('components/menu',array('menu'=>'mais_opcoes'),true);

        $this->parameters['pg_title'] = '<i class="fa fa-file-text"></i> Gerar Faturamento de Cliente';
        $this->parameters['pg_subtitle'] = $cliente->nome_ou_fantasia;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gerar_faturamento','cliente'=>$cliente,'faturas'=>$faturas),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function gerar_faturamento_prorata(){
		$id_cliente = $this->verificar_parametro(3,'Não foi informado um cliente válido','cliente');
		$cliente = $this->cliente->retornar($id_cliente)->row();
		$faturas = $this->cliente->retornar_faturas($id_cliente)->result();

		$this->form_validation->set_rules('inicio', 'Início', 'required');
		$this->form_validation->set_rules('fim', 'Fim', 'required');
		$this->form_validation->set_rules('vencimento', 'Vencimento', 'required');

		if ($this->form_validation->run() == TRUE) {

			$this->load->model("faturamento_model", 'faturamento');

			$inicio     = data_db($this->input->post('inicio'));
			$fim        = data_db($this->input->post('fim'));
			$vencimento = data_db($this->input->post('vencimento'));

			// diferença em dias entre as datas (mantendo sua função atual)
			$dias_fat = diferenca_datas($inicio, $fim);

			// valor base da mensalidade (numérico)
			$mensalidade_base = (float) $cliente->mensalidade;

			// por padrão, assume fatura completa
			$tipo_fatura      = 'Completa';
			$mensalidade_cobrada = $mensalidade_base;

			// --- PRÓ-RATA SE PERÍODO < 30 DIAS ---
			if ($dias_fat < 30) {
				// valor diário da mensalidade (base 30 dias)
				$valor_diario = $mensalidade_base / 30;

				// mensalidade proporcional ao período informado
				$mensalidade_cobrada = $valor_diario * $dias_fat;

				// opcional: marcar o tipo da fatura como pró-rata
				$tipo_fatura = 'Pró-rata';
			}
			// se quiser também tratar > 30 dias como pró-rata "pra mais", você pode fazer:
			// else if ($dias_fat > 30) {
			//     $valor_diario = $mensalidade_base / 30;
			//     $mensalidade_cobrada = $valor_diario * $dias_fat;
			//     $tipo_fatura = 'Pró-rata (+30 dias)';
			// }

			$faturamento = $this->faturamento
				->retornar_gerar_faturamento_individual($id_cliente, $inicio, $fim)
				->result();

			$fatura_atual = new stdClass();
			$fatura_atual->id_cliente           = $id_cliente;
			$fatura_atual->qtd_dias_faturamento = $dias_fat;
			$fatura_atual->vencimento           = $vencimento;
			$fatura_atual->debito               = 0;
			$fatura_atual->credito              = 0;
			$fatura_atual->inicio               = $inicio;
			$fatura_atual->fim                  = $fim;
			$fatura_atual->nome                 = 'Consumo ('.data_pt($inicio, false).' à '.data_pt($fim, false).')';

			// agora usamos o tipo calculado
			$fatura_atual->tipo         = $tipo_fatura;

			// mantém registro da mensalidade cheia e da mensalidade efetivamente cobrada
			$fatura_atual->mensalidade  = $mensalidade_cobrada;
			$fatura_atual->franquia     = $cliente->franquia;

			// valor inicial da fatura = mensalidade (já pró-rata se for o caso)
			$fatura_atual->valor        = $mensalidade_cobrada;

			$fatura_atual->consumo      = 0;
			$fatura_atual->itens        = array();

			foreach ($faturamento as $index => $linha) :
				$fatura_item_atual = new stdClass();
				$fatura_item_atual->id_cliente_fk = $linha->id_cliente_fk;

				$fatura_item_atual->nome = $linha->nome;
				if ($linha->nome == "+ Crédito Pefin + Varejo")   $fatura_item_atual->nome = "+ Crédito Pefin";
				if ($linha->nome == "+ Crédito Pefin + Varejo PJ") $fatura_item_atual->nome = "+ Crédito Pefin PJ";

				$fatura_item_atual->descricao = $linha->entrada;
				$fatura_item_atual->grupo     = $linha->grupo;
				$fatura_item_atual->valor     = $linha->valor;
				$fatura_item_atual->data      = $linha->data;

				// soma o consumo real do período
				$fatura_atual->consumo += $linha->valor;

				$fatura_atual->itens[] = $fatura_item_atual;
			endforeach;

			// excedente de franquia continua igual:
			if ($fatura_atual->consumo > $fatura_atual->franquia) {
				$excedente = $fatura_atual->consumo - $fatura_atual->franquia;
				$fatura_atual->valor = money($fatura_atual->valor + $excedente);
			}

			$id_fatura = $this->inserir_fatura($fatura_atual);

			foreach ($fatura_atual->itens as $item_now) :
				$this->inserir_fatura_item($item_now, $id_fatura);
			endforeach;

			redirect(current_url());
		}

        array_push($this->parameters['breadcrumb'],array('cliente/perfil/'.$cliente->nome_ou_fantasia,'Perfil de '.$cliente->nome_ou_fantasia));
        $this->parameters['menu'] = $this->load->view('components/menu',array('menu'=>'cliente_perfil','cliente'=>$cliente),true);
        $this->parameters['menu'] .= $this->load->view('components/menu',array('menu'=>'mais_opcoes'),true);

        $this->parameters['pg_title'] = '<i class="fa fa-file-text"></i> Gerar Faturamento Pró-Rata de Cliente';
        $this->parameters['pg_subtitle'] = $cliente->nome_ou_fantasia;

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'gerar_faturamento_prorata','cliente'=>$cliente,'faturas'=>$faturas),true);
        $this->load->view('templates/maing',$this->parameters);
    }
    public function gerar_boleto_via_fatura(){
        $this->load->model('fatura_model','fatura');
        $id_fatura = $this->verificar_parametro(3,'Não foi informada uma fatura válida.','cliente');
        $fatura = $this->fatura->retornar($id_fatura)->row();
        
        $id_cliente = $fatura->id_cliente_fk;
        $vencimento = $fatura->vencimento;
        $valor = $fatura->valor;

        $outros = array();
        $outros['descricao_boleto'] = $fatura->nome;

        $this->load->model('boleto_model','boleto');
        //if($this->boleto->criar($id_cliente,$valor,$vencimento,$outros)){
		$this->load->model('boletoV3_model','boletov3');
		if($this->boletov3->newBoleto($id_cliente, $valor, $vencimento, $outros)){
            $ultimo_boleto = $this->boleto->retornar_ultimo_boleto()->row();
            $this->fatura->alterar($fatura->id_fatura,array('faturado'=>1,'id_boleto_fk'=>$ultimo_boleto->id_boleto,'hash_boleto'=>$ultimo_boleto->hash));
        }else{
            set_msg('Ocorreu um erro ao gerar o boleto.');
        }
        redirect('cliente/gerar_faturamento/'.$id_cliente);
    }
    private function inserir_fatura($fatura){
        $dados = array();
        $dados['id_cliente_fk'] = $fatura->id_cliente;
        $dados['nome']          = $fatura->nome;
        $dados['inicio']        = $fatura->inicio;
        $dados['fim']           = $fatura->fim;
        $dados['tipo']          = $fatura->tipo;
        $dados['mensalidade']   = $fatura->mensalidade;
        $dados['franquia']      = $fatura->franquia;
        $dados['consumo']       = $fatura->consumo;
        $dados['valor']         = $fatura->valor;
        $dados['vencimento']    = $fatura->vencimento;
        $dados['credito']       = $fatura->credito;
        $dados['debito']        = $fatura->debito;

        $this->faturamento->inserir_fatura($dados);
        return $this->faturamento->id_ultima_fatura();
    }
    private function inserir_fatura_item($item,$id_fatura){
        $dados = array();
        $dados['id_fatura_fk']  = $id_fatura;
        $dados['id_cliente_fk'] = $item->id_cliente_fk;
        $dados['nome']          = $item->nome;
        $dados['descricao']     = $item->descricao;
        $dados['grupo']         = $item->grupo;
        $dados['valor']         = $item->valor;
        $dados['data']          = $item->data;

        $this->faturamento->inserir_fatura_item($dados);
    }
    private function requisicao_negativacao($parametros,$slug,$negativacao,$cliente){
        $consulta = $this->cliente->retornar_consulta_mais_barata($slug,$cliente->id_cliente)->row();

        $parametros['CHAVE'] = $consulta->chave;
        $parametros['USUARIO'] = $consulta->usuario;
        $parametros['SENHA'] = $consulta->senha;

        $parametros['CNPJ_CREDOR'] = $cliente->cpf_cnpj;
        $parametros['RAZAO_CREDOR'] = $this->replaceSpecialCarac($cliente->razao_social);
        $telefone_credor = $this->telefone_credor_normalizado($cliente);

        $parametros['FANTASIA_CREDOR'] = $cliente->nome_ou_fantasia;
        $parametros['TELEFONE_CREDOR'] = $telefone_credor['telefone'];
        $parametros['EMAIL_CREDOR'] = $cliente->email;
        $parametros['CEP_CREDOR'] = $cliente->cep;
        $parametros['ENDERECO_CREDOR'] = $this->replaceSpecialCarac($cliente->logradouro);
        $parametros['NUMERO_ENDERECO_CREDOR'] = $cliente->numero;
        $parametros['COMPLEMENTO_ENDERECO_CREDOR'] = $cliente->complemento;
        $parametros['BAIRRO_CREDOR'] = $this->replaceSpecialCarac($cliente->bairro);
        $parametros['CIDADE_CREDOR'] = $this->replaceSpecialCarac($cliente->cidade);
        $parametros['UF_CREDOR'] = $cliente->uf;
        $parametros['DDD_CREDOR'] = $this->complete($telefone_credor['ddd'],4,'left','0');
        $parametros['TELEFONE_CREDOR'] = $this->complete($telefone_credor['telefone'],9,'left','0');

        if($slug=="negativacaoscpcpf"||$slug=="negativacaoscpcpj"){
            if($slug=="negativacaoscpcpf"){
                $parametros['CNPJ_CREDOR'] = $this->complete($parametros['CNPJ_CREDOR'],15,'left','0');
            }else{
                $parametros['CNPJ_CREDOR'] = $this->complete($parametros['CNPJ_CREDOR'],14,'left','0');
            }

            $parametros['RAZAO_CREDOR'] = $this->complete($parametros['RAZAO_CREDOR'],60);
            $parametros['ENDERECO_CREDOR'] = $this->complete($this->replaceSpecialCarac($cliente->logradouro),40);
            $parametros['BAIRRO_CREDOR'] = $this->complete($this->replaceSpecialCarac($cliente->bairro),30);
            $parametros['CIDADE_CREDOR'] = $this->complete($this->replaceSpecialCarac($cliente->cidade),30);
        }

        if($slug=="negativacaoscpcpf"||$slug=="negativacaoscpcpj"){
            $url_preparada = $this->montar_url_negativacao_scpc($slug, $parametros);
        }else{
            $url_preparada = $this->prepara_url($consulta->requisicao,$parametros);

            if(!isset($parametros['NOME_PAI'])) $url_preparada = str_replace('&nome_pai={{NOME_PAI}}','',$url_preparada);
            if(!isset($parametros['NOME_MAE'])) $url_preparada = str_replace('&nome_mae={{NOME_MAE}}','',$url_preparada);
        }

        $milis_atual = strtotime(date('Y-m-d H:i:s'));
        $dados_consulta_efetuada = array();
        $dados_consulta_efetuada['id_usuario_fk'] = $negativacao->id_usuario_fk;
        $dados_consulta_efetuada['id_cliente_fk'] = $negativacao->id_cliente_fk;
        $dados_consulta_efetuada['requisicao'] = $url_preparada;
		$dados_consulta_efetuada['contrato'] = $parametros['CONTRATO'];
        if(isset($parametros['CPF_DEVEDOR'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['CPF_DEVEDOR'];
        if(isset($parametros['DEVEDOR_CPF'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['DEVEDOR_CPF'];
        if(isset($parametros['CNPJ_DEVEDOR'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['CNPJ_DEVEDOR'];
        if(isset($parametros['DEVEDOR_CNPJ'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['DEVEDOR_CNPJ'];
        $dados_consulta_efetuada['parametros'] = json_encode($parametros);
        $dados_consulta_efetuada['custo'] = $negativacao->custo;
        $dados_consulta_efetuada['valor'] = $negativacao->valor;
        $dados_consulta_efetuada['slug'] = $consulta->consulta_slug;
        $dados_consulta_efetuada['fornecedor'] = $consulta->fornecedor;
        $dados_consulta_efetuada['criado_em'] = $negativacao->criado_em;
        $dados_consulta_efetuada['recriacao'] = date('Y-m-d H:i:s');

        $retorno_principal = @file_get_contents($url_preparada);
        if($retorno_principal === false || trim($retorno_principal) === ''){
            set_msg($this->mensagem_erro_negativacao('fornecedor nao retornou uma resposta valida.', $url_preparada));
            redirect(current_url());
        }
        if($slug!="negativacaoscpcpf"&&$slug!="negativacaoscpcpj"){
            $retorno_array = simplexml_load_string($retorno_principal);
            $retorno_json = json_encode($retorno_array);
        }else{
            $retorno_json = '{}';
            $retorno_scpc = trim($retorno_principal);
            if (strpos($retorno_scpc, 'ERRO') !== false || substr($retorno_scpc, 0, 1) === '3'){
                set_msg($this->mensagem_erro_negativacao($retorno_principal, $url_preparada));
                redirect(current_url());
            }else{
                $this->cliente->atualizar_negativacao($negativacao->id_negativacao,array('id_cliente_fk'=>2,'id_usuario_fk'=>1));
            }
        }
        $dados_consulta_efetuada['retorno'] = $retorno_principal;
        $dados_consulta_efetuada['retorno_json'] = $retorno_json;
        $dados_consulta_efetuada['tempo_retorno'] = strtotime(date('Y-m-d H:i:s')) - $milis_atual;
        if(isset($retorno_array->id_consulta)) $dados_consulta_efetuada['id_consulta'] = $retorno_array->id_consulta;
        // Inserção no banco de dados
        $this->cliente->inserir_negativacao($dados_consulta_efetuada);
        $id_consulta = $this->cliente->retornar_id_ultima_negativacao($negativacao->id_cliente_fk,$negativacao->id_usuario_fk);
        return $id_consulta;
    }

    private function montar_url_negativacao_scpc($slug, &$parametros){
        if($slug=="negativacaoscpcpf"){
            $parametros['TIPO_DEVEDOR'] = isset($parametros['TIPO_DEVEDOR']) ? $parametros['TIPO_DEVEDOR'] : 'C';
            $parametros['DEVEDOR_MAE'] = isset($parametros['DEVEDOR_MAE']) ? $parametros['DEVEDOR_MAE'] : '';
            $parametros['DEVEDOR_DDD'] = isset($parametros['DEVEDOR_DDD']) ? $parametros['DEVEDOR_DDD'] : '';
            $parametros['DEVEDOR_TELEFONE'] = isset($parametros['DEVEDOR_TELEFONE']) ? $parametros['DEVEDOR_TELEFONE'] : '';

            $campos = array(
                'codigo' => $this->scpc_texto($parametros['USUARIO'], 5),
                'senha' => $this->scpc_texto($parametros['SENHA'], 5),
                'cnpj_empresa' => $this->scpc_numero($parametros['CNPJ_CREDOR'], 15),
                'nome_empresa' => $this->scpc_texto($parametros['RAZAO_CREDOR'], 60),
                'endereco_empresa' => $this->scpc_texto($parametros['ENDERECO_CREDOR'], 40),
                'bairro_empresa' => $this->scpc_texto($parametros['BAIRRO_CREDOR'], 30),
                'cidade_empresa' => $this->scpc_texto($parametros['CIDADE_CREDOR'], 30),
                'estado_empresa' => strtoupper($this->scpc_texto($parametros['UF_CREDOR'], 2)),
                'cep_empresa' => $this->scpc_numero($parametros['CEP_CREDOR'], 8),
                'ddd_empresa' => $this->scpc_numero($parametros['DDD_CREDOR'], 4),
                'telefone_empresa' => $this->scpc_numero($parametros['TELEFONE_CREDOR'], 9),
                'tipo_devedor' => $this->scpc_texto($parametros['TIPO_DEVEDOR'], 1),
                'nome' => $this->scpc_texto($parametros['DEVEDOR_NOME'], 60),
                'cpf' => $this->scpc_numero($parametros['DEVEDOR_CPF'], 11),
                'data_nascimento' => $this->scpc_data($parametros['DEVEDOR_NASCIMENTO']),
                'nome_mae' => $this->scpc_texto($parametros['DEVEDOR_MAE'], 60),
                'ddd' => $this->scpc_numero($parametros['DEVEDOR_DDD'], 4),
                'telefone' => $this->scpc_numero($parametros['DEVEDOR_TELEFONE'], 9),
                'endereco' => $this->scpc_texto($parametros['DEVEDOR_ENDERECO'], 40),
                'numero' => $this->scpc_numero_endereco(isset($parametros['DEVEDOR_NUMERO']) ? $parametros['DEVEDOR_NUMERO'] : ''),
                'complemento' => $this->scpc_texto(isset($parametros['DEVEDOR_COMPLEMENTO']) ? $parametros['DEVEDOR_COMPLEMENTO'] : '', 20),
                'bairro' => $this->scpc_texto($parametros['DEVEDOR_BAIRRO'], 30),
                'municipio' => $this->scpc_texto($parametros['DEVEDOR_CIDADE'], 30),
                'estado' => strtoupper($this->scpc_texto($parametros['DEVEDOR_UF'], 2)),
                'cep' => $this->scpc_numero($parametros['DEVEDOR_CEP'], 8),
                'natureza_operacao' => $this->scpc_numero($parametros['NATUREZA_OPERACAO'], 2),
                'contrato' => $this->scpc_texto($parametros['CONTRATO'], 20),
                'parcelas' => $this->scpc_numero($parametros['PARCELAS'], 2),
                'valor' => $this->scpc_numero($parametros['VALOR'], 11),
                'data_atraso' => $this->scpc_data($parametros['DATA_ATRASO']),
                'data_termino' => $this->scpc_data($parametros['DATA_TERMINO']),
            );

            return $this->scpc_url('http://www.3ccomunicacao.com.br/webservice/inclusao/pf/string.php', $campos);
        }

        $parametros['TIPO_DEVEDOR'] = isset($parametros['TIPO_DEVEDOR']) ? $parametros['TIPO_DEVEDOR'] : 'D';
        $parametros['DEVEDOR_DDD'] = isset($parametros['DEVEDOR_DDD']) ? $parametros['DEVEDOR_DDD'] : '';
        $parametros['DEVEDOR_TELEFONE'] = isset($parametros['DEVEDOR_TELEFONE']) ? $parametros['DEVEDOR_TELEFONE'] : '';

        $campos = array(
            'codigo' => $this->scpc_texto($parametros['USUARIO'], 10),
            'senha' => $this->scpc_texto($parametros['SENHA'], 10),
            'cnpj_empresa' => $this->scpc_numero($parametros['CNPJ_CREDOR'], 14),
            'nome_empresa' => $this->scpc_texto($parametros['RAZAO_CREDOR'], 60),
            'endereco_empresa' => $this->scpc_texto($parametros['ENDERECO_CREDOR'], 40),
            'bairro_empresa' => $this->scpc_texto($parametros['BAIRRO_CREDOR'], 30),
            'cidade_empresa' => $this->scpc_texto($parametros['CIDADE_CREDOR'], 30),
            'estado_empresa' => strtoupper($this->scpc_texto($parametros['UF_CREDOR'], 2)),
            'cep_empresa' => $this->scpc_numero($parametros['CEP_CREDOR'], 8),
            'ddd_empresa' => $this->scpc_numero($parametros['DDD_CREDOR'], 4),
            'telefone_empresa' => $this->scpc_numero($parametros['TELEFONE_CREDOR'], 9),
            'tipo_devedor' => $this->scpc_texto($parametros['TIPO_DEVEDOR'], 1),
            'nome' => $this->scpc_texto($parametros['DEVEDOR_RAZAO_SOCIAL'], 60),
            'cnpj' => $this->scpc_numero($parametros['DEVEDOR_CNPJ'], 14),
            'ddd' => $this->scpc_numero($parametros['DEVEDOR_DDD'], 3),
            'telefone' => $this->scpc_numero($parametros['DEVEDOR_TELEFONE'], 8),
            'endereco' => $this->scpc_texto($parametros['DEVEDOR_ENDERECO'], 40),
            'numero' => $this->scpc_numero_endereco(isset($parametros['DEVEDOR_NUMERO']) ? $parametros['DEVEDOR_NUMERO'] : ''),
            'complemento' => $this->scpc_texto(isset($parametros['DEVEDOR_COMPLEMENTO']) ? $parametros['DEVEDOR_COMPLEMENTO'] : '', 20),
            'bairro' => $this->scpc_texto($parametros['DEVEDOR_BAIRRO'], 30),
            'municipio' => $this->scpc_texto($parametros['DEVEDOR_CIDADE'], 30),
            'estado' => strtoupper($this->scpc_texto($parametros['DEVEDOR_UF'], 2)),
            'cep' => $this->scpc_numero($parametros['DEVEDOR_CEP'], 8),
            'natureza_operacao' => $this->scpc_numero($parametros['NATUREZA_OPERACAO'], 2),
            'razao_devedor' => $this->scpc_texto($parametros['DEVEDOR_RAZAO_SOCIAL'], 60),
            'titulo' => $this->scpc_texto($parametros['CONTRATO'], 20),
            'parcelas' => $this->scpc_numero($parametros['PARCELAS'], 2),
            'valor' => $this->scpc_numero($parametros['VALOR'], 14),
            'data_atraso' => $this->scpc_data($parametros['DATA_ATRASO']),
            'data_termino' => $this->scpc_data($parametros['DATA_TERMINO']),
        );

        return $this->scpc_url('http://www.3ccomunicacao.com.br/webservice/inclusao/pj/string.php', $campos);
    }

    private function scpc_url($base, $campos){
        return $base.'?'.http_build_query($campos, '', '&');
    }

    private function scpc_texto($valor, $tamanho){
        $valor = $this->replaceSpecialCarac((string) $valor);
        $valor = preg_replace('/[^A-Za-z0-9 ]/', ' ', $valor);
        $valor = preg_replace('/\s+/', ' ', trim($valor));
        return str_pad(substr($valor, 0, $tamanho), $tamanho, ' ', STR_PAD_RIGHT);
    }

    private function scpc_numero($valor, $tamanho){
        $valor = only_numbers((string) $valor);
        return str_pad(substr($valor, -$tamanho), $tamanho, '0', STR_PAD_LEFT);
    }

    private function scpc_data($valor){
        return $this->scpc_numero($valor, 8);
    }

    private function scpc_numero_endereco($valor){
        $valor = trim((string) $valor);
        if($valor === ''){
            return 'S/N';
        }

        $numero = only_numbers($valor);
        return $numero !== '' ? $numero : 'S/N';
    }

    private function montar_endereco_scpc($logradouro, $numero, $complemento=''){
        $partes = array(trim((string) $logradouro), trim((string) $numero), trim((string) $complemento));
        $endereco = implode(' ', array_filter($partes, function($parte){
            return $parte !== '';
        }));

        $endereco = $this->replaceSpecialCarac($endereco);
        $endereco = preg_replace('/\s+/', ' ', trim($endereco));
        return $endereco;
    }

    private function separar_logradouro_numero_scpc($logradouro, $numero){
        $logradouro = trim((string) $logradouro);
        $numero = trim((string) $numero);

        if($numero === '' && preg_match('/^(.*?)[,\s]+(\d+[A-Za-z]?)$/', $logradouro, $match)){
            $logradouro = trim($match[1]);
            $numero = $match[2];
        }

        return array('logradouro' => $logradouro, 'numero' => $numero);
    }

    private function mensagem_erro_negativacao($erro, $url){
        $mensagem = 'Ocorreu um erro na negativacao: '.$erro;
        if($this->input->get('debug_url') == '1'){
            $mensagem .= '<br><small style="word-break: break-all;"><b>URL chamada:</b> '.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'</small>';
        }

        return $mensagem;
    }
    public function redefinir_senha(){

    }
    public function negativacao_mover(){

    }
    public function negativacao_lista(){
        $this->load->model('negativacao_model','negativacao');
        $negativacoes = $this->negativacao->retornar_pefin()->result();

        echo '<table><tbody></tbody>';
        foreach($negativacoes  as $index => $negativacao):
            $dt = json_decode($negativacao->parametros);
            echo '<tr>';
                echo '<td>'.$negativacao->cliente.'</td>';
                if(isset($dt->CPF_DEVEDOR)) echo '<td>'.$dt->CPF_DEVEDOR.'</td>'; else echo '<td>'.$dt->CNPJ_DEVEDOR.'</td>';
                echo '<td>'.$dt->NOME_DEVEDOR.'</td>';
                echo '<td>'.$dt->VENCIMENTO_DIVIDA.'</td>';
                echo '<td>'.$dt->VALOR_DIVIDA.'</td>';
            echo '</tr>';
        endforeach;
        echo '</table></table>';
    }

    public function pastas(){
        $pastas = $this->cliente->clientes_pastas()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-folder"></i> Pasta de Cliente';
        $this->parameters['pg_subtitle'] = 'Controle de Pastas de Clientes para emissão de relatórios';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'pastas','pastas'=>$pastas),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function pasta_cadastrar(){
        $this->form_validation->set_rules('nome', 'descricao', 'required');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','descricao'),$this->input->post());

            if($this->cliente->cliente_pasta_cadastrar($dados)){
                set_msg('Pasta Cadastrada com sucesso.','sucesso');
                redirect('cliente/pastas');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar a Pasta.');
                redirect(current_url());
            }
        }

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Cadastro de Pasta de Cliente';
        $this->parameters['pg_subtitle'] = 'Cadastro de Pasta de Cliente';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'pasta_cadastrar'),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function pasta_subs(){
        $id_pasta = $this->uri->segment(3);
        $clientes = $this->cliente->retornar_todos()->result();

        $this->form_validation->set_rules('cliente', 'Cliente', 'required');

        if($this->form_validation->run()==TRUE) {
            $id_cliente = $this->input->post('cliente');
            $dados = array('id_cliente_fk'=>$id_cliente,'id_cliente_pasta_fk'=>$id_pasta);
            if($this->cliente->cliente_pasta_sub_cadastrar($dados)){
                set_msg('Inscrição Efetuada com sucesso.','sucesso');
                redirect('cliente/pasta_subs/'.$id_pasta);
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar a Pasta.');
                redirect(current_url());
            }
        }

        $subs = $this->cliente->cliente_pasta_subs($id_pasta)->result();

        $this->parameters['pg_title'] = '<i class="fa fa-plus"></i> Inscrições em Pastas de Clientes';
        $this->parameters['pg_subtitle'] = 'Inscrição de cliente em Pasta de Clientes';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'pasta_subs','clientes'=>$clientes,'subs'=>$subs),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function pasta_relatorio_valores(){
        $id_pasta = $this->uri->segment(3);

        $this->form_validation->set_rules('data', 'Data', 'required');

        if($this->form_validation->run()==TRUE) {
            $dt = $this->input->post('data');
            $inicio = data_db('01/'.$dt,false);
            $fim = data_db('31/'.$dt,false);
            $ids = array();
            $subs = $this->cliente->cliente_pasta_subs($id_pasta)->result();
            foreach($subs as $i => $s): array_push($ids, $s->id_cliente_fk); endforeach;
            $boletos = $this->cliente->cliente_pasta_boletos_de_ids($ids, $inicio, $fim)->result();
            $this->relatorio_csv_download($boletos);
            exit;
        }

        $this->parameters['pg_title'] = '<i class="fa fa-file-text"></i> Relatório de Valores';
        $this->parameters['pg_subtitle'] = 'Relatório com os dados do cliente';

        $this->parameters['menu'] = $this->load_menu('clientes');

        $this->parameters['content'] = $this->load->view('screens/cliente',array('content'=>'pasta_relatorio_valores'),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    private function relatorio_csv_download($boletos){
        $f = fopen('php://memory', 'w');
        foreach ($boletos as $i => $b) {
            fputcsv($f, array('nome'=>$b->nome_sacado, 'valor'=>dinheiro($b->valor_boleto)),';');
        }
        fseek($f, 0);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="relatorio.csv";');
        fpassthru($f);
    }

    private function replaceSpecialCarac($str) {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
        //$str = preg_replace('/[^a-z0-9]/i', '_', $str);
        //$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
        return $str;
    }
    private function telefone_credor_normalizado($cliente){
        $candidatos = array(
            isset($cliente->celular) ? $cliente->celular : '',
            isset($cliente->telefone) ? $cliente->telefone : '',
            isset($cliente->telefone2) ? $cliente->telefone2 : '',
            isset($cliente->celular2) ? $cliente->celular2 : '',
        );

        foreach($candidatos as $telefone){
            $digitos = preg_replace('/\D+/', '', (string) $telefone);
            if(strlen($digitos) >= 11){
                return array(
                    'ddd' => substr($digitos, 0, 2),
                    'telefone' => substr($digitos, 2, 9),
                );
            }
        }

        foreach($candidatos as $telefone){
            $digitos = preg_replace('/\D+/', '', (string) $telefone);
            if(strlen($digitos) >= 10){
                return array(
                    'ddd' => substr($digitos, 0, 2),
                    'telefone' => substr($digitos, 2, 8),
                );
            }
        }

        return array('ddd' => '', 'telefone' => '');
    }
    private function complete($string,$tamanho,$side='right',$val=' '){
        $retorno = $string;
        $tamanho_string = strlen($retorno);
        if($tamanho_string<$tamanho){
            while($tamanho_string<$tamanho){
                if($side=='right') $retorno =  $retorno.$val;
                else $retorno = $val.$retorno;
                $tamanho_string = strlen($retorno);
            }
        }else{
            if($tamanho_string>$tamanho){
                $retorno = substr($string,0,$tamanho-1);
            }
        }
        return $retorno;
    }
    private function prepara_url($url,$parametros){
        $retorno = $url;
        foreach($parametros as $index => $parametro):
            $retorno = str_replace('{{'.$index.'}}',urlencode($parametro),$retorno);
        endforeach;
        return $retorno;
    }
}
