<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CadastroCPF{
    public $nome_procurado;
    public $data_hora;
    public $pessoas = array();

    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'sinpc':
                $this->nome_procurado = $dados->dados_consulta->dados_utilizados->nome;
                $this->data_hora = $dados->dados_consulta->dados_utilizados->data_hora;
                $pessoas = $dados->pessoa->registro;
                foreach($pessoas as $index => $pessoa):
                    $pessoa_now = new stdClass();
                    $pessoa_now->nome = $pessoa->nome;
                    $pessoa_now->cpf = $pessoa->cpf;
                    $pessoa_now->idade = $pessoa->idade;
                    $pessoa_now->cidade = $pessoa->cidade;
                    $pessoa_now->uf = $pessoa->uf;
                    array_push($this->pessoas,$pessoa_now);
                endforeach;
                break;
        endswitch;
        return $this;
    }
}