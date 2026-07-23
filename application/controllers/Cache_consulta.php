<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cache_consulta extends ControllerAuth {
    private $csrf_session_key = 'cache_consulta_csrf';

    public function __construct(){
        parent::__construct();
        $this->load->model('cache_consulta_model', 'cache_consulta');
        $this->load->model('adminauditoria_model', 'adminauditoria');
        $this->parameters['title'] = 'Cache de Consultas';
        $this->parameters['title_window'] = 'Cache de Consultas';
        $this->parameters['menu'] = $this->load_menu('padrao_novo');
        array_push($this->parameters['breadcrumb'], array('cache_consulta', 'Cache de Consultas'));
    }

    public function index(){
        $documento_informado = trim((string) $this->input->get('documento', true));
        $documento = '';
        $erro = null;
        $caches = array();
        $estrutura_disponivel = $this->cache_consulta->estrutura_disponivel();

        if(!$estrutura_disponivel){
            $erro = 'A estrutura de cache ainda nao esta disponivel neste banco de dados.';
        }elseif($documento_informado!==''){
            $documento = $this->normalizar_documento($documento_informado);
            if(!$this->documento_valido($documento)){
                $erro = 'Informe um CPF com 11 digitos ou um CNPJ com 14 digitos.';
            }else{
                $caches = $this->cache_consulta->localizar_por_documento($documento);
            }
        }

        $this->parameters['pg_title'] = '<i class="fa fa-database"></i> Cache de Consultas';
        $this->parameters['pg_subtitle'] = 'Pesquise os caches de um CPF ou CNPJ e invalide-os quando necessario.';
        $this->parameters['content'] = $this->load->view('screens/cache_consulta', array(
            'content'=>'index',
            'documento_informado'=>$documento_informado,
            'documento'=>$documento,
            'tipo_documento'=>$this->tipo_documento($documento),
            'caches'=>$caches,
            'erro'=>$erro,
            'estrutura_disponivel'=>$estrutura_disponivel,
            'csrf_token'=>$this->csrf_token()
        ), true);
        $this->load->view('templates/maing', $this->parameters);
    }

    public function excluir_documento(){
        if(strtoupper((string) $this->input->server('REQUEST_METHOD'))!=='POST'){
            show_error('Metodo nao permitido.', 405);
        }

        if(!$this->csrf_valido($this->input->post('csrf_token'))){
            show_error('A sessao do formulario expirou. Atualize a pagina e tente novamente.', 419);
        }

        $documento = $this->normalizar_documento($this->input->post('documento'));
        if(!$this->documento_valido($documento)){
            set_msg('CPF ou CNPJ invalido.');
            redirect('cache_consulta');
        }

        $quantidade_antes = count($this->cache_consulta->localizar_por_documento($documento));
        $resultado = $this->cache_consulta->excluir_por_documento($documento);
        $this->session->unset_userdata($this->csrf_session_key);

        if(!empty($resultado['ok'])){
            $this->adminauditoria->registrar(array(
                'area'=>'cache_consulta',
                'acao'=>'excluir_por_documento',
                'status'=>'sucesso',
                'referencia_tipo'=>$this->tipo_documento($documento),
                'mensagem'=>'Caches de documento removidos pelo administrativo.',
                'contexto'=>array(
                    'documento_mascarado'=>$this->mascarar_documento($documento),
                    'documento_hash'=>hash('sha256', $documento),
                    'quantidade_antes'=>$quantidade_antes,
                    'quantidade_excluida'=>(int) $resultado['quantidade']
                )
            ));
            set_msg(
                (int) $resultado['quantidade'].' cache(s) removido(s) com sucesso. As proximas consultas chamarao os fornecedores novamente.',
                'sucesso'
            );
        }else{
            $this->adminauditoria->registrar(array(
                'area'=>'cache_consulta',
                'acao'=>'excluir_por_documento',
                'status'=>'erro',
                'erro'=>'CACHE_DELETE_FAILED',
                'mensagem'=>'Falha ao remover caches de documento.',
                'contexto'=>array(
                    'documento_mascarado'=>$this->mascarar_documento($documento),
                    'documento_hash'=>hash('sha256', $documento),
                    'quantidade_antes'=>$quantidade_antes
                )
            ));
            set_msg('Nao foi possivel remover os caches deste documento.');
        }

        redirect('cache_consulta?documento='.rawurlencode($documento));
    }

    private function normalizar_documento($documento){
        return preg_replace('/\D+/', '', trim((string) $documento));
    }

    private function documento_valido($documento){
        $tamanho = strlen((string) $documento);
        return $tamanho===11 || $tamanho===14;
    }

    private function tipo_documento($documento){
        if(strlen((string) $documento)===11) return 'cpf';
        if(strlen((string) $documento)===14) return 'cnpj';
        return null;
    }

    private function mascarar_documento($documento){
        if(strlen($documento)===11){
            return substr($documento, 0, 3).'.***.***-'.substr($documento, -2);
        }
        if(strlen($documento)===14){
            return substr($documento, 0, 2).'.***.***/****-'.substr($documento, -2);
        }
        return '';
    }

    private function csrf_token(){
        $token = (string) $this->session->userdata($this->csrf_session_key);
        if($token===''){
            $token = bin2hex(random_bytes(32));
            $this->session->set_userdata($this->csrf_session_key, $token);
        }
        return $token;
    }

    private function csrf_valido($token){
        $esperado = (string) $this->session->userdata($this->csrf_session_key);
        return $esperado!=='' && is_string($token) && hash_equals($esperado, $token);
    }
}
