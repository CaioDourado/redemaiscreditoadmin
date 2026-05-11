<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

}

class ControllerAuth extends CI_Controller{

    protected $parameters;
    protected $session_ci;

    public function __construct(){
        parent::__construct();
        $this->guard_session();
        $this->parameters['title'] = APP_NAME;
        $this->parameters['menu'] = $this->load_menu('padrao_novo');
        $this->parameters['breadcrumb'] = array(array('inicio','Início'));
    }

    private function guard_session(){
        if(is_cli()){
            return;
        }

        if($this->is_public_route()){
            return;
        }

        if($this->session->userdata('logado')!=null){
            return;
        }

        set_msg('Sessao expirada. Entre novamente.');
        redirect('inicio');
    }

    private function is_public_route(){
        $class = strtolower($this->router->fetch_class());
        $method = strtolower($this->router->fetch_method());

        $public_routes = array(
            'inicio' => array('index', 'sair'),
            'boletov3' => array('retorno', 'retorno_req', 'check_retorno')
        );

        return isset($public_routes[$class]) && in_array($method, $public_routes[$class]);
    }

    private function auth(){
        $token_content = get_token_content();
        if($token_content!=null){
            $this->auth2($token_content->id,$token_content->uuid);
        }else{
            $this->close_session();
            $this->redirect_login('Não foi possível acessar a sessão, por favor entre novamente.');
        }
    }

    private function auth2($id,$uuid){

    }

    private function redirect_login($msg=null){
        if($msg!=null) $this->session->set_flashdata('erro',$msg);
        redirect('login');
    }

    public function close_session(){
        $this->session->unset_userdata(array('name', 'Inicio','token','permissions'));
    }

    public function verificar_parametro($indice=3,$mensagem='Parâmetro Não Informado.',$redirect=null){
        $param = $this->uri->segment($indice);
        if($param==null){
            set_msg($mensagem);
            if($redirect==null) redirect('inicio'); else redirect($redirect);
        }
        return $param;
    }

    public function load_menu($menu, $param = null, $mais_opcoes = true ,$file = 'components/menu'){
        $retorno = $this->load->view($file, array('menu'=>$menu,'param'=> $param),true);
        if($mais_opcoes) $retorno .= $this->load->view($file, array('menu'=> 'mais_opcoes', 'param'=> $param),true);
        return $retorno;
    }
}

class ControllerAuthSystem extends CI_Controller{
    protected $parameters;
    protected $session_ci;

    public function __construct(){
        parent::__construct();
        //$this->auth();
        $this->parameters['title'] = APP_NAME;
        $this->parameters['menu'] = $this->load->view('components/menu_sistema',array('menu'=>'padrao'),true);
        $this->parameters['breadcrumb'] = array(array('sistema','Início'));
    }

    public function verificar_parametro($indice=3,$mensagem='Parâmetro Não Informado.',$redirect=null){
        $param = $this->uri->segment($indice);
        if($param==null){
            set_msg($mensagem);
            if($redirect==null) redirect('inicio'); else redirect($redirect);
        }
        return $param;
    }
}
