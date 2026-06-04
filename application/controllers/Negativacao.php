<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Negativacao extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('negativacao_model', 'negativacao');
    }

    public function index(){
        $limite = (int) $this->input->get('limite');
        if($limite <= 0 || $limite > 200){
            $limite = 200;
        }

        $pagina = (int) $this->input->get('pagina');
        if($pagina <= 0){
            $pagina = 1;
        }

        $busca = trim((string) $this->input->get('busca'));
        $offset = ($pagina - 1) * $limite;
        $total = $this->negativacao->contar_todas($busca);
        $negativacoes = $this->negativacao->listar_todas($limite, $offset, $busca)->result();

        foreach($negativacoes as $negativacao){
            $negativacao->_status_negativacao = $this->status_negativacao($negativacao);
            $negativacao->_status_baixa = $this->status_baixa($negativacao);
        }

        $this->parameters['pg_title'] = 'Negativacoes';
        $this->parameters['pg_subtitle'] = 'Acompanhamento de inclusoes e baixas';
        $this->parameters['content'] = $this->load->view('screens/negativacao', array(
            'content' => 'lista',
            'negativacoes' => $negativacoes,
            'total' => $total,
            'pagina' => $pagina,
            'limite' => $limite,
            'busca' => $busca,
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function dossie(){
        $id_negativacao = $this->verificar_parametro(3, 'Negativacao nao informada.', 'negativacao');
        $negativacao = $this->negativacao->retornar_dossie($id_negativacao)->row();

        if($negativacao === null){
            set_msg('Negativacao nao encontrada.');
            redirect('negativacao');
        }

        $negativacao->_status_negativacao = $this->status_negativacao($negativacao);
        $negativacao->_status_baixa = $this->status_baixa($negativacao);

        $baixas = $this->negativacao->retornar_baixas_da_negativacao($negativacao)->result();
        foreach($baixas as $baixa){
            $baixa->_status_baixa = $this->status_negativacao($baixa);
            $baixa->_auditorias = $this->negativacao->retornar_auditorias('negativacao_baixa', $baixa->id_negativacao_baixa)->result();
        }

        $auditorias = $this->negativacao->retornar_auditorias('negativacao', $id_negativacao)->result();

        $this->parameters['pg_title'] = 'Dossie da negativacao';
        $this->parameters['pg_subtitle'] = '#'.$id_negativacao;
        $this->parameters['content'] = $this->load->view('screens/negativacao', array(
            'content' => 'dossie',
            'negativacao' => $negativacao,
            'baixas' => $baixas,
            'auditorias' => $auditorias,
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    private function status_baixa($negativacao){
        if(isset($negativacao->baixas_qtd) && (int) $negativacao->baixas_qtd > 0){
            return array('texto' => 'Baixada', 'classe' => 'success', 'icone' => 'check');
        }

        return array('texto' => 'Nao baixada', 'classe' => 'default', 'icone' => 'clock-o');
    }

    private function status_negativacao($linha){
        $retorno = '';
        if(isset($linha->retorno) && $linha->retorno !== null){
            $retorno .= ' '.$linha->retorno;
        }
        if(isset($linha->retorno_json) && $linha->retorno_json !== null){
            $retorno .= ' '.$linha->retorno_json;
        }
        if(isset($linha->status) && $linha->status !== null){
            $retorno .= ' '.$linha->status;
        }

        $retorno = trim($retorno);
        if($retorno === ''){
            return array('texto' => 'Sem retorno', 'classe' => 'warning', 'icone' => 'exclamation-triangle');
        }

        if($this->retorno_tem_erro($retorno)){
            return array('texto' => 'Erro', 'classe' => 'danger', 'icone' => 'times');
        }

        return array('texto' => 'Sucesso', 'classe' => 'success', 'icone' => 'check');
    }

    private function retorno_tem_erro($retorno){
        $texto = strtolower($retorno);
        $marcadores = array('erro_', ' erro ', 'error', 'exception', 'http_400', 'http_500', 'nao foi possivel', 'invalid');
        foreach($marcadores as $marcador){
            if(strpos($texto, $marcador) !== false){
                return true;
            }
        }

        $json = json_decode($retorno);
        if(json_last_error() === JSON_ERROR_NONE && is_object($json)){
            if(isset($json->success) && !$json->success){
                return true;
            }
            if(isset($json->valido) && !$json->valido){
                return true;
            }
            if(isset($json->erro) && $json->erro){
                return true;
            }
            if(isset($json->HEADER->INFORMACOES_RETORNO->STATUS_RETORNO->CODIGO)
                && (string) $json->HEADER->INFORMACOES_RETORNO->STATUS_RETORNO->CODIGO === '0'){
                return true;
            }
        }

        return false;
    }

    public function pefin_ativo(){
        $negativacoes = $this->negativacao->retornar_pefin_ativo()->result();
        $this->parameters['content'] = $this->load->view('screens/negativacao',array('content'=>'index','negativacoes'=>$negativacoes),true);
        $this->load->view('templates/main_sem_janela',$this->parameters);
    }
    public function conversao(){
        $this->load->model('cliente_model','cliente');

        $id_negativacao = $this->uri->segment(3);
        $negativacao = $this->negativacao->retornar($id_negativacao)->row();
        $dados = json_decode($negativacao->parametros);
        $cliente = $this->cliente->retornar($negativacao->id_cliente_fk)->row();

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
            $parametros = array();
            $parametros['DEVEDOR_CPF'] = $this->complete($this->input->post('cpf'),11,'left','0');
            $parametros['DEVEDOR_NOME'] = $this->complete($this->replaceSpecialCarac($this->input->post('nome')),60);
            $parametros['DEVEDOR_ENDERECO'] = $this->complete($this->replaceSpecialCarac($this->input->post('logradouro').' '.$this->input->post('numero').' '.$this->input->post('complemento')),40);
            $parametros['DEVEDOR_BAIRRO'] = $this->complete($this->replaceSpecialCarac($this->input->post('bairro')),30);
            $parametros['DEVEDOR_CIDADE'] = $this->complete($this->replaceSpecialCarac($this->input->post('cidade')),30);
            $parametros['DEVEDOR_UF'] = $this->input->post('uf');
            $parametros['DEVEDOR_NASCIMENTO'] = str_replace('/','',$this->input->post('data_nascimento'));
            $parametros['DEVEDOR_CEP'] = $this->input->post('cep');
            $parametros['VALOR'] = str_replace('.','',$this->input->post('valor'));
            $parametros['VALOR'] = $this->complete(str_replace(',','',$parametros['VALOR']),'11','left','0');
            $parametros['CONTRATO'] = $this->complete($this->input->post('contrato'),20);
            $parametros['PARCELAS'] = $this->complete($this->input->post('parcelas'),2,'left','0');
            $parametros['NATUREZA_OPERACAO'] = $this->input->post('natureza');
            $parametros['DATA_ATRASO'] = str_replace('/','',$this->input->post('vencimento_inicio'));
            $parametros['DATA_TERMINO'] = str_replace('/','',$this->input->post('vencimento_fim'));

            $id_consulta = $this->requisicao_negativacao($parametros, $negativacao, $cliente, 'negativacaoscpcpf');
            set_msg('Sua Negativação foi efetuada com sucesso!','successo');
            redirect('negativacao');
        }

        $this->parameters['content'] = $this->load->view('screens/negativacao', array('content' => 'conversao', 'negativacao' => $negativacao,'cliente'=>$cliente,'devedor'=>$dados), true);
        $this->load->view('templates/main_sem_janela', $this->parameters);
    }

    private function requisicao_negativacao($parametros, $negativacao, $cliente, $slug='negativacaopefinpf'){
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

        $url_preparada = $this->prepara_url($consulta->requisicao,$parametros);

        if(!isset($parametros['NOME_PAI'])) $url_preparada = str_replace('&nome_pai={{NOME_PAI}}','',$url_preparada);
        if(!isset($parametros['NOME_MAE'])) $url_preparada = str_replace('&nome_mae={{NOME_MAE}}','',$url_preparada);
        if(!isset($parametros['DEVEDOR_MAE'])) $url_preparada = str_replace('&nome_mae={{DEVEDOR_MAE}}','&nome_mae=++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++',$url_preparada);

        if($slug=="negativacaoscpcpf"||$slug=="negativacaoscpcpj"){
            if($slug=="negativacaoscpcpf"){
                if(!isset($parametros['DEVEDOR_DDD'])) $url_preparada = str_replace('&ddd={{DEVEDOR_DDD}}','&ddd=0000',$url_preparada);
                if(!isset($parametros['DEVEDOR_TELEFONE'])) $url_preparada = str_replace('&telefone={{DEVEDOR_TELEFONE}}','&telefone=000000000',$url_preparada);
            }else{
                if(!isset($parametros['DEVEDOR_DDD'])) $url_preparada = str_replace('&ddd={{DEVEDOR_DDD}}','&ddd=000',$url_preparada);
                if(!isset($parametros['DEVEDOR_TELEFONE'])) $url_preparada = str_replace('&telefone={{DEVEDOR_TELEFONE}}','&telefone=00000000',$url_preparada);
            }
        }

        $milis_atual = strtotime(date('Y-m-d H:i:s'));
        $dados_consulta_efetuada = array();
        $dados_consulta_efetuada['id_usuario_fk'] = $negativacao->id_usuario_fk;
        $dados_consulta_efetuada['id_cliente_fk'] = $negativacao->id_cliente_fk;
        $dados_consulta_efetuada['requisicao'] = $url_preparada;
        if(isset($parametros['CPF_DEVEDOR'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['CPF_DEVEDOR'];
        if(isset($parametros['DEVEDOR_CPF'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['DEVEDOR_CPF'];
        if(isset($parametros['CNPJ_DEVEDOR'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['CNPJ_DEVEDOR'];
        if(isset($parametros['DEVEDOR_CNPJ'])) $dados_consulta_efetuada['cpf_cnpj'] = $parametros['DEVEDOR_CNPJ'];
        $dados_consulta_efetuada['parametros'] = json_encode($parametros);
        $dados_consulta_efetuada['custo'] = $consulta->custo;
        $dados_consulta_efetuada['valor'] = $consulta->venda;
        $dados_consulta_efetuada['slug'] = $consulta->consulta_slug;
        $dados_consulta_efetuada['fornecedor'] = $consulta->fornecedor;
        $dados_consulta_efetuada['criado_em'] = $negativacao->criado_em;
        $dados_consulta_efetuada['recriacao'] = date('Y-m-d H:i:s');

        $retorno_principal = file_get_contents($url_preparada);
        if($slug!="negativacaoscpcpf"&&$slug!="negativacaoscpcpj"){
            $retorno_array = simplexml_load_string($retorno_principal);
            $retorno_json = json_encode($retorno_array);
        }else{
            $retorno_json = '{}';
            if (strpos($retorno_principal, 'ERRO') !== false){
                set_msg('Ocorreu um erro na negativação: '.$retorno_principal);
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
    public function negativacoes_pdf(){
        $this->load->model('negativacao_model','negativacao');
        $negativacoes = $this->negativacao->retornar_negativacoes()->result();

        $this->load->view('components/relatorio_negativacao',array('negativacoes'=>$negativacoes));
    }
}
