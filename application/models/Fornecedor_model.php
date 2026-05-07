<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fornecedor_model extends ModelAuth {
    protected $tabela = 'fornecedor';
    protected $campo_id = 'id_fornecedor';
    protected $campo_principal = 'nome';

    public function inserir_consulta($dados){
        if ($dados != NULL) {
            $this->db->insert('fornecedor_consulta', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function retornar_consulta($id_consulta=null){
        if ($id_consulta != NULL) {
            $this->db->where(array('id_fornecedor_consulta'=>$id_consulta));
            return $this->db->get('fornecedor_consulta');
        }else{
            return false;
        }
    }

    public function retornar_consultas($id_fornecedor=null){
        if ($id_fornecedor != NULL) {
            $this->db->where(array('id_fornecedor_fk'=>$id_fornecedor));
            return $this->db->get('fornecedor_consulta');
        }else{
            return false;
        }
    }

    public function alterar_consulta($id_tabela=null,$dados=null){
        if($dados!=NULL&&$id_tabela!=null) {
            $this->db->update('fornecedor_consulta', $dados, array('id_fornecedor_consulta'=>$id_tabela));
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function inserir_consulta_teste($dados=null){
        if($dados!=null){
            $this->db->insert('consulta_teste', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    public function inserir_consulta_teste_bateria($dados=null){
        if($dados!=null){
            $this->db->insert('consulta_teste_bateria', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    public function alterar_consulta_teste_bateria($id_consulta=null,$dados=null){
        if($dados!=NULL&&$id_consulta!=null) {
            $this->db->update('consulta_teste_bateria', $dados, array('id_consulta_teste'=>$id_consulta));
            if($this->db->affected_rows()>0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function retornar_historico_consulta_teste($id_consulta,$id_fornecedor){
        if ($id_consulta != NULL && $id_fornecedor != NULL) {
            $this->db->where(array('id_fornecedor_consulta_fk'=>$id_consulta,'id_fornecedor_fk'=>$id_fornecedor));
            return $this->db->get('consulta_teste');
        }else{
            return false;
        }
    }

    public function retornar_historico_consulta_teste_bateria($id_consulta,$id_fornecedor){
        if ($id_consulta != NULL && $id_fornecedor != NULL) {
            $this->db->where(array('id_fornecedor_consulta_fk'=>$id_consulta,'id_fornecedor_fk'=>$id_fornecedor));
            return $this->db->get('consulta_teste_bateria');
        }else{
            return false;
        }
    }

    public function inserir_token($dados){
        if ($dados != NULL) {
            $this->db->insert('fornecedor_token', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function return_token($fornecedor=null){
        $sql = 'SELECT * FROM fornecedor_token WHERE fornecedor = "'.$fornecedor.'" AND criado_em BETWEEN "'.date('Y-m-d').' 00:00" AND "'.date('Y-m-d').' 23:59" ';
        return $this->db->query($sql);
    }
}