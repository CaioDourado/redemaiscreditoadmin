<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CadastroEndereco{
    public $cpf_procurado;
    public $data_hora;

    public $nome;
    public $cpf;
    public $data_nascimento;
    public $nome_mae;
    public $telefones = array();
    public $emails = array();

    public $enderecos = array();
    public $parentes = array();
    public $vizinhos = array();
    public $trabalhos = array();

    public function iniciar($fornecedor,$dados){
        switch ($fornecedor):
            case 'sinpc':

                break;
            case 'redesia':
                    $this->cpf_procurado = $dados->HEADER->PARAMETROS->CPFCNPJ;
                    $this->data_hora = $dados->HEADER->DT_HORA_CONSULTA;

                    $dados_informacao_pessoa = $dados->DADOS_CADASTRAIS_COMPLETOS->INFO_PESSOA;

                    $this->nome = $dados_informacao_pessoa->PESSOA_FISICA_INFO[0]->NOME;
                    $this->cpf = $dados_informacao_pessoa->PESSOA_FISICA_INFO[0]->DOCUMENTO;
                    $this->data_nascimento = $dados_informacao_pessoa->PESSOA_FISICA_INFO[0]->NASCIMENTO;
                    $this->nome_mae = $dados_informacao_pessoa->PESSOA_FISICA_INFO[0]->NOME_MAE;

                    foreach($dados_informacao_pessoa->PESSOA_FISICA_INFO as $index => $informacao):
                        if(!in_array($informacao->TELEFONE,$this->telefones))
                            array_push($this->telefones,$informacao->TELEFONE);
                        if(!in_array($informacao->EMAIL,$this->emails)&&$informacao->EMAIL!="")
                            array_push($this->emails,$informacao->EMAIL);

                        $endereco_atual = new stdClass();
                        $endereco_atual->endereco = $informacao->ENDERECO;
                        $endereco_atual->bairro = $informacao->BAIRRO;
                        $endereco_atual->cidade = $informacao->CIDADE;
                        $endereco_atual->cep = $informacao->CEP;
                        $endereco_atual->uf = $informacao->UF;
                        array_push($this->enderecos,$endereco_atual);
                    endforeach;

                    foreach($dados->DADOS_CADASTRAIS_COMPLETOS->INFO_PARENTE->PESSOA_FISICA_INFO as $index => $parente):
                        $parente_atual = new stdClass();
                        $parente_atual->nome = $parente->NOME;
                        $parente_atual->cpf = $parente->DOCUMENTO;
                        $parente_atual->data_nascimento = $parente->NASCIMENTO;
                        $parente_atual->telefone = $parente->TELEFONE;
                        $parente_atual->endereco = $parente->ENDERECO;
                        $parente_atual->bairro = $parente->BAIRRO;
                        $parente_atual->cidade = $parente->CIDADE;
                        $parente_atual->cep = $parente->CEP;
                        $parente_atual->uf = $parente->UF;
                        $parente_atual->email = $parente->EMAIL;
                        array_push($this->parentes,$parente_atual);
                    endforeach;

                    foreach($dados->DADOS_CADASTRAIS_COMPLETOS->INFO_VIZINHO->PESSOA_FISICA_INFO as $index => $vizinho):
                        $vizinho_atual = new stdClass();
                        $vizinho_atual->nome = $vizinho->NOME;
                        $vizinho_atual->cpf = $vizinho->DOCUMENTO;
                        $vizinho_atual->data_nascimento = $vizinho->NASCIMENTO;
                        $vizinho_atual->telefone = $vizinho->TELEFONE;
                        $vizinho_atual->endereco = $vizinho->ENDERECO;
                        $vizinho_atual->bairro = $vizinho->BAIRRO;
                        $vizinho_atual->cidade = $vizinho->CIDADE;
                        $vizinho_atual->cep = $vizinho->CEP;
                        $vizinho_atual->uf = $vizinho->UF;
                        $vizinho_atual->email = $vizinho->EMAIL;
                        array_push($this->vizinhos,$vizinho_atual);
                    endforeach;

                    foreach($dados->DADOS_CADASTRAIS_COMPLETOS->INFO_COMERCIAL->PESSOA_JURIDICA_INFO as $index => $trabalho):
                        $trabalho_atual = new stdClass();
                        $trabalho_atual->nome = $trabalho->NOME;
                        $trabalho_atual->cnpj = $trabalho->CNPJ;
                        $trabalho_atual->telefone = $trabalho->TELEFONE;
                        $trabalho_atual->endereco = $trabalho->ENDERECO;
                        $trabalho_atual->bairro = $trabalho->BAIRRO;
                        $trabalho_atual->cidade = $trabalho->CIDADE;
                        $trabalho_atual->cep = $trabalho->CEP;
                        $trabalho_atual->uf = $trabalho->UF;
                        array_push($this->trabalhos,$trabalho_atual);
                    endforeach;
                break;
        endswitch;
        return $this;
    }
}