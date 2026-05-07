<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Franquia_model extends ModelAuth {
    public function inserir_franquia($dados){
        if ($dados != NULL) {
            $this->db->insert('franquia', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function inserir_unidade($dados){
        if ($dados != NULL) {
            $this->db->insert('franquia_unidade', $dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function retornar_franquia($id_franquia=null){
        $this->db->where(array('id_franquia'=>$id_franquia));
        return $this->db->get('franquia');
    }

    public function retornar_unidade($id_unidade=null){
        $this->db->where(array('id_unidade_franquia'=>$id_unidade));
        return $this->db->get('franquia_unidade');
    }

    public function retornar_unidades($id_franquia){
        $sql = 'SELECT *,(SELECT COUNT(*) FROM cliente WHERE id_unidade_fk = id_unidade_franquia) AS clientes FROM franquia_unidade WHERE id_franquia_fk = '.$id_franquia;
        return $this->db->query($sql);
    }

    public function retornar_faturas($id_franquia=null){
        if($id_franquia!=null):
            $this->db->where(array('id_franquia_fk'=>$id_franquia));
            return $this->db->get('adm_franquia_fatura');
        else:
            return null;
        endif;
    }

    public function retornar_fatura($id_fatura=null){
        if($id_fatura!=null):
            $this->db->where(array('id_adm_franquia_fatura'=>$id_fatura));
            return $this->db->get('adm_franquia_fatura');
        else:
            return null;
        endif;
    }

    public function retornar_fatura_itens($id_fatura=null){
        if($id_fatura!=null):
            $this->db->where(array('id_fatura_fk'=>$id_fatura));
            return $this->db->get('adm_franquia_fatura_item');
        else:
            return null;
        endif;
    }

    public function retornar_franquias(){
        $sql  = 'SELECT *, ';
        $sql .= '(SELECT COUNT(*) FROM franquia_unidade WHERE tbmain.id_franquia = id_franquia_fk ) AS unidades, ';
        $sql .= '(SELECT COUNT(*) FROM cliente WHERE tbmain.id_franquia = id_franquia_fk ) AS clientes ';
        $sql .= 'FROM franquia AS tbmain';
        return $this->db->query($sql);
    }

    public function inserir_adm_franquia_fatura($dados){
        if($dados!=null):
            $this->db->insert('adm_franquia_fatura',$dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }

    public function id_ultima_fatura(){
        $sql = 'SELECT id_adm_franquia_fatura FROM adm_franquia_fatura ORDER BY id_adm_franquia_fatura DESC LIMIT 1';
        return $this->db->query($sql)->row()->id_adm_franquia_fatura;
    }

    public function franquia_qtd_clientes($id_franquia,$max_date){
        $sql  = 'SELECT COUNT(*)AS qtd FROM cliente WHERE id_franquia_fk = '.$id_franquia.' AND consultor = 0 AND (criado_em < "'.$max_date.'" AND status > 0)';
        return $this->db->query($sql)->row()->qtd;
    }

    public function inserir_fatura_item($dados){
        if($dados!=null):
            $this->db->insert('adm_franquia_fatura_item',$dados);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }
}