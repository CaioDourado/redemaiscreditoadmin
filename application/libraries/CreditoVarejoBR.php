<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditoVarejoBR{
    public $cpf_procurado;
    public $data_hora;

    public $cpf;
    public $data_nascimento;
    public $nome;
    public $mae;

    public $resumo = array();
    public $ocorrencias = array();

    public function iniciar($fornecedor, $dados){
        switch ($fornecedor):
            case 'redesia':
                    $this->cpf_procurado = $dados->HEADER->PARAMETROS->CPFCNPJ;
                    $this->data_hora = $dados->HEADER->DT_HORA_CONSULTA;

                    $this->cpf = $dados->HEADER->PARAMETROS->CPFCNPJ;
                    $this->nome = $dados->DADOS_RECEITA_FEDERAL->NOME;
                    $this->data_nascimento = date('d/m/Y',strtotime($dados->DADOS_RECEITA_FEDERAL->DATA_NASCIMENTO_FUNDACAO));
                    $this->mae = $dados->DADOS_RECEITA_FEDERAL->NOME_MAE;

                    $linhas = $dados->PEND_FINANCEIRAS->OCORRENCIAS->PENDENCIA_FINANCEIRA_OCORRENCIA;

                    if($linhas!=null):
                        if(count($linhas)>0){
                            $this->resumo['pendencias'] = new stdClass();
                            $this->resumo['pendencias']->qtd = count($linhas);
                            $this->resumo['pendencias']->mais_antigo = $linhas[0]->DATA_INCLUSAO;
                            $this->resumo['pendencias']->valor = 0;
                        }
                    endif;

                    foreach($linhas as $index => $linha):
                        $ocorrencia_atual = new stdClass();
                        $ocorrencia_atual->data_vencimento = date('d/m/Y',strtotime($linha->DATA_VENCIMENTO));
                        $ocorrencia_atual->data_inclusao = date('d/m/Y',strtotime($linha->DATA_INCLUSAO));
                        $ocorrencia_atual->tipo_devedor = $linha->TIPO_DEVEDOR;
                        $ocorrencia_atual->credor = $linha->CREDOR;
                        $ocorrencia_atual->valor = $linha->VALOR;
                        $ocorrencia_atual->contrato = $linha->CONTRATO;
                        $ocorrencia_atual->origem = $linha->ORIGEM;
                        array_push($this->ocorrencias,$ocorrencia_atual);

                        $this->resumo['pendencias']->mais_recente = $linha->DATA_INCLUSAO;
                        $this->resumo['pendencias']->valor += only_numbers($linha->VALOR);
                    endforeach;

                    if(isset($this->resumo['pendencias'])) $this->resumo['pendencias']->valor = $this->resumo['pendencias']->valor/100;
                break;
        endswitch;
        return $this;
    }
}