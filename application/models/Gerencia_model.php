<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gerencia_model extends ModelAuth{
    function return_clientes_group_status(){
        return $this->db->query('SELECT  status ,COUNT(*) AS qtd FROM cliente GROUP BY status');
    }

    function return_clientes_group_status_array(){
        $retorno = array( '1' => array('status'=>'Ativo','qtd'=> 0), '2' => array('status'=>'Inadimplente','qtd'=> 0),'0' => array('status'=>'Cancelado','qtd'=> 0));
        $data = $this->return_clientes_group_status()->result();
        foreach($data as $index => $l):
            $retorno[$l->status]['qtd'] = $l->qtd;
        endforeach;
        return $retorno;
    }

    function return_clientes_group_inadimplencia($days = 60){
        $sql = 'SELECT UPPER(nome_sacado) AS nome, COUNT(*) AS qtd, SUM(valor_boleto) AS valor FROM boleto WHERE (pago = 0) AND (data_vencimento > NOW() - INTERVAL '.$days.' DAY) AND data_vencimento < NOW() GROUP BY nome_sacado ORDER BY nome_sacado';
        return $this->db->query($sql);
    }
}