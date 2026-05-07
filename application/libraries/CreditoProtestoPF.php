<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditoProtestoPF{

    public $cpf_procurado;
    public $data_hora;

    public $cpf;
    public $nome;
    public $mae;

    public $mensagens;

    public $resumo = array('protestos'=>null);
    public $protestos = array();

    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'sinpc':
                $this->cpf_procurado = $dados->dados_utilizados->cpf;
                $this->data_hora = $dados->dados_utilizados->data_hora;

                $this->mensagens = $dados->mensagem;

                foreach($dados->protestos->protesto as $index => $protesto):
                    foreach($protesto->ocorrencias->ocorrencia as $indice => $ocorrencia):
                        $dados_ocorrencia = new stdClass();
                        $dados_ocorrencia->nome = $protesto->cartorio->nome;
                        $dados_ocorrencia->cidade = $protesto->cartorio->cidade;
                        $dados_ocorrencia->endereco = $protesto->cartorio->endereco;
                        $dados_ocorrencia->codigo_cidade = $protesto->cartorio->codigo_da_cidade;
                        $dados_ocorrencia->uf = $protesto->cartorio->uf;
                        $dados_ocorrencia->telefone = $protesto->cartorio->telefone;
                        $dados_ocorrencia->data = data_pt($ocorrencia->data,false);
                        $dados_ocorrencia->valor = dinheiro(floatval($ocorrencia->valor));
                        array_push($this->protestos,$dados_ocorrencia);
                    endforeach;
                endforeach;
            break;
        endswitch;
        return $this;
    }
}