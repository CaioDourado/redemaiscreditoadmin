<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sistema_configuracao_model extends CI_Model {
    private $tabela = 'sistema_configuracao';

    public function obter($escopo, $chave){
        if(!$this->db->table_exists($this->tabela)) return null;

        return $this->db
            ->where('escopo', $escopo)
            ->where('chave', $chave)
            ->limit(1)
            ->get($this->tabela)
            ->row();
    }

    public function booleano($escopo, $chave, $fallback=false){
        $configuracao = $this->obter($escopo, $chave);
        if($configuracao===null) return (bool) $fallback;

        return $this->valor_booleano($configuracao->valor, $fallback);
    }

    public function alternar_booleano($escopo, $chave, $id_usuario=null){
        if(!$this->db->table_exists($this->tabela)){
            return array('ok'=>false, 'mensagem'=>'Tabela de configuracao nao encontrada.');
        }

        $this->db->trans_begin();
        $configuracao = $this->db->query(
            'SELECT * FROM `'.$this->tabela.'` WHERE `escopo` = ? AND `chave` = ? LIMIT 1 FOR UPDATE',
            array($escopo, $chave)
        )->row();

        if($configuracao===null){
            $this->db->trans_rollback();
            return array('ok'=>false, 'mensagem'=>'Configuracao nao encontrada.');
        }

        $anterior = $this->valor_booleano($configuracao->valor, false);
        $atual = !$anterior;
        $this->db
            ->where('id_sistema_configuracao', $configuracao->id_sistema_configuracao)
            ->update($this->tabela, array(
                'valor' => $atual ? '1' : '0',
                'id_usuario_atualizacao_fk' => $id_usuario
            ));

        if($this->db->trans_status()===false){
            $this->db->trans_rollback();
            return array('ok'=>false, 'mensagem'=>'Nao foi possivel atualizar a configuracao.');
        }

        $this->db->trans_commit();
        return array(
            'ok'=>true,
            'id'=>(int) $configuracao->id_sistema_configuracao,
            'anterior'=>$anterior,
            'atual'=>$atual
        );
    }

    private function valor_booleano($valor, $fallback){
        if(is_bool($valor)) return $valor;
        if($valor===null || trim((string) $valor)==='') return (bool) $fallback;

        $normalizado = strtolower(trim((string) $valor));
        if(in_array($normalizado, array('1','true','sim','yes','on'), true)) return true;
        if(in_array($normalizado, array('0','false','nao','não','no','off'), true)) return false;
        return (bool) $fallback;
    }
}
