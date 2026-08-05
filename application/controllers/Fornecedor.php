<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fornecedor extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('fornecedor_model', 'fornecedor');
        $this->parameters['title'] = 'Fornecedor';
        $this->parameters['title_window'] = 'Fornecedor';
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'fornecedores'), true);
        array_push($this->parameters['breadcrumb'], array('fornecedor', 'Fornecedores'));
    }

    public function Index(){
        $fornecedores = $this->fornecedor->retornar_todos()->result();

        $this->parameters['pg_title'] = '<i class="fa fa-truck"></i> Fornecedores';
        $this->parameters['pg_subtitle'] = 'Controle de Fornecedores';

        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content' => 'gerenciar', 'fornecedores' => $fornecedores),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function gerenciar(){
        
    }

    public function perfil(){
        $id_fornecedor = $this->verificar_parametro(3,'Fornecedor Não Informado');
        $fornecedor = $this->fornecedor->retornar($id_fornecedor)->row();
        $consultas = $this->fornecedor->retornar_consultas($fornecedor->id_fornecedor)->result();

        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'fornecedor_perfil','fornecedor'=>$fornecedor), true);
        $this->parameters['menu'] .= $this->load->view('components/menu',array('menu'=>'mais_opcoes'),true);

        $this->parameters['pg_title'] = '<i class="fa fa-truck"></i> Perfil de Fornecedor';
        $this->parameters['pg_subtitle'] = $fornecedor->nome;

        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'perfil','fornecedor'=>$fornecedor,'consultas'=>$consultas),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function cadastrar(){
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'only_numbers');
        $this->form_validation->set_rules('celular', 'Celular', 'only_numbers');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','email','telefone','celular','observacao','usuario','senha','chave'),$this->input->post());
            if($this->fornecedor->inserir($dados)){
                set_msg('Fornecedor Cadastrada com sucesso!','sucesso');
                redirect('fornecedor');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar o Fornecedor.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('fornecedor/cadastrar','Cadastrar'));
        $this->parameters['title'] .= ' - Cadastrar';
        $this->parameters['title_window'] .= ' - Cadastrar';
        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'cadastrar'),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function alterar(){
        $id_fornecedor = $this->verificar_parametro(3,'Não foi informado um fornecedor válido','fornecedor');
        $fornecedor = $this->fornecedor->retornar($id_fornecedor)->row();

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'only_numbers');
        $this->form_validation->set_rules('celular', 'Celular', 'only_numbers');

        if($this->form_validation->run()==TRUE) {
            $is_auth = 0;
            if($this->input->post('isauth')!=null) { $is_auth = 1; }
            
            $dados = elements(array('nome','email','telefone','celular','observacao','usuario','senha','chave','authurl','authbody','header'),$this->input->post());
            $dados['isauth'] = $is_auth;
            if($this->fornecedor->alterar($fornecedor->id_fornecedor,$dados)){
                set_msg('Fornecedor Cadastrada com sucesso!','sucesso');
                redirect('fornecedor');
            }else{
                set_msg('Ocorreu um erro na hora de cadastrar o Fornecedor.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('fornecedor/alterar/'.$fornecedor->id_fornecedor,'Alterar'));
        $this->parameters['title'] .= ' - Alterar';
        $this->parameters['title_window'] .= ' - Alterar';
        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'alterar','fornecedor'=>$fornecedor),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function adicionar_consulta(){
        $id_fornecedor = $this->verificar_parametro(3,'Fornecedor Não Informado');
        $fornecedor = $this->fornecedor->retornar($id_fornecedor)->row();

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('slug', 'Slug', 'required');
        $this->form_validation->set_rules('requisicao', 'Requisição', 'required');
        $this->form_validation->set_rules('custo', 'Custo', 'required|only_numbers');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','slug','requisicao','descricao','header','body','formato'),$this->input->post());
            $dados['custo'] = $this->input->post('custo')/100;
            $dados['id_fornecedor_fk'] = $fornecedor->id_fornecedor;
            if($this->fornecedor->inserir_consulta($dados)){
                set_msg('Consulta Cadastrada para Fornecedor com sucesso!','sucesso');
                redirect('fornecedor/perfil/'.$fornecedor->id_fornecedor);
            }else{
                set_msg('Ocorreu um erro ao cadastrar a consulta para o Fornecedor.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Perfil de '.$fornecedor->nome));
        array_push($this->parameters['breadcrumb'],array('fornecedor/adicionar_consulta/'.$fornecedor->id_fornecedor,'Adicionar Consulta'));
        $this->parameters['menu'] = $this->load->view('components/menu', array('menu' => 'fornecedor_perfil','fornecedor'=>$fornecedor), true);
        $this->parameters['title'] .= ' - Adicionar Consulta';
        $this->parameters['title_window'] .= ' - Adicionar Consulta';
        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'adicionar_consulta','fornecedor'=>$fornecedor),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function alterar_consulta(){
        $id_consulta = $this->verificar_parametro(3,'Consulta não informada');
        $consulta = $this->fornecedor->retornar_consulta($id_consulta)->row();
        $fornecedor = $this->fornecedor->retornar($consulta->id_fornecedor_fk)->row();

        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('slug', 'Slug', 'required');
        $this->form_validation->set_rules('requisicao', 'Requisição', 'required');
        $this->form_validation->set_rules('custo', 'Custo', 'required|only_numbers');
        $this->form_validation->set_rules('timeout', 'Timeout', 'required|integer|greater_than_equal_to[1]|less_than_equal_to[600]');

        if($this->form_validation->run()==TRUE) {
            $dados = elements(array('nome','slug','requisicao','descricao','header','body','formato','timeout'),$this->input->post());
            $dados['custo'] = $this->input->post('custo')/100;
            $dados['timeout'] = (int) $this->input->post('timeout');
            $dados['id_fornecedor_fk'] = $fornecedor->id_fornecedor;
            if($this->fornecedor->alterar_consulta($consulta->id_fornecedor_consulta,$dados)){
                set_msg('Consulta Alterada para o Fornecedor com sucesso!','sucesso');
                redirect('fornecedor/perfil/'.$fornecedor->id_fornecedor);
            }else{
                set_msg('Ocorreu um erro ao alterar a consulta para o Fornecedor.');
                redirect(current_url());
            }
        }

        array_push($this->parameters['breadcrumb'],array('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Perfil de '.$fornecedor->nome));
        array_push($this->parameters['breadcrumb'],array('fornecedor/alterar_consulta/'.$fornecedor->id_fornecedor,'Alterar'));
        $this->parameters['title'] .= ' - Alterar Consulta';
        $this->parameters['title_window'] .= ' - Alterar Consulta';
        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'alterar_consulta','fornecedor'=>$fornecedor,'consulta'=>$consulta),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function teste(){
		$id_consulta = $this->verificar_parametro(3,'Consulta não informada');
        $consulta = $this->fornecedor->retornar_consulta($id_consulta)->row();
        $fornecedor = $this->fornecedor->retornar($consulta->id_fornecedor_fk)->row();
        $header = '';
        $body = '';
        if($fornecedor->isauth==1){
            $token = $this->get_token($fornecedor);
            $header = $this->prepara_url_utf8($consulta->header, array('TOKEN'=>$token));
            $body = $consulta->body;
        }

        $consulta->requisicao = str_replace("{{USUARIO}}",$fornecedor->usuario,$consulta->requisicao);
        $consulta->requisicao = str_replace("{{SENHA}}",$fornecedor->senha,$consulta->requisicao);
        $consulta->requisicao = str_replace("{{CHAVE}}",$fornecedor->chave,$consulta->requisicao);

        $this->form_validation->set_rules('requisicao', 'Requsicao', 'required');

        if($this->form_validation->run()==TRUE) {
            $url_preparada = $this->input->post('requisicao');

            $milis_atual = strtotime(date('Y-m-d H:i:s'));
            $dados_consulta_efetuada = array();
            $dados_consulta_efetuada['id_fornecedor_consulta_fk'] = $consulta->id_fornecedor_consulta;
            $dados_consulta_efetuada['nome'] = $consulta->nome;
            $dados_consulta_efetuada['requisicao'] = $url_preparada;
            $dados_consulta_efetuada['slug'] = $consulta->slug;
            $dados_consulta_efetuada['id_fornecedor_fk'] = $consulta->id_fornecedor_fk;

            if($fornecedor->isauth==1){
                $dados_consulta_efetuada['header'] = $this->input->post('header');
                $dados_consulta_efetuada['body'] = $this->input->post('body');
                $header = explode(",",$this->input->post('header'));
                $retorno_principal = $this->make_request_json($consulta->requisicao, $this->input->post('body'), $header);
                $retorno_principal = $retorno_principal['retorno'];
                $retorno_json = $retorno_principal;
            }else{
				if($consulta->file!=null){
					ini_set('display_errors', 1);
					ini_set('display_startup_errors', 1);
					error_reporting(E_ALL);

					$retorno_principal = $this->make_request_file($url_preparada, $consulta->file);
					$retorno_principal = $retorno_principal['retorno'];

					if($consulta->formato=="xml"){
						$retorno_array = simplexml_load_string($retorno_principal);
						$retorno_json = json_encode($retorno_array);
					}else{
						$retorno_json = $retorno_principal;
					}
				}else{
					$retorno_principal = $this->make_request($url_preparada);
					$retorno_principal = $retorno_principal['retorno'];

					if($consulta->formato=="xml"){
						$retorno_array = simplexml_load_string($retorno_principal);
						$retorno_json = json_encode($retorno_array);
					}else{
						$retorno_json = $retorno_principal;
					}
				}
            }
            
            $dados_consulta_efetuada['retorno'] = $retorno_principal;
            $dados_consulta_efetuada['retorno_json'] = $retorno_json;
            $dados_consulta_efetuada['tempo_retorno'] = strtotime(date('Y-m-d H:i:s')) - $milis_atual;

            $this->fornecedor->inserir_consulta_teste($dados_consulta_efetuada);

            redirect(current_url());
        }

        $historico = $this->fornecedor->retornar_historico_consulta_teste($id_consulta,$fornecedor->id_fornecedor)->result();

        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'teste','fornecedor'=>$fornecedor,'consulta'=>$consulta,'historico'=>$historico,'header'=>$header,'body'=>$body),true);
        $this->load->view('templates/maing',$this->parameters);
    }


    private function get_token($fornecedor){
        $token_check = $this->fornecedor->return_token($fornecedor->slug)->row();
        if($token_check!=null){
            return $token_check->token;
        }else{
            $dados = array('USUARIO'=>$fornecedor->usuario, 'SENHA'=>$fornecedor->senha);
            $token_json = $this->make_request_json($fornecedor->authurl, $this->prepara_url_utf8($fornecedor->authbody,$dados));
            $token_json = json_decode($token_json['retorno']);
            $this->fornecedor->inserir_token(array('fornecedor'=>$fornecedor->slug, 'token'=> $token_json->token));
            return $token_json->token;
        }
    }

    private function prepara_url_utf8($url,$parametros){
        $retorno = $url;
        foreach($parametros as $index => $parametro):
            $retorno = str_replace('{{'.$index.'}}',$parametro,$retorno);
        endforeach;
        return $retorno;
    }

	private function make_request_file($url, $file_path=null){
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$postData = array( 'file' => '@'.$file_path );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		*/
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($file_path)),
		));

		$return = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return array('status'=>$status, 'retorno'=>$return);
	}

    private function make_request($url = null){
        if($url==null) return null;

		var_dump($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40000); //timeout in seconds
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array('status'=>$status, 'retorno'=>$return);
    }

    public function make_request_json($url, $body, $header=null){
        $curl = curl_init();
        $crl_header = array('Content-Type: application/json');
        if($header!=null) { $crl_header = array_merge($header, $crl_header); }
        $opts = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $crl_header,
        );
        curl_setopt_array($curl, $opts);
        $return = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return array('status'=>$status, 'retorno'=>$return);
    }

    public function teste_error(){
        $url = 'https://v2.brasilcredit.com.br/api/consulta?login=1909301146&senha=d4ba6b7758&consulta=168&documento=06081729000116';
        try{
            $content = @file_get_contents($url);
            echo $content;
        }catch(Exception $e){
            var_dump($e);
        }
    }

    public function teste_bateria(){
        set_time_limit(300);
        $cnpjs = [
            /*
            "00067750000261",
            "00074073000128",
            "00085792000144",
            "00288250000179",
            "00348824000157",
            "00427711000147",
            "00437448000177",
            "00441017000184",
            "00535340000460",
            "00560759000129",
            "00572286000180",
            "00579029000170",
            "00684375000118",
            "00716173000100",
            "00716207000167",
            "00751886000105",
            "00771405000123",
            "00791022000117",
            "00827783003288",
            "00885021000131",
            "00967735000199",
            "01013296000148",
            "01018631000109",
            "01127192800012",
            "01205131000178",
            "01208063000109",
            "01254315000128",
            "01424961000196",
            "01476061000192",
            "01577222000134",
            "01668085000143",
            "01708132000216",
            "01742320000180",
            "01780512000180",
            "01878267000149",
            "01936127000180",
            "01947636000108",
            "01958293000187",
            "01988193000101",
            "01998370000122",
            "02015189000111",
            "02027010000146",
            "02068273000101",
            "02109372000186",
            "02129601000205",
            "02169905000115",
            "02218098000183",
            "02251802000108",
            "02284372000112",
            */
            "00062984000135",
            "00072795000143",
            "00085406000114",
            "00099805000134",
            "00103349000159",
            "00109395000165",
            "00115578000193",
            "00173802000101",
            "00209995000103",
            "00254473000115",
            "00264004000187",
            "00266450200014",
            "00266521000195",
            "00276395000150",
            "00286605000190",
            "00289864000256",
            "00351297000130",
            "00352451000198",
            "00361239000197",
            "00377281000104"
        ];

        $id_consulta = $this->verificar_parametro(3,'Consulta não informada');
        $consulta = $this->fornecedor->retornar_consulta($id_consulta)->row();
        $fornecedor = $this->fornecedor->retornar($consulta->id_fornecedor_fk)->row();
        $header = '';
        $body = '';
        if($fornecedor->isauth==1){
            $token = $this->get_token($fornecedor);
            $header = $this->prepara_url_utf8($consulta->header, array('TOKEN'=>$token));
            $body = $consulta->body;
        }

        $consulta->requisicao = str_replace("{{USUARIO}}",$fornecedor->usuario,$consulta->requisicao);
        $consulta->requisicao = str_replace("{{SENHA}}",$fornecedor->senha,$consulta->requisicao);

        $this->form_validation->set_rules('requisicao', 'Requsicao', 'required');

        if($this->form_validation->run()==TRUE) {
            foreach($cnpjs as $i => $cnpj):
                $url_preparada = $this->input->post('requisicao');
                $url_preparada = str_replace("{{CNPJ}}", $cnpj, $url_preparada);
                
                $milis_atual = strtotime(date('Y-m-d H:i:s'));
                $dados_consulta_efetuada = array();
                $dados_consulta_efetuada['id_fornecedor_consulta_fk'] = $consulta->id_fornecedor_consulta;
                $dados_consulta_efetuada['pesquisa'] = $cnpj;
                $dados_consulta_efetuada['nome'] = $consulta->nome;
                $dados_consulta_efetuada['requisicao'] = $url_preparada;
                $dados_consulta_efetuada['slug'] = $consulta->slug;
                $dados_consulta_efetuada['id_fornecedor_fk'] = $consulta->id_fornecedor_fk;

                if($fornecedor->isauth==1){
                    $dados_consulta_efetuada['header'] = $this->input->post('header');
                    $dados_consulta_efetuada['body'] = $this->input->post('body');
                    $header = explode(",",$this->input->post('header'));
                    $retorno_principal = $this->make_request_json($consulta->requisicao, $this->input->post('body'), $header);
                    $retorno_principal = $retorno_principal['retorno'];
                    $retorno_json = $retorno_principal;
                }else{
                    $retorno_principal = $this->make_request($url_preparada);
                    $retorno_principal = $retorno_principal['retorno'];

                    $retorno_principal = $this->analise_xml($retorno_principal);
                    $retorno_array = simplexml_load_string($retorno_principal);
                    $retorno_json = json_encode($retorno_array);
                }

                
                $dados_consulta_efetuada['retorno'] = $retorno_principal;
                $dados_consulta_efetuada['retorno_json'] = $retorno_json;
                $dados_consulta_efetuada['tempo_retorno'] = strtotime(date('Y-m-d H:i:s')) - $milis_atual;

                $this->fornecedor->inserir_consulta_teste_bateria($dados_consulta_efetuada);
            endforeach;

            redirect(current_url());
        }

        $historico = $this->fornecedor->retornar_historico_consulta_teste_bateria($id_consulta,$fornecedor->id_fornecedor)->result();

        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'teste_bateria','fornecedor'=>$fornecedor,'consulta'=>$consulta,'historico'=>$historico,'header'=>$header,'body'=>$body),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    public function teste_bateria_analise(){
        $id_consulta = $this->verificar_parametro(3,'Consulta não informada');
        $consulta = $this->fornecedor->retornar_consulta($id_consulta)->row();
        $fornecedor = $this->fornecedor->retornar($consulta->id_fornecedor_fk)->row();

        $retorno = array();
        $historico = $this->fornecedor->retornar_historico_consulta_teste_bateria($id_consulta,$fornecedor->id_fornecedor)->result();

        // Algoritmo de tratamento de XML
        foreach($historico as $i => $h):
            //echo $h->retorno;
            $teste_xml = simplexml_load_string($h->retorno);
            if($teste_xml==false){
                $line = $h;
                $line->retorno = $this->analise_xml($h->retorno);

                $retorno_array = simplexml_load_string($line->retorno);
                $retorno_json = json_encode($retorno_array);
                $line->retorno_json = $retorno_json;
                array_push($retorno, $line);
                $this->fornecedor->alterar_consulta_teste_bateria($h->id_consulta_teste, array('retorno'=>$line->retorno, 'retorno_json'=>$retorno_json));
            }else{
                array_push($retorno, $h);
            }
        endforeach;

        $this->parameters['content'] = $this->load->view('screens/fornecedor',array('content'=>'teste_bateria_analise','fornecedor'=>$fornecedor,'consulta'=>$consulta,'historico'=>$retorno),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    private function analise_xml($entrada){
        $retorno = $entrada;
        $retorno = str_replace("&aacute;","a",$retorno);
        $retorno = str_replace("&acirc;","a",$retorno);
        $retorno = str_replace("&agrave;","a",$retorno);
        $retorno = str_replace("&atilde;","a",$retorno);
        $retorno = str_replace("&ccedil;","c",$retorno);
        $retorno = str_replace("&eacute;","e",$retorno);
        $retorno = str_replace("&ecirc;","e",$retorno);
        $retorno = str_replace("&iacute;","i",$retorno);
        $retorno = str_replace("&oacute;","o",$retorno);
        $retorno = str_replace("&ocirc;","o",$retorno);
        $retorno = str_replace("&otilde;","o",$retorno);
        $retorno = str_replace("&uacute;","u",$retorno);
        $retorno = str_replace("&uuml;","u",$retorno);

        $retorno = str_replace("&Aacute;","a",$retorno);
        $retorno = str_replace("&Acirc;","a",$retorno);
        $retorno = str_replace("&Agrave;","a",$retorno);
        $retorno = str_replace("&Atilde;","a",$retorno);
        $retorno = str_replace("&Ccedil;","c",$retorno);
        $retorno = str_replace("&Eacute;","e",$retorno);
        $retorno = str_replace("&Ecirc;","e",$retorno);
        $retorno = str_replace("&Iacute;","i",$retorno);
        $retorno = str_replace("&Oacute;","o",$retorno);
        $retorno = str_replace("&Ocirc;","o",$retorno);
        $retorno = str_replace("&Otilde;","o",$retorno);
        $retorno = str_replace("&Uacute;","u",$retorno);
        $retorno = str_replace("&Uuml;","u",$retorno);

        $retorno = str_replace("&","e",$retorno);

        return $retorno;
    }
}
