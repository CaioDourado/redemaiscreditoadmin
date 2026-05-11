<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

class BoletoV3 extends ControllerAuth {
    private $debug_steps = array();
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
        $this->verifica_retorno($id_solicitacao, $this->debugEnabled(), !$this->debugEnabled());
        if($this->debugEnabled()){
            $this->renderDebug('Debug Retorno Sicoob - Verificar');
        }
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

    public function get_retorno($d, $d_1, $debug=false){
        if($debug){
            $this->debugStep('Inicio da requisicao de retorno', array('inicio'=>$d_1, 'fim'=>$d));
        }
        $token = $this->getSicoobToken();
        if($debug){
            $this->debugStep('Token Sicoob obtido', array('obtido'=>($token ? 'sim' : 'nao'), 'tamanho'=>strlen((string)$token)));
        }
        $fields = array();
        $fields['numeroCliente'] = (int)adm_env('SICOOB_NUMERO_CLIENTE', 0);
        $fields['tipoMovimento'] = 5;
        $fields['dataInicial'] = $d_1;
        $fields['dataFinal'] = $d;
        if($debug){
            $this->debugStep('Payload enviado ao Sicoob', $fields);
        }
        $raw_req = $this->sicoobRequest("boletos/movimentacoes", json_encode($fields), $token);
        $req = json_decode($raw_req);
        if($debug){
            $this->debugStep('Resposta crua da requisicao', $raw_req);
            $this->debugStep('Resposta decodificada da requisicao', $req);
        }

        if(isset($req->resultado->codigoSolicitacao)){
            $bd_data = ["id_solicitacao"=>$req->resultado->codigoSolicitacao, "inicio"=>$d_1, "fim"=>$d];
            $this->boletoV3->inserirRetornoReq($bd_data);
            if($debug){
                $this->debugStep('Solicitacao gravada em retorno_req', $bd_data);
                $this->verifica_retorno($req->resultado->codigoSolicitacao, true, false);
                return;
            }
			set_msg("Retorno requisitado com sucesso!","sucesso");
        }else{
            if($debug){
                $this->debugStep('Solicitacao nao retornou codigoSolicitacao', $req);
                return;
            }
			set_msg("Ocorreu um erro ao requisitar o retorno.","erro");
        }
        redirect('boletoV3/retorno');
    }

    public function retorno_req(){
        if(isset($_GET["fim"])){
            $debug = $this->debugEnabled();
            $d = trim(data_db(urldecode($_GET["fim"])));
            $d_1 = trim(date("Y-m-d",strtotime($d.' -1 day')));

            $this->get_retorno($d, $d_1, $debug);
            if($debug){
                $this->renderDebug('Debug Retorno Sicoob - Pedir Retorno');
            }
        }else{
            set_msg("Não foi informada uma data para a solicitação de retorno.");
            redirect('boletoV3/retorno');
        }
    }

    public function verifica_retorno($codigo_solicitacao, $debug=false, $redirect=true){
        if($debug){
            $this->debugStep('Inicio da verificacao do retorno', array('codigo_solicitacao'=>$codigo_solicitacao));
        }
        $token = $this->getSicoobToken();
        if($debug){
            $this->debugStep('Token Sicoob obtido para verificar', array('obtido'=>($token ? 'sim' : 'nao'), 'tamanho'=>strlen((string)$token)));
        }
        $path = "boletos/movimentacoes?numeroCliente=".adm_env('SICOOB_NUMERO_CLIENTE', 0)."&codigoSolicitacao=".$codigo_solicitacao;
        if($debug){
            $this->debugStep('Endpoint de verificacao', $path);
        }
        $raw_req = $this->sicoobRequestGET($path, $token);
        $req = json_decode($raw_req, false);
        if($debug){
            $this->debugStep('Resposta crua da verificacao', $raw_req);
            $this->debugStep('Resposta decodificada da verificacao', $req);
        }
        if(isset($req->mensagens)){
            if($debug){
                $this->debugStep('Sicoob retornou mensagem na verificacao', $req->mensagens);
                return;
            }
			set_msg($req->mensagens[0]->mensagem,"alerta");
        }else{
            if(!isset($req->resultado->idArquivos[0])){
                if($debug){
                    $this->debugStep('Nenhum arquivo disponivel para download', $req);
                    return;
                }
                set_msg("Retorno sem arquivo disponivel para download.","alerta");
                if($redirect){
                    redirect("boletoV3/retorno");
                }
                return;
            }
            $this->download_retorno($codigo_solicitacao, $req->resultado->idArquivos[0], $debug);
            if($debug){
                return;
            }
			set_msg("Retorno consolidado com sucesso!","sucesso");
        }
        if($redirect){
            redirect("boletoV3/retorno");
        }
    }

    public function download_retorno($codigo_solicitacao, $id_arquivo, $debug=false){
        //$codigo_solicitacao = "29165897";
        $token = $this->getSicoobToken();
        $path = "boletos/movimentacoes/download?numeroCliente=".adm_env('SICOOB_NUMERO_CLIENTE', 0)."&codigoSolicitacao=".$codigo_solicitacao.'&idArquivo='.$id_arquivo;
        if($debug){
            $this->debugStep('Endpoint de download', $path);
        }
        $raw_req = $this->sicoobRequestGET($path, $token);
        $req = json_decode($raw_req, false);
        if($debug){
            $debug_req = json_decode(json_encode($req));
            if(isset($debug_req->resultado->arquivo)){
                $debug_req->resultado->arquivo = '[base64 omitido - tamanho '.strlen((string)$req->resultado->arquivo).']';
            }
            $this->debugStep('Resposta crua do download', '[conteudo omitido por conter arquivo base64 - tamanho '.strlen((string)$raw_req).']');
            $this->debugStep('Resposta decodificada do download', $debug_req);
        }
        if(!isset($req->resultado->arquivo) || !isset($req->resultado->nomeArquivo)){
            if($debug){
                $this->debugStep('Download nao retornou arquivo/nomeArquivo', $req);
            }
            return;
        }
        $this->b64Conversion($req->resultado->arquivo, $req->resultado->nomeArquivo, $debug);
    }

    public function b64Conversion($fileData=null, $fileName=null, $debug=false){
        if($fileData!==null && $fileName!==null){
            $this->ensureRetornoDirs();
            $data = preg_replace('/\s+/', '', (string)$fileData);
            $decoded = base64_decode($data, true);
            if($decoded===false){
                if($debug){
                    $this->debugStep('Falha ao decodificar base64', array('nomeArquivo'=>$fileName, 'tamanho_base64'=>strlen($data)));
                }
                return;
            }

            $file = $this->retornoPath($fileName);
            file_put_contents($file, $decoded);
            if($debug){
                $this->debugStep('Arquivo de retorno salvo', array(
                    'arquivo'=>$file,
                    'bytes'=>strlen((string)$decoded),
                    'primeiros_bytes_hex'=>bin2hex(substr($decoded, 0, 16)),
                    'primeiros_bytes_texto'=>substr($decoded, 0, 80)
                ));
            }

            $zip = new ZipArchive;
            $res = $zip->open($file);
            if($debug){
                $this->debugStep('Resultado ao abrir zip', array('resultado'=>$res));
            }
            if ($res === TRUE) {
                $zip_dir = $this->retornoPath('zips');
                $zip->extractTo($zip_dir);
                if($debug){
                    $nomes = array();
                    for($i=0; $i<$zip->numFiles; $i++){
                        $nomes[] = $zip->getNameIndex($i);
                    }
                    $this->debugStep('Arquivos encontrados no zip', $nomes);
                }
                $zip->close();
                unlink($file);
                $this->readFilesFromRetornosZips($debug);
            }else{
                $this->processNonZipRetorno($decoded, $fileName, $file, $res, $debug);
            }
        }elseif($debug){
            $this->debugStep('Conversao base64 ignorada por dados ausentes', array('fileData'=>($fileData!==null), 'fileName'=>$fileName));
        }
    }

    public function readFilesFromRetornosZips($debug=false){
        $this->ensureRetornoDirs();
        $dir = $this->retornoPath('zips');
        $files = scandir($dir);
        if($debug){
            $this->debugStep('Arquivos na pasta retornos/zips', array('diretorio'=>$dir, 'arquivos'=>$files));
        }
        foreach($files as $i => $f):
            if($i>1):
                $this->readJsonData($dir.DIRECTORY_SEPARATOR.$f, $debug);
            endif;
        endforeach;
    }

    private function readJsonData($path, $debug=false){
        $raw_content = file_get_contents($path);
        $content = json_decode($raw_content, false);
        if($debug){
            $this->debugStep('Conteudo bruto do arquivo de retorno', array('arquivo'=>$path, 'conteudo'=>$raw_content));
            $this->debugStep('Boletos decodificados do arquivo de retorno', $content);
        }
        if(!is_array($content)){
            if($debug){
                $this->debugStep('Arquivo de retorno nao possui lista de boletos valida', $content);
            }
            return;
        }
        $retorno = array();
        foreach($content as $i => $c):
            $now = array();
            $now['numero_titulo'] = isset($c->numeroTitulo) ? $c->numeroTitulo : null;
            $now['seu_numero'] = isset($c->seuNumero) ? $c->seuNumero : null;
            $now['valor_titulo'] = isset($c->valorTitulo) ? $c->valorTitulo : null;
            $now['data_liquidacao'] = isset($c->dataLiquidacao) ? $this->convertData($c->dataLiquidacao) : null;
            $now['data_previsao'] = isset($c->dataPrevisaoCredito) ? $this->convertData($c->dataPrevisaoCredito) : null;
            $now['valor_liquido'] = isset($c->valorLiquido) ? $c->valorLiquido : null;
            $now['valor_tarifa'] = isset($c->valorTarifaMovimento) ? $c->valorTarifaMovimento : null;
            $insert = $this->boletoV3->inserirRetorno($now);

            $update = array();
            $update['data_pagamento'] = $now['data_liquidacao'];
            $update['data_retorno'] = date("Y-m-d H:i:s");
            $update['pago'] = 1;
            $update['valor_pago'] = $now['valor_liquido'];
            $update['valor_tarifa'] = $now['valor_tarifa'];
            $update['codigo_retorno'] = "05";
            $update_result = $this->boletoV3->updateBoleto($update, array('seu_numero'=>$now['seu_numero']));

            if($debug){
                $this->debugStep('Boleto processado para baixa', array(
                    'indice'=>$i,
                    'retorno'=>$now,
                    'insert_retorno'=>$insert ? 'ok' : 'verificar',
                    'update_boleto_por_seu_numero'=>$now['seu_numero'],
                    'update_boleto'=>$update_result ? 'ok' : 'verificar'
                ));
            }

            $retorno[] = $now;
        endforeach;
        if($debug){
            $this->debugStep('Resumo dos boletos processados', array('quantidade'=>count($retorno), 'boletos'=>$retorno));
        }
        unlink($path);
        if($debug){
            $this->debugStep('Arquivo de retorno removido apos processamento', $path);
        }
    }

    private function debugEnabled(){
        return isset($_GET['debug']) && $_GET['debug']=='1';
    }

    private function ensureRetornoDirs(){
        $base = $this->retornoPath();
        $zips = $this->retornoPath('zips');
        if(!is_dir($base)){
            mkdir($base, 0775, true);
        }
        if(!is_dir($zips)){
            mkdir($zips, 0775, true);
        }
    }

    private function retornoPath($path=''){
        $base = rtrim(FCPATH, '/\\').DIRECTORY_SEPARATOR.'retornos';
        if($path===''){
            return $base;
        }
        return $base.DIRECTORY_SEPARATOR.str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }

    private function processNonZipRetorno($decoded, $fileName, $file, $zipResult, $debug=false){
        if($debug){
            $this->debugStep('Arquivo nao abriu como zip, tentando formatos alternativos', array(
                'arquivo'=>$file,
                'nomeArquivo'=>$fileName,
                'resultado_zip'=>$zipResult,
                'primeiros_bytes_hex'=>bin2hex(substr($decoded, 0, 16)),
                'preview'=>substr($decoded, 0, 300)
            ));
        }

        $content = $decoded;
        if(substr($decoded, 0, 2)==="\x1f\x8b"){
            $unzipped = gzdecode($decoded);
            if($unzipped!==false){
                $content = $unzipped;
                if($debug){
                    $this->debugStep('Conteudo gzip descompactado', array('bytes'=>strlen($content), 'preview'=>substr($content, 0, 300)));
                }
            }
        }

        $trimmed = ltrim($content, "\xEF\xBB\xBF\r\n\t ");
        if($trimmed!=='' && ($trimmed[0]=='[' || $trimmed[0]=='{')){
            $json_name = preg_replace('/\.[^.]+$/', '', basename($fileName)).'.json';
            $json_path = $this->retornoPath('zips'.DIRECTORY_SEPARATOR.$json_name);
            file_put_contents($json_path, $trimmed);
            if(file_exists($file)){
                unlink($file);
            }
            if($debug){
                $this->debugStep('Arquivo tratado como JSON direto', array('arquivo'=>$json_path));
            }
            $this->readJsonData($json_path, $debug);
            return;
        }

        if($debug){
            $this->debugStep('Formato do retorno nao reconhecido', array(
                'arquivo_salvo_para_inspecao'=>$file,
                'observacao'=>'O Sicoob retornou um arquivo que nao e zip, json ou gzip reconhecido.'
            ));
        }
    }

    private function debugStep($title, $data=null){
        $this->debug_steps[] = array('title'=>$title, 'data'=>$data);
    }

    private function renderDebug($title){
        $this->output->set_content_type('text/html', 'UTF-8');
        echo '<!doctype html><html><head><meta charset="utf-8">';
        echo '<title>'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:24px;background:#f7f7f7;color:#222}';
        echo 'h1{font-size:24px;margin:0 0 18px}.step{background:#fff;border:1px solid #ddd;border-left:5px solid #0b8f53;margin:0 0 14px;padding:14px}';
        echo '.step h2{font-size:16px;margin:0 0 10px;color:#0b6f40}pre{white-space:pre-wrap;word-break:break-word;background:#111;color:#e8e8e8;padding:12px;border-radius:3px;font-size:12px;line-height:1.4}';
        echo '.muted{color:#777;font-size:12px;margin-bottom:18px}</style></head><body>';
        echo '<h1>'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</h1>';
        echo '<div class="muted">Debug gerado em '.date('d/m/Y H:i:s').'. Token e certificado nao sao exibidos.</div>';
        foreach($this->debug_steps as $i => $step){
            echo '<div class="step">';
            echo '<h2>'.($i+1).'. '.htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8').'</h2>';
            if($step['data']!==null){
                echo '<pre>'.htmlspecialchars(print_r($step['data'], true), ENT_QUOTES, 'UTF-8').'</pre>';
            }
            echo '</div>';
        }
        echo '</body></html>';
        exit;
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
