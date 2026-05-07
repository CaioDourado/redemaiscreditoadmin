<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Boleto_aberto extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('boleto_model', 'boleto');
    }

    public function Index(){
        echo 'eoq';
    }

    public function visualizar_hash(){
        $hash = $this->uri->segment(3);
        if($hash==null) redirect('https://redemaiscredito.com.br/');

        $boleto = $this->boleto->retornar_hash($hash)->row();
        $conta = $this->boleto->retorna_conta($boleto->id_conta_banco)->row();
        $this->carrgar_boleto_pdf($boleto,$conta);
    }

    private function carrgar_boleto_pdf($boleto,$conta){
        $pdf = array();
        $pdf['conteudo'] = $this->load->view('components/boleto',array('boleto'=>$boleto,'conta'=>$conta),true);
        $pdf['titulo'] = 'Boleto Rede Mais Crédito';
        $pdf['senha'] = null;
        $this->load->view('components/pdf',$pdf);
    }
}