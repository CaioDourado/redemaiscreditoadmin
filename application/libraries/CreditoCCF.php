<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditoCCF{
    public $cpf_procurado;
    public $data_hora;

    public $nome;
    public $cpf;
    public $quantidade;

    public $ocorrencias = array();
    public $passagens = array();

    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'redesia':
                    $this->cpf_procurado = $dados->HEADER->PARAMETROS->CPFCNPJ;
                    $this->data_hora = $dados->HEADER->DT_HORA_CONSULTA;

                    $this->nome = $dados->CH_SEM_FUNDOS_BACEN->CORRENTISTA;
                    $this->cpf = $dados->CH_SEM_FUNDOS_BACEN->CPFCNPJ;
                    $this->quantidade = $dados->CH_SEM_FUNDOS_BACEN->QUANTIDADE_OCORRENCIA;

                    if(isset($dados->CH_SEM_FUNDOS_BACEN->OCORRENCIAS->CHEQUE_ITEM)):
                        foreach($dados->CH_SEM_FUNDOS_BACEN->OCORRENCIAS->CHEQUE_ITEM as $index => $ocorrencia):
                            $cheque_atual = new stdClass();
                            $cheque_atual->banco = $ocorrencia->DCR_BANCO;
                            $cheque_atual->banco_numero = $ocorrencia->NUM_BANCO;
                            $cheque_atual->agencia = $ocorrencia->NUM_AGENCIA;
                            $cheque_atual->motivo_devolucao = $ocorrencia->MOTIVO_DEVOLUCAO;
                            $cheque_atual->qtd = $ocorrencia->QTD_CHEQUES;
                            $cheque_atual->ultima_ocorrencia = $ocorrencia->DT_ULTIMA_OCORRENCIA;
                            $cheque_atual->dados_agencia = $ocorrencia->DADOS_AGENCIA;
                            array_push($this->ocorrencias,$cheque_atual);
                        endforeach;
                    endif;

                    if(isset($dados->PASSAGENS_COMERCIAIS->PASSAGEM_COMERCIAL)):
                        foreach($dados->PASSAGENS_COMERCIAIS->PASSAGEM_COMERCIAL as $index => $passagem):
                            $passagem_atual = new stdClass();
                            $passagem_atual->data = $passagem->DATA_CONSULTA;
                            $passagem_atual->hora = $passagem->HORA_CONSULTA;
                            $passagem_atual->cliente = $passagem->CLIENTE_CONSULTA;
                            $passagem_atual->telefone = $passagem->TELEFONE_CLIENTE;
                            $passagem_atual->cidade = $passagem->CIDADE_UF_CLIENTE;
                            array_push($this->passagens,$passagem_atual);
                        endforeach;
                    endif;
                break;
        endswitch;
        return $this;
    }
}