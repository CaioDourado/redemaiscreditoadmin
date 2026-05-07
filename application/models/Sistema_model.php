<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sistema_model extends CI_Model {
    public function criar_sessao($dados=null){
        if ($dados != NULL) {
            $this->db->insert('sessao_adm', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function inserir_consulta_efetuada($dados = null){
        if ($dados != NULL) {
            $this->db->insert('consulta_efetuada', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function retornar_consulta_mais_barata($slug_consulta=null){
        if($slug_consulta!=null){
            $query  = 'SELECT tbmain.*,tb2.chave,tb2.usuario,tb2.senha,tb3.slug AS consulta_slug,tb3.nome AS nome_real,tb2.slug AS fornecedor,tb3.venda FROM `fornecedor_consulta` AS tbmain ';
            $query .= 'LEFT JOIN fornecedor AS tb2 ON tbmain.id_fornecedor_fk = tb2.id_fornecedor ';
            $query .= 'LEFT JOIN consulta AS tb3 ON tbmain.slug = tb3.slug ';
            $query .= 'WHERE tbmain.slug = "'.$slug_consulta.'" ORDER BY custo ASC LIMIT 1';
            return $this->db->query($query);
        }else{
            return false;
        }
    }

    public function retornar_consulta_efetuada($id_consulta=null,$id_cliente){
        $sql  = 'SELECT tbmain.*,tb2.retorno AS endereco,tb2.fornecedor AS endereco_fornecedor, tb3.retorno AS cheque, tb3.fornecedor AS cheque_fornecedor, tb4.retorno AS protestos, tb4.fornecedor AS protestos_fornecedor FROM consulta_efetuada AS tbmain ';
        $sql .= 'LEFT JOIN consulta_efetuada AS tb2 ON tbmain.endereco_id = tb2.id_consulta_efetuada ';
        $sql .= 'LEFT JOIN consulta_efetuada AS tb3 ON tbmain.cheque_id = tb3.id_consulta_efetuada ';
        $sql .= 'LEFT JOIN consulta_efetuada AS tb4 ON tbmain.cheque_id = tb4.id_consulta_efetuada ';
        $sql .= 'WHERE tbmain.id_consulta_efetuada = '.$id_consulta.' AND tbmain.id_cliente_fk = '.$id_cliente.' ';
        $sql .= 'LIMIT 1';
        return $this->db->query($sql);
    }

    public function ultimas_consultas($id_cliente=null,$qtd=10){
        if($id_cliente!=null){
            $this->db->where(array('id_cliente_fk'=>$id_cliente));
            $this->db->order_by('criado_em','DESC');
            $this->db->limit($qtd);
            return $this->db->get('consulta_efetuada');
        }else{
            return false;
        }
    }

    public function retornar_id_ultima_consulta($id_cliente,$id_usuario){
        $this->db->where(array('id_cliente_fk'=>$id_cliente,'id_usuario_fk'=>$id_usuario));
        $this->db->order_by('id_consulta_efetuada','DESC');
        $this->db->limit(1);
        $retorno = $this->db->get('consulta_efetuada');
        return $retorno->row()->id_consulta_efetuada;
    }
}