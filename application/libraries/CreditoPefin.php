<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditoPefin{
    public $cpf_procurado;
    public $data_hora;

    public $cpf;
    public $cpf_regiao;
    public $rg;
    public $data_informacao;
    public $data_nascimento;
    public $signo;
    public $sexo;
    public $nome;
    public $pai;
    public $mae;
    public $email;
    public $estado_civil;

    public $mensagens;

    public $resumo = array('cheques_sem_fundo'=>null,'pendencias'=>null);
    public $cheques_sem_fundo = array();
    public $pendencias_financeiras = array();


    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'sinpc':
                    $this->cpf_procurado = $dados->dados_consulta->dados_utilizados->cpf;
                    $this->data_hora = $dados->dados_consulta->dados_utilizados->data_hora;

                    $this->cpf = $dados->dados_cadastrais->cpf;
                    $this->cpf_regiao = $dados->dados_cadastrais->cpf_regiao;
                    $this->rg = $dados->dados_cadastrais->rg;
                    $this->data_informacao = $dados->dados_cadastrais->data_da_informacao;
                    $this->data_nascimento = $dados->dados_cadastrais->data_de_nascimento;
                    $this->signo = $dados->dados_cadastrais->signo;
                    $this->sexo = $dados->dados_cadastrais->sexo;
                    $this->nome = $dados->dados_cadastrais->nome;
                    $this->pai = $dados->dados_cadastrais->pai;
                    $this->mae = $dados->dados_cadastrais->mae;
                    $this->email = $dados->dados_cadastrais->email;
                    $this->estado_civil = $dados->dados_cadastrais->estado_civil;

                    $this->mensagens = $dados->mensagem;
                    $this->cheques_sem_fundo = $dados->cheque_sem_fundo->registro;
                    $this->pendencias_financeiras = $dados->pendencia_financeira->registro;

                    if($this->cheques_sem_fundo!=null):
                        if(count($this->cheques_sem_fundo)>0){
                            $this->resumo['cheques_sem_fundo'] = new stdClass();
                            $this->resumo['cheques_sem_fundo']->qtd = count($this->cheques_sem_fundo);
                            $this->resumo['cheques_sem_fundo']->mais_antigo = $this->cheques_sem_fundo[0]->data;
                            foreach($this->cheques_sem_fundo as $index => $cheque):
                                $this->resumo['cheques_sem_fundo']->mais_recente = $cheque->data;
                            endforeach;
                        }
                    endif;

                    if($this->serasa!=null):
                        if(count($this->serasa)>0){
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

    public function iniciar_endereco(){

    }

    public function iniciar_protestos(){

    }

    public function iniciar_cheques(){

    }
}