<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diario extends ControllerAuth{
    public function __construct(){
        parent::__construct();
        $this->load->model('diario_model','diario');
    }

    public function index(){
        $semana = $this->diario->retornar_semana()->result();

        $dados = array();
        foreach($semana as $index => $item):
            $simple_date = date('d_m_Y',strtotime($item->criado_em));
            if(!isset($dados[$simple_date])) $dados[$simple_date] = array();

            $item->status = '';
            if($item->nome=="Negativação"){
                if($item->slug=="negativacaoscpcpf"||$item->slug=="negativacaoscpcpj"){
                    //$item->nome .= ' SCPC';
                    if (strpos($item->retorno, 'ERRO') !== false){
                        $item->status = 'danger';
                    }else{
                        $item->status = 'success';
                    }
                }else{
                    //$item->nome .= ' Pefin';
                    $retorno_json = simplexml_load_string($item->retorno);
                    if(isset($retorno_json->inclusao->erro)){
                        $item->status = 'danger';
                    }else{
                        $item->status = 'success';
                    }
                }
            }

            array_push($dados[$simple_date],$item);
        endforeach;

        $this->parameters['content'] = $this->load->view('screens/diario',array('content'=>'index','dados'=>$dados),true);
        $this->load->view('templates/maing',$this->parameters);
    }

    function financeiro(){
        
    }
}