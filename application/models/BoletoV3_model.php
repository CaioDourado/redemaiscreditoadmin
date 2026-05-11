<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once APPPATH.'config/env.php';

class BoletoV3_model extends CI_Model{
    private $last_http_status = null;
    private $last_curl_error = null;

    public function inserirRetorno($dados=null){
        if($dados!==null){
            $this->db->insert('retorno', $dados);
            return $this->db->affected_rows()>0;
        }
        return false;
    }

    public function inserirRetornoReq($dados=null){
        if($dados!==null){
            $this->db->insert('retorno_req', $dados);
            return $this->db->affected_rows()>0;
        }
        return false;
    }

    public function updateBoleto($dados=null, $condicao=null){
        if($dados!==null) {
            $this->db->update('boleto', $dados, $condicao);
            return $this->db->affected_rows()>0;
        }
        return false;
    }

    public function getRetornoReq(){
        return $this->db->query('SELECT * FROM retorno_req ORDER BY criado_em DESC, id_retorno_req DESC LIMIT 20');
    }

    public function getRetornoReqArray($reqs){
        $retorno = array();
        foreach($reqs as $req){
            array_push($retorno, $req->id_solicitacao);
        }
        return $retorno;
    }

    public function getBoletosLiquidados(){
        return $this->db->query('SELECT * FROM boleto WHERE data_retorno IS NOT NULL ORDER BY data_pagamento DESC LIMIT 100');
    }

    public function newBoleto($id_cliente=NULL,$valor=NULL,$data_vencimento=NULL,$outros=NULL){
        $resultado = $this->newBoletoResult($id_cliente, $valor, $data_vencimento, $outros);
        return $resultado['success'];
    }

    public function newBoletoResult($id_cliente=NULL,$valor=NULL,$data_vencimento=NULL,$outros=NULL,$usuario_id=NULL){
        if($id_cliente===null || $valor===null || $data_vencimento===null){
            return $this->result(false, 'PARAMETROS_INVALIDOS', 'Cliente, valor e vencimento sao obrigatorios.');
        }

        $pagador = $this->retornar_cliente($id_cliente)->row();
        if($pagador===null){
            return $this->result(false, 'PAGADOR_NAO_ENCONTRADO', 'Cliente nao encontrado para gerar boleto.');
        }

        return $this->newBoletoPessoaResult($pagador, $valor, $data_vencimento, $outros, $usuario_id);
    }

    public function newBoletoPessoaResult($pagador=NULL,$valor=NULL,$data_vencimento=NULL,$outros=NULL,$usuario_id=NULL){
        if($pagador===null || $valor===null || $data_vencimento===null){
            return $this->fail('PARAMETROS_INVALIDOS', 'Pagador, valor e vencimento sao obrigatorios.', null, null, $pagador, $valor, $data_vencimento, $outros);
        }

        $conta = $this->retornar_conta(1)->row();
        if($conta===null){
            return $this->fail('CONTA_NAO_ENCONTRADA', 'Conta bancaria de emissao nao encontrada.', null, null, $pagador, $valor, $data_vencimento, $outros);
        }

        $config_result = $this->validar_configuracao_sicoob();
        if(!$config_result['success']){
            $this->auditar_erro_boleto($config_result, $pagador, $valor, $data_vencimento, $outros);
            return $config_result;
        }

        $token_result = $this->getSicoobTokenResult();
        if(!$token_result['success']){
            $this->auditar_erro_boleto($token_result, $pagador, $valor, $data_vencimento, $outros);
            return $token_result;
        }

        $id_boleto_atual = $this->get_ultimo_id_boleto()+1;
        $fields = $this->montar_payload_sicoob($pagador, $valor, $data_vencimento, $outros, $id_boleto_atual);
        $response = $this->sicoobRequest('boletos', json_encode($fields), $token_result['token']);
        $decoded = json_decode($response, false);

        if($this->last_curl_error){
            return $this->fail('CURL_ERROR', $this->last_curl_error, $this->last_http_status, $response, $pagador, $valor, $data_vencimento, $outros);
        }

        if((int)$this->last_http_status < 200 || (int)$this->last_http_status >= 300){
            return $this->fail('HTTP_'.$this->last_http_status, 'Sicoob retornou HTTP '.$this->last_http_status.'.', $this->last_http_status, $response, $pagador, $valor, $data_vencimento, $outros);
        }

        if(!isset($decoded->resultado->codigoBarras)){
            return $this->fail('RETORNO_INVALIDO', 'Sicoob nao retornou codigo de barras.', $this->last_http_status, $response, $pagador, $valor, $data_vencimento, $outros);
        }

        $registro = $this->registrarBoletoBD($pagador, $valor, $data_vencimento, $decoded, $conta, $id_boleto_atual, $usuario_id);
        if(!$registro['success']){
            $this->auditar_erro_boleto($registro, $pagador, $valor, $data_vencimento, $outros);
            return $registro;
        }

        return array(
            'success' => true,
            'id_boleto' => $registro['id_boleto'],
            'hash' => $registro['hash'],
            'seu_numero' => $id_boleto_atual,
            'http_status' => $this->last_http_status,
            'erro' => null,
            'mensagem' => 'Boleto gerado com sucesso.',
            'response' => $response
        );
    }

    private function montar_payload_sicoob($pagador, $valor, $data_vencimento, $outros, $id_boleto_atual){
        $data_limite = date('Y-m-d',strtotime($data_vencimento.'+1 month'));
        $data_multa = date('Y-m-d',strtotime($data_vencimento.'+1 day'));
        $cep = $this->safe($pagador, 'cep');
        $valid_cep = str_replace(array('.', '-'), '', $cep);

        $fields = array();
        $fields['numeroCliente'] = (int)adm_env('SICOOB_NUMERO_CLIENTE', 0);
        $fields['codigoModalidade'] = 1;
        $fields['numeroContaCorrente'] = (int)adm_env('SICOOB_CONTA_CORRENTE', 0);
        $fields['codigoEspecieDocumento'] = 'DM';
        $fields['dataEmissao'] = date('Y-m-d');
        $fields['seuNumero'] = $id_boleto_atual;
        $fields['identificacaoEmissaoBoleto'] = 2;
        $fields['identificacaoDistribuicaoBoleto'] = 2;
        $fields['valor'] = (float)$valor;
        $fields['dataVencimento'] = $data_vencimento;
        $fields['dataLimitePagamento'] = $data_limite;
        $fields['tipoDesconto'] = 0;
        $fields['tipoMulta'] = 2;
        $fields['dataMulta'] = $data_multa;
        $fields['valorMulta'] = 2;
        $fields['tipoJurosMora'] = 1;
        $fields['dataJurosMora'] = $data_multa;
        $fields['valorJurosMora'] = 0.033;
        $fields['numeroParcela'] = 1;
        $fields['pagador'] = array();
        $fields['pagador']['numeroCpfCnpj'] = preg_replace('/\D+/', '', $this->safe($pagador, 'cpf_cnpj'));
        $fields['pagador']['nome'] = $this->safe($pagador, 'nome_ou_fantasia', $this->safe($pagador, 'nome'));
        $fields['pagador']['endereco'] = $this->safe($pagador, 'logradouro');
        $fields['pagador']['bairro'] = $this->safe($pagador, 'bairro');
        $fields['pagador']['cidade'] = $this->safe($pagador, 'cidade');
        $fields['pagador']['cep'] = $valid_cep;
        $fields['pagador']['uf'] = $this->safe($pagador, 'uf');
        $fields['pagador']['email'] = $this->safe($pagador, 'email');
        $fields['beneficiarioFinal'] = array();
        $fields['beneficiarioFinal']['numeroCpfCnpj'] = adm_env('SICOOB_BENEFICIARIO_CPF_CNPJ', '');
        $fields['beneficiarioFinal']['nome'] = adm_env('SICOOB_BENEFICIARIO_NOME', 'Rede Mais Credito');
        $fields['mensagensInstrucao'] = array();

        if(isset($outros['descricao_boleto'])){
            $fields['mensagensInstrucao'][] = $outros['descricao_boleto'];
        }else{
            $fields['mensagensInstrucao'][] = 'Boleto de numero '.$id_boleto_atual;
        }

        foreach(array('descricao_boleto2','descricao_boleto3','descricao_boleto4') as $campo){
            if(isset($outros[$campo]) && $outros[$campo] !== ''){
                $fields['mensagensInstrucao'][] = $outros[$campo];
            }
        }

        $fields['gerarPdf'] = true;
        $fields['codigoCadastrarPIX'] = 0;
        return $fields;
    }

    private function registrarBoletoBD($pessoa, $valor, $data_vencimento, $retorno, $conta, $id_boleto_atual, $usuario_id=NULL){
        $codigo_sacado = $this->safe($pessoa, 'codigo_sacado', $this->safe($pessoa, 'id_cliente'));
        $hash = md5($id_boleto_atual);

        $dados = array();
        $dados['id_boleto'] = $id_boleto_atual;
        $dados['id_cliente_fk'] = $this->safe($pessoa, 'id_cliente') !== '' ? $this->safe($pessoa, 'id_cliente') : null;
        $dados['nome_sacado'] = $this->safe($pessoa, 'nome_ou_fantasia', $this->safe($pessoa, 'nome'));
        $dados['cpf_cnpj'] = $this->safe($pessoa, 'cpf_cnpj');
        $dados['logradouro'] = $this->safe($pessoa, 'logradouro');
        $dados['numero'] = $this->safe($pessoa, 'numero');
        $dados['complemento'] = $this->safe($pessoa, 'complemento');
        $dados['bairro'] = $this->safe($pessoa, 'bairro');
        $dados['cidade'] = $this->safe($pessoa, 'cidade');
        $dados['uf'] = $this->safe($pessoa, 'uf');
        $dados['cep'] = $this->safe($pessoa, 'cep');
        $dados['email'] = $this->safe($pessoa, 'email');
        $dados['seu_numero'] = $id_boleto_atual;
        $dados['codigo_sacado'] = $codigo_sacado !== '' ? $codigo_sacado : null;
        $dados['carteira'] = $conta->carteira;
        $dados['codigo_moeda'] = $conta->especie_moeda;
        $dados['especie'] = $conta->especie;
        $dados['aceite'] = $conta->aceite;
        $dados['hash'] = $hash;

        for($i=0; $i<4; $i++){
            $campo = $i===0 ? 'descricao_boleto' : 'descricao_boleto'.($i+1);
            if(isset($retorno->resultado->mensagensInstrucao[$i])){
                $dados[$campo] = $retorno->resultado->mensagensInstrucao[$i];
            }
        }

        $dados['cancelado'] = 0;
        $dados['baixado'] = 0;
        $dados['nota_fiscal'] = 0;
        $dados['correio'] = 0;
        $dados['id_conta_banco'] = $conta->id_conta;
        $dados['valor_boleto'] = $valor;
        $dados['valor_desconto'] = 0;
        $dados['valor_multa'] = 0;
        $dados['valor_juros'] = 0;
        $dados['valor_abatimento'] = 0;
        $dados['pago'] = 0;
        $dados['data_vencimento'] = $data_vencimento;
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $dados['criado_por_usuario_fk'] = $usuario_id!==NULL ? $usuario_id : $this->session->userdata('id');
        $dados['nosso_numero'] = $retorno->resultado->nossoNumero;
        $dados['nosso_numero_formatado'] = $retorno->resultado->nossoNumero;
        $dados['codigo_de_barras'] = $retorno->resultado->codigoBarras;
        $dados['linha_digitavel'] = $retorno->resultado->linhaDigitavel;
        $dados['retorno_api'] = json_encode($retorno);

        $this->db->insert('boleto',$dados);
        if($this->db->affected_rows()>0){
            return array('success'=>true, 'id_boleto'=>$id_boleto_atual, 'hash'=>$hash);
        }

        return $this->result(false, 'DB_INSERT_ERROR', 'Nao foi possivel gravar o boleto no banco.', null, json_encode($this->db->error()));
    }

    public function retornar_cliente($id_cliente){
        $this->db->where(array('id_cliente'=>$id_cliente));
        $this->db->limit(1);
        return $this->db->get('cliente');
    }

    public function retornar_conta($id_conta){
        $this->db->where(array('id_conta'=>$id_conta));
        $this->db->limit(1);
        return $this->db->get('conta');
    }

    private function get_ultimo_id_boleto(){
        $this->db->select_max('id_boleto');
        $row = $this->db->get('boleto')->row();
        return $row && $row->id_boleto ? (int)$row->id_boleto : 0;
    }

    private function getSicoobToken(){
        $resultado = $this->getSicoobTokenResult();
        return $resultado['success'] ? $resultado['token'] : null;
    }

    private function getSicoobTokenResult(){
        $response = $this->siboocRequestToken();
        $decoded = json_decode($response, false);

        if($this->last_curl_error){
            return $this->result(false, 'TOKEN_CURL_ERROR', $this->last_curl_error, $this->last_http_status, $response);
        }

        if((int)$this->last_http_status < 200 || (int)$this->last_http_status >= 300){
            return $this->result(false, 'TOKEN_HTTP_'.$this->last_http_status, 'Sicoob token retornou HTTP '.$this->last_http_status.'.', $this->last_http_status, $response);
        }

        if(!isset($decoded->access_token)){
            return $this->result(false, 'TOKEN_INVALIDO', 'Sicoob nao retornou access_token.', $this->last_http_status, $response);
        }

        return array('success'=>true, 'token'=>$decoded->access_token, 'http_status'=>$this->last_http_status);
    }

    private function siboocRequestToken(){
        $cert_path = $this->sicoob_cert_path();
        $client_id = adm_env('SICOOB_CLIENT_ID', '');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://auth.sicoob.com.br/auth/realms/cooperado/protocol/openid-connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERTTYPE => 'P12',
            CURLOPT_SSLCERT => $cert_path,
            CURLOPT_SSLCERTPASSWD => adm_env('SICOOB_CERT_PASSWORD', ''),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'client_id='.$client_id.'&grant_type=client_credentials&scope=boletos_inclusao%20boletos_consulta%20boletos_alteracao',
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        ));
        $response = curl_exec($curl);
        $this->last_curl_error = curl_error($curl);
        $this->last_http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }

    private function sicoobRequest($path, $fields, $token){
        $cert_path = $this->sicoob_cert_path();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sicoob.com.br/cobranca-bancaria/v3/'.$path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERTTYPE => 'P12',
            CURLOPT_SSLCERT => $cert_path,
            CURLOPT_SSLCERTPASSWD => adm_env('SICOOB_CERT_PASSWORD', ''),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                'client_id: '.adm_env('SICOOB_CLIENT_ID', ''),
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $this->last_curl_error = curl_error($curl);
        $this->last_http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }

    private function safe($object, $field, $default=''){
        return isset($object->$field) && $object->$field !== null ? $object->$field : $default;
    }

    private function sicoob_cert_path(){
        $path = adm_env('SICOOB_CERT_PATH', 'cert/rmc_2026.pfx');
        if(preg_match('/^[A-Za-z]:[\\\\\/]/', $path) || substr($path, 0, 1)==='/'){
            return $path;
        }
        return rtrim(getcwd(), '/\\').DIRECTORY_SEPARATOR.str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }

    private function validar_configuracao_sicoob(){
        $faltando = array();
        foreach(array(
            'SICOOB_CERT_PATH',
            'SICOOB_CERT_PASSWORD',
            'SICOOB_CLIENT_ID',
            'SICOOB_NUMERO_CLIENTE',
            'SICOOB_CONTA_CORRENTE',
            'SICOOB_BENEFICIARIO_CPF_CNPJ'
        ) as $campo){
            if(adm_env($campo, '')===''){
                $faltando[] = $campo;
            }
        }

        $cert_path = $this->sicoob_cert_path();
        if($cert_path==='' || !is_file($cert_path) || !is_readable($cert_path)){
            $faltando[] = 'SICOOB_CERT_FILE';
        }

        if(count($faltando)>0){
            return $this->result(false, 'CONFIGURACAO_SICOOB_INCOMPLETA', 'Configuracao do Sicoob incompleta: '.implode(', ', $faltando).'.', null, array('faltando'=>$faltando));
        }

        return array('success'=>true);
    }

    private function result($success, $erro, $mensagem, $http_status=null, $response=null){
        return array(
            'success' => $success,
            'id_boleto' => null,
            'hash' => null,
            'erro' => $erro,
            'mensagem' => $mensagem,
            'http_status' => $http_status,
            'response' => $response
        );
    }

    private function fail($erro, $mensagem, $http_status=null, $response=null, $pagador=null, $valor=null, $data_vencimento=null, $outros=null){
        $resultado = $this->result(false, $erro, $mensagem, $http_status, $response);
        $this->auditar_erro_boleto($resultado, $pagador, $valor, $data_vencimento, $outros);
        return $resultado;
    }

    private function auditar_erro_boleto($resultado, $pagador=null, $valor=null, $data_vencimento=null, $outros=null){
        if(!$this->db->table_exists('adm_auditoria')){
            return;
        }

        $this->load->model('adminauditoria_model', 'adminauditoria');
        $this->adminauditoria->registrar(array(
            'area' => 'boleto',
            'acao' => 'sicoob_gerar_boleto',
            'status' => 'erro',
            'referencia_tipo' => $this->safe($pagador, 'id_cliente') !== '' ? 'cliente' : null,
            'referencia_id' => $this->safe($pagador, 'id_cliente') !== '' ? $this->safe($pagador, 'id_cliente') : null,
            'http_status' => isset($resultado['http_status']) ? $resultado['http_status'] : null,
            'erro' => isset($resultado['erro']) ? $resultado['erro'] : null,
            'mensagem' => isset($resultado['mensagem']) ? $resultado['mensagem'] : null,
            'contexto' => array(
                'pagador' => array(
                    'id_cliente' => $this->safe($pagador, 'id_cliente'),
                    'codigo_sacado' => $this->safe($pagador, 'codigo_sacado'),
                    'nome' => $this->safe($pagador, 'nome_ou_fantasia', $this->safe($pagador, 'nome')),
                    'cpf_cnpj' => $this->safe($pagador, 'cpf_cnpj')
                ),
                'valor' => $valor,
                'vencimento' => $data_vencimento,
                'outros' => $outros
            ),
            'retorno' => isset($resultado['response']) ? $resultado['response'] : null
        ));
    }
}
