<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminauditoria_model extends CI_Model{
    public function registrar($dados=array()){
        if(!$this->db->table_exists('adm_auditoria')){
            return false;
        }

        $registro = array(
            'area' => isset($dados['area']) ? $dados['area'] : 'admin',
            'acao' => isset($dados['acao']) ? $dados['acao'] : 'evento',
            'status' => isset($dados['status']) ? $dados['status'] : 'info',
            'referencia_tipo' => isset($dados['referencia_tipo']) ? $dados['referencia_tipo'] : null,
            'referencia_id' => isset($dados['referencia_id']) ? $dados['referencia_id'] : null,
            'id_usuario_fk' => isset($dados['id_usuario_fk']) ? $dados['id_usuario_fk'] : $this->session->userdata('id'),
            'http_status' => isset($dados['http_status']) ? $dados['http_status'] : null,
            'erro' => isset($dados['erro']) ? $dados['erro'] : null,
            'mensagem' => isset($dados['mensagem']) ? $dados['mensagem'] : null,
            'contexto' => isset($dados['contexto']) ? $this->encode($dados['contexto']) : null,
            'retorno' => isset($dados['retorno']) ? $this->encode($dados['retorno']) : null
        );

        $this->db->insert('adm_auditoria', $registro);
        return $this->db->affected_rows()>0;
    }

    private function encode($valor){
        if($valor===null || $valor===''){
            return null;
        }

        if(is_string($valor)){
            return $valor;
        }

        return json_encode($valor, JSON_UNESCAPED_UNICODE);
    }
}
