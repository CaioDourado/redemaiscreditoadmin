<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends ControllerAuth {

    public function __construct(){
        parent::__construct();
    }

    public function Index(){
        if($this->session->userdata('logado')!=null){
            $this->parameters['pg_title'] = '<i class="fa fa-home"></i> Rede Mais Crédito';
            $this->parameters['pg_subtitle'] = 'Tela Inicial do Sistema';

            $this->load->model('gerencia_model','gerencia');
            switch($this->session->userdata('adm_nivel')):
                case 2:
                        $content = $this->load->view('screens/inicio',array('content'=>'super'),true);
                    break;
                case 3:
                        $clientes_status = $this->gerencia->return_clientes_group_status_array();
                        $clientes_inadimplentes = $this->gerencia->return_clientes_group_inadimplencia()->result();
                        $content = $this->load->view('screens/inicio',array_merge(array('content'=>'admin','status'=>$clientes_status,'inadimplentes'=>$clientes_inadimplentes), $this->dados_aberturas_clientes()),true);
                    break;
                default:
                        $clientes_status = $this->gerencia->return_clientes_group_status_array();
                        $clientes_inadimplentes = $this->gerencia->return_clientes_group_inadimplencia()->result();
                        $content = $this->load->view('screens/inicio',array_merge(array('content'=>'admin','status'=>$clientes_status,'inadimplentes'=>$clientes_inadimplentes), $this->dados_aberturas_clientes()),true);
                    break;
            endswitch;

            $this->parameters['content'] = $content;
            $this->load->view('templates/maing.php',$this->parameters);
        }else{

            $this->form_validation->set_rules('usuario', 'Usuário', 'required');
            $this->form_validation->set_rules('senha', 'Senha', 'required');

            if($this->form_validation->run()==TRUE) {
                $usuario = $this->input->post('usuario');
                $senha = $this->input->post('senha');
                $login_ok = false;

                $lat = $this->input->post('lat');
                $lng = $this->input->post('lng');
                $ts = $this->input->post('timestamp');
                $ip = $this->input->ip_address();
                $localizacao = 'https://maps.google.com/?q='.$lat.','.$lng;

                $this->load->model('sistema_model','sistema');
                switch($usuario):
                    case 'admin':
                            if($senha==SENHA_ADM){
                                $this->session->set_userdata(array('logado'=>'ready','adm_nivel'=>1));
                                $this->sistema->criar_sessao(array('lat'=>$lat,'lng'=>$lng,'timestamp'=>$ts,'ip'=>$ip,'localizacao'=>$localizacao));
                                $login_ok = true;
                            }
                        break;
                    case 'supervisor':
                            if($senha==SENHA_SUPER){
                                $this->session->set_userdata(array('logado'=>'ready','adm_nivel'=>2));
                                $this->sistema->criar_sessao(array('lat'=>$lat,'lng'=>$lng,'timestamp'=>$ts,'ip'=>$ip,'localizacao'=>$localizacao));
                                $login_ok = true;
                            }
                        break;
                    case 'gerencia':
                            if($senha==SENHA_GERENCIA){
                                $this->session->set_userdata(array('logado'=>'ready','adm_nivel'=>3));
                                $this->sistema->criar_sessao(array('lat'=>$lat,'lng'=>$lng,'timestamp'=>$ts,'ip'=>$ip,'localizacao'=>$localizacao));
                                $login_ok = true;
                            }
                        break;
                    default:
                        break;
                endswitch;
                if(!$login_ok){
                    set_msg('Usuario ou senha invalidos.');
                }
                redirect(current_url());
            }
            $this->load->view('templates/login.php',null);
        }
    }

    public function teste(){
        $this->load->view('templates/maing',null);
    }

    public function sair(){
        $this->session->unset_userdata('logado');
        redirect('inicio');
    }

    private function dados_aberturas_clientes(){
        $inicio_mensal = date('Y-m-01', strtotime('-11 months'));
        $mensal_por_periodo = array();
        foreach($this->gerencia->return_clientes_abertos_por_mes($inicio_mensal)->result() as $linha){
            $mensal_por_periodo[$linha->periodo] = $linha;
        }

        $aberturas_mensais = array();
        for($i = 11; $i >= 0; $i--){
            $periodo = date('Y-m', strtotime('-'.$i.' months'));
            $linha = isset($mensal_por_periodo[$periodo]) ? $mensal_por_periodo[$periodo] : null;
            $aberturas_mensais[] = array(
                'periodo' => $periodo,
                'label' => date('m/Y', strtotime($periodo.'-01')),
                'matriz' => $linha !== null ? (int) $linha->matriz : 0,
                'franquia' => $linha !== null ? (int) $linha->franquia : 0
            );
        }

        $inicio_semana_atual = new DateTime(date('Y-m-d'));
        $inicio_semana_atual->modify('-'.(int) $inicio_semana_atual->format('w').' days');
        $inicio_semanal = clone $inicio_semana_atual;
        $inicio_semanal->modify('-14 weeks');
        $semanal_por_periodo = array();
        foreach($this->gerencia->return_clientes_abertos_por_semana($inicio_semanal->format('Y-m-d'))->result() as $linha){
            $semanal_por_periodo[$linha->periodo] = $linha;
        }

        $aberturas_semanais = array();
        for($i = 0; $i < 15; $i++){
            $semana = clone $inicio_semanal;
            if($i > 0) $semana->modify('+'.$i.' weeks');
            $periodo = $semana->format('Y-m-d');
            $linha = isset($semanal_por_periodo[$periodo]) ? $semanal_por_periodo[$periodo] : null;
            $aberturas_semanais[] = array(
                'periodo' => $periodo,
                'label' => $semana->format('d/m'),
                'matriz' => $linha !== null ? (int) $linha->matriz : 0,
                'franquia' => $linha !== null ? (int) $linha->franquia : 0
            );
        }

        return array('aberturas_mensais' => $aberturas_mensais, 'aberturas_semanais' => array_reverse($aberturas_semanais));
    }
}
