<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

class BoletoV3 extends ControllerAuth {
    function __construct(){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);

        parent::__construct();
        $this->load->model('boletoV3_model','boletoV3');
    }

    public function retorno(){
        $reqs = $this->boletoV3->getRetornoReq()->result();
        $boletos = $this->boletoV3->getBoletosLiquidados()->result();

		$this->parameters['menu'] = $this->load_menu('boletos');
		$this->parameters['content'] = $this->load->view('screens/boletov3', array("tela"=>'retorno','reqs'=>$reqs,'boletos'=>$boletos), true);
		$this->load->view('templates/maing', $this->parameters);
    }

    public function check_retorno(){
        $id_solicitacao = $this->uri->segment(3);
        $this->verifica_retorno($id_solicitacao);
    }

    public function nbol(){

        $token = $this->getSicoobToken();

        $pagador = new stdClass();
        $pagador->cpfcnpj = '11746842679';
        $pagador->nome = 'Caio Fellipe Dourado';
        $pagador->endereco = 'Rua Sampaio 45';
        $pagador->bairro = 'Grambery';
        $pagador->cidade = 'Juiz de Fora';
        $pagador->cep = '36010360';
        $pagador->uf = 'MG';
        $pagador->email = 'caiof.dourado@gmail.com';

        $data_vencimento = '2024-10-10';
        $data_limite = date('Y-m-d',strtotime($data_vencimento.'+1 month'));
        $data_multa = date('Y-m-d',strtotime($data_vencimento.'+1 day'));

        $fields = array();
        $fields['numeroCliente'] = (int)adm_env('SICOOB_NUMERO_CLIENTE', 0);
        $fields['codigoModalidade'] = 1;
        $fields['numeroContaCorrente'] = (int)adm_env('SICOOB_CONTA_CORRENTE', 0);
        $fields['codigoEspecieDocumento'] = "DM";
        $fields['dataEmissao'] = date('Y-m-d');
        $fields['seuNumero'] = '1';
        $fields['identificacaoEmissaoBoleto'] = 2;
        $fields['identificacaoDistribuicaoBoleto'] = 2;
        $fields['valor'] = 1.5;
        $fields['dataVencimento'] = '2024-10-10';
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
        $fields['pagador']['numeroCpfCnpj'] = $pagador->cpfcnpj;
        $fields['pagador']['nome'] = $pagador->nome;
        $fields['pagador']['endereco'] = $pagador->endereco;
        $fields['pagador']['bairro'] = $pagador->bairro;
        $fields['pagador']['cidade'] = $pagador->cidade;
        $fields['pagador']['cep'] = $pagador->cep;
        $fields['pagador']['uf'] = $pagador->uf;
        $fields['pagador']['email'] = $pagador->email;
        $fields['beneficiarioFinal'] = array();
        $fields['beneficiarioFinal']['numeroCpfCnpj'] = adm_env('SICOOB_BENEFICIARIO_CPF_CNPJ', '');
        $fields['beneficiarioFinal']['nome'] = adm_env('SICOOB_BENEFICIARIO_NOME', 'Rede Mais Credito');
        $fields['mensagensInstrucao'] = array(
            "Mensalidade Setembro de 2024"
        );
        $fields['gerarPdf'] = true;
        $fields['codigoCadastrarPIX'] = 0;

        $req = $this->sicoobRequest("boletos", json_encode($fields), $token);
        echo $req.'<br><br>';
        $req = json_decode($req, false);

        echo $token.'<br><br>';

        var_dump($req);
    }

    public function get_retorno($d, $d_1){
        $token = $this->getSicoobToken();
        $fields = array();
        $fields['numeroCliente'] = (int)adm_env('SICOOB_NUMERO_CLIENTE', 0);
        $fields['tipoMovimento'] = 5;
        $fields['dataInicial'] = $d_1;
        $fields['dataFinal'] = $d;
        $req = $this->sicoobRequest("boletos/movimentacoes", json_encode($fields), $token);
        $req = json_decode($req);

        if(isset($req->resultado->codigoSolicitacao)){
            $bd_data = ["id_solicitacao"=>$req->resultado->codigoSolicitacao, "inicio"=>$d_1, "fim"=>$d];
            $this->boletoV3->inserirRetornoReq($bd_data);
			set_msg("Retorno requisitado com sucesso!","sucesso");
        }else{
			set_msg("Ocorreu um erro ao requisitar o retorno.","erro");
        }
        redirect('boletoV3/retorno');
    }

    public function retorno_req(){
        if(isset($_GET["fim"])){
            $d = trim(data_db(urldecode($_GET["fim"])));
            $d_1 = trim(date("Y-m-d",strtotime($d.' -1 day')));

            $this->get_retorno($d, $d_1);
        }else{
            set_msg("Não foi informada uma data para a solicitação de retorno.");
            redirect('boletoV3/retorno');
        }
    }

    public function verifica_retorno($codigo_solicitacao){
        $token = $this->getSicoobToken();
        $path = "boletos/movimentacoes?numeroCliente=".adm_env('SICOOB_NUMERO_CLIENTE', 0)."&codigoSolicitacao=".$codigo_solicitacao;
        $req = $this->sicoobRequestGET($path, $token);
        $req = json_decode($req, false);
        if(isset($req->mensagens)){
			set_msg($req->mensagens[0]->mensagem,"alerta");
        }else{
            $this->download_retorno($codigo_solicitacao, $req->resultado->idArquivos[0]);
			set_msg("Retorno consolidado com sucesso!","sucesso");
        }
        redirect("boletoV3/retorno");
    }

    public function download_retorno($codigo_solicitacao, $id_arquivo){
        //$codigo_solicitacao = "29165897";
        $token = $this->getSicoobToken();
        $path = "boletos/movimentacoes/download?numeroCliente=".adm_env('SICOOB_NUMERO_CLIENTE', 0)."&codigoSolicitacao=".$codigo_solicitacao.'&idArquivo='.$id_arquivo;
        $req = $this->sicoobRequestGET($path, $token);
        $req = json_decode($req, false);
        $this->b64Conversion($req->resultado->arquivo, $req->resultado->nomeArquivo);
    }

    public function b64Conversion($fileData=null, $fileName=null){
        if($fileData!==null && $fileName!==null){
            $data = $fileData;
            $decoded = base64_decode($data);
            $file = 'retornos/'.$fileName;
            file_put_contents($file, $decoded);

            $zip = new ZipArchive;
            $res = $zip->open($file);
            if ($res === TRUE) {
                $zip->extractTo('retornos/zips/');
                $zip->close();
                unlink($file);
                $this->readFilesFromRetornosZips();
            }
        }
    }

    public function readFilesFromRetornosZips(){
        $files = scandir("retornos/zips");
        foreach($files as $i => $f):
            if($i>1):
                $this->readJsonData("retornos/zips/".$f);
            endif;
        endforeach;
    }

    private function readJsonData($path){
        $content = json_decode(file_get_contents($path), false);
        $retorno = array();
        foreach($content as $i => $c):
            $now = array();
            $now['numero_titulo'] = $c->numeroTitulo;
            $now['seu_numero'] = $c->seuNumero;
            $now['valor_titulo'] = $c->valorTitulo;
            $now['data_liquidacao'] = $this->convertData($c->dataLiquidacao);
            $now['data_previsao'] = $this->convertData($c->dataPrevisaoCredito);
            $now['valor_liquido'] = $c->valorLiquido;
            $now['valor_tarifa'] = $c->valorTarifaMovimento;
            $this->boletoV3->inserirRetorno($now);

            $update = array();
            $update['data_pagamento'] = $now['data_liquidacao'];
            $update['data_retorno'] = date("Y-m-d H:i:s");
            $update['pago'] = 1;
            $update['valor_pago'] = $now['valor_liquido'];
            $update['valor_tarifa'] = $now['valor_tarifa'];
            $update['codigo_retorno'] = "05";
            $this->boletoV3->updateBoleto($update, array('seu_numero'=>$now['seu_numero']));

            $retorno[] = $now;
        endforeach;
        unlink($path);
    }

    private function getSicoobToken(){
        return json_decode($this->siboocRequestToken(), false)->access_token;
    }

    private function sicoobCertPath(){
        $path = adm_env('SICOOB_CERT_PATH', 'cert/rmc_2026.pfx');
        if(preg_match('/^[A-Za-z]:[\\\\\/]/', $path) || substr($path, 0, 1)==='/'){
            return $path;
        }
        return rtrim(getcwd(), '/\\').DIRECTORY_SEPARATOR.str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }

    //public function sicoobRequest(){
    private function siboocRequestToken(){
        $cert_path = $this->sicoobCertPath();
        $client_id = adm_env('SICOOB_CLIENT_ID', '');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://auth.sicoob.com.br/auth/realms/cooperado/protocol/openid-connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $cert_path,
            CURLOPT_SSLCERTPASSWD => adm_env('SICOOB_CERT_PASSWORD', ''),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'client_id='.$client_id.'&grant_type=client_credentials&scope=boletos_inclusao%20boletos_consulta%20boletos_alteracao',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
		//$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }

    private function sicoobRequest($path, $fields, $token){
        $cert_path = $this->sicoobCertPath();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sicoob.com.br/cobranca-bancaria/v3/'.$path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $cert_path,
            CURLOPT_SSLCERTPASSWD => adm_env('SICOOB_CERT_PASSWORD', ''),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$fields,
            CURLOPT_HTTPHEADER => array(
                'client_id: '.adm_env('SICOOB_CLIENT_ID', ''),
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function sicoobRequestGET($path, $token){
        $cert_path = $this->sicoobCertPath();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sicoob.com.br/cobranca-bancaria/v3/'.$path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $cert_path,
            CURLOPT_SSLCERTPASSWD => adm_env('SICOOB_CERT_PASSWORD', ''),
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'client_id: '.adm_env('SICOOB_CLIENT_ID', ''),
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //echo $httpcode.'<br><br>';
        curl_close($curl);
        return $response;
    }

    private function convertData($date){
        return explode("T", $date)[0];
    }
}
