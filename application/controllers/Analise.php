<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Analise extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('analise_model','analise');
    }

    public function index(){

    }

    public function negativacoes(){
        $negativacoes = $this->analise->retornar_negativacoes()->result();

        $erros = array();
        $sucessos = array();
        foreach($negativacoes as $index => $negativacao):
            $retorno_atual = json_decode($negativacao->retorno_json);
            if(isset($retorno_atual->inclusao->erro)) array_push($erros,$negativacao);
            else array_push($sucessos,$negativacao);
        endforeach;

        $conteudo = $this->load->view('screens/analise',array('content'=>'negativacoes','erros'=>$erros,'sucessos'=>$sucessos),true);
        $this->load->view('templates/relatorio',array('conteudo'=>$conteudo));
    }
}