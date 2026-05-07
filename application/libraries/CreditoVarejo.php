<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditoVarejo{
    public $cpf_procurado;
    public $data_hora;

    public $cpf;
    public $nome;
    public $mae;

    public $mensagens;

    public $resumo = array('pendencias'=>null);
    public $pendencias_financeiras = array();


    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'sinpc':
                    $this->cpf_procurado = $dados->dados_consulta->dados_utilizados->cpf;
                    $this->data_hora = $dados->dados_consulta->dados_utilizados->data_hora;

                    $this->cpf = $dados->dados_cadastrais->cpf;
                    $this->nome = $dados->dados_cadastrais->nome;
                    $this->mae = $dados->dados_cadastrais->nome_da_mae;

                    $this->mensagens = $dados->mensagem;

                    $this->pendencias_financeiras = $dados->pendencia_financeira->registro;

                    if($this->pendencias_financeiras!=null):
                        if(count($this->pendencias_financeiras)>0){
                            $this->resumo['pendencias'] = new stdClass();
                            $this->resumo['pendencias']->qtd = count($this->pendencias_financeiras);
                            $this->resumo['pendencias']->mais_antigo = $this->pendencias_financeiras[0]->ocorrencia;
                            $this->resumo['pendencias']->valor = 0;
                            foreach($this->pendencias_financeiras as $index => $pendencia):
                                $this->resumo['pendencias']->mais_recente = $pendencia->ocorrencia;
                                $this->resumo['pendencias']->valor += only_numbers($pendencia->valor);
                            endforeach;
                            $this->resumo['pendencias']->valor = $this->resumo['pendencias']->valor/100;
                        }
                    endif;
                break;
        endswitch;
        return $this;
    }
}