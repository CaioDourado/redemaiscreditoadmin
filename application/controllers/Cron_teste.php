<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_teste extends CI_Controller{
    public function __construct(){
        parent::__construct();

        if(!$this->input->is_cli_request()){
            show_404();
            exit;
        }
    }

    public function registrar(){
        $agora = date('Y-m-d H:i:s');
        $resultado = 'Teste cron consultaadm executado em '.$agora.' por '.php_uname('n');

        $this->db->insert('teste', array(
            'resultado' => $resultado,
            'criado_em' => $agora
        ));

        if($this->db->affected_rows() > 0){
            echo 'OK - registro inserido em teste: '.$agora.PHP_EOL;
            return;
        }

        echo 'ERRO - nenhum registro inserido em teste: '.$agora.PHP_EOL;
    }
}
