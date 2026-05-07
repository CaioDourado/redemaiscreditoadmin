<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once APPPATH.'config/env.php';

class Remessa_model extends CI_Model{

    private $diretorio      = '';
    private $banco          = '756';
    private $tipo           = '2'; // 1 - CPF | 2 - CNPJ
    private $cnpj           = '';
    private $agencia        = '';
    private $conta          = '';
    private $conta_dv       = '';
    private $nome_empresa   = 'REDE MAIS CREDITO';
    private $nome_banco     = 'SICOOB';

    private $carteira       = '1'; // Simples
    private $modalidade     = '01'; // Com Registro
    private $especie        = '02';
    private $especie_desc   = 'DM';

    public function __construct(){
        parent::__construct();
        $this->diretorio = FCPATH.'/remessas/';
        $this->banco = adm_env('SICOOB_BANCO_NUMERO', $this->banco);
        $this->cnpj = adm_env('SICOOB_BENEFICIARIO_CPF_CNPJ', $this->cnpj);
        $this->agencia = adm_env('SICOOB_AGENCIA', $this->agencia);
        $this->conta = adm_env('SICOOB_CONTA_REMESSA', $this->conta);
        $this->conta_dv = adm_env('SICOOB_CONTA_DV', $this->conta_dv);
        $this->nome_empresa = adm_env('SICOOB_EMPRESA_NOME', $this->nome_empresa);
        $this->nome_banco = adm_env('SICOOB_BANCO_NOME', $this->nome_banco);
    }


    public function carregar_arquivo(){
        $hoje = date('Y-m-d');
        $arquivo = FCPATH.'/remessas/remessa_seg_rastreadores_'.str_replace('-','_',$hoje).'.REM';

        if(!file_exists($arquivo)){
            $num_atual = $this->retornar_quantidade_remessas();
            $handle = fopen($arquivo,'w') or die('Não foi possível gerar a remessa: '.$arquivo);
            fclose($handle);
            $this->escrever_header($arquivo,$num_atual+1);
        }

        return $arquivo;
    }

    private function escrever_header($arquivo,$sequencia_atual){
        $banco                  = $this->banco;
        $lote                   = '0000';
        $tipo_registro          = '0';
        $CNAB                   = $this->em_branco(9);

        $empresa_tipo           = $this->tipo;
        $empresa_numero         = $this->pad_left($this->cnpj,14);
        $empresa_convenio       = $this->em_branco(20);

        $empresa_agencia_cod    = $this->pad_left($this->agencia,5);
        $empresa_agencia_dv     = ' ';
        $empresa_conta_cod      = $this->pad_left($this->conta,12);
        $empresa_conta_dv       = $this->conta_dv;
        $empresa_dv             = '0';
        $empresa_nome           = $this->pad_right($this->nome_empresa,30,' ');

        $nome_banco             = $this->pad_right($this->nome_banco,30,' ');
        $CNAB2                  = $this->em_branco(10);

        $arquivo_codigo         = '1';
        $arquivo_data_geracao   = date('dmY');
        $arquivo_hora_geracao   = date('His');
        $arquivo_sequencia      = $this->pad_left($sequencia_atual,6);
        $arquivo_layout         = '081';
        $arquivo_densidade      = '00000';

        $reserva_banco          = $this->em_branco(20);
        $reservado_empresa      = $this->em_branco(20);
        $CNAB3                  = $this->em_branco(29);

        $string_para_escrita  = $banco.$lote.$tipo_registro.$CNAB;
        $string_para_escrita .= $empresa_tipo.$empresa_numero.$empresa_convenio.$empresa_agencia_cod.$empresa_agencia_dv.$empresa_conta_cod.$empresa_conta_dv.$empresa_dv.$empresa_nome;
        $string_para_escrita .= $nome_banco.$CNAB2;
        $string_para_escrita .= $arquivo_codigo.$arquivo_data_geracao.$arquivo_hora_geracao.$arquivo_sequencia.$arquivo_layout.$arquivo_densidade;
        $string_para_escrita .= $reserva_banco.$reservado_empresa.$CNAB3;
        $string_para_escrita .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    public function ecrever_lote_de_boleto($dados_entrada){
        $dados = new stdClass();
        $dados->tipo_movimento      = '01'; // Entrada de Títulos
        $dados->id_documento        = $dados_entrada->id_boleto; // Id do boleto;escrever_trailler
        $dados->nosso_numero        = intval($dados_entrada->nosso_numero_formatado);
        $dados->data_vencimento     = date('dmY',strtotime($dados_entrada->data_vencimento));
        $dados->data_limite_pag     = date('dmY',strtotime($dados_entrada->data_vencimento.' +1 month'));
        $dados->data_emissao        = date('dmY');

        $dados->cod_juros_mora      = '2'; // 0 - Isento | 1 - Valor por Dia | 2 - Taxa Mensal
        $dados->data_juros_mora     = date('dmY',strtotime($dados_entrada->data_vencimento.'+1 day'));
        $dados->valor_juros_mora    = '250'; // Valor em % para Valor por dia ou Taxa mensal

        $dados->valor               = str_replace('.','',$dados_entrada->valor_boleto);

        $dados->multa_cod           = '2'; // 0 - Isento | 1 -Valor Fixo por Dia | 2 - Percentual Até a data informada;
        $dados->multa_data          = date('dmY',strtotime($dados_entrada->data_vencimento.'+1 day'));
        $dados->multa_valor         = '200';

        $dados->desconto1_cod       = '0'; // 0 - Não conceder desconto | 1 - Valor Fixo Até a data informada | 2 - Percentual até a data informada
        $dados->desconto1_data      = '00000000';
        $dados->desconto1_valor     = '0'; // Valor ou Percentual concedido caso pague até a data.

        $dados->desconto2_cod       = '0'; // 0 - Não conceder desconto | 1 - Valor Fixo Até a data informada | 2 - Percentual até a data informada
        $dados->desconto2_data      = '00000000';
        $dados->desconto2_valor     = '0'; // Valor ou Percentual concedido caso pague até a data.

        $dados->desconto3_cod       = '0'; // 0 - Não conceder desconto | 1 - Valor Fixo Até a data informada | 2 - Percentual até a data informada
        $dados->desconto3_data      = '00000000';
        $dados->desconto3_valor     = '0'; // Valor ou Percentual concedido caso pague até a data.

        $dados->descricao           = truncate_boleto($dados_entrada->descricao_boleto,25);
        $dados->protesto            = '1'; // 1 - Protestar dias corridos | 3 - Não Protestas | 9 - Cancelar Instrução de Protesto
        $dados->protesto_prazo      = '0'; // Prazo de inicio a partir do dia do protesto | 0 para não protestar
        $dados->cod_movimento       = '01'; // 01 - Entrada de Titulos | 02 - Solicitação de Baixa | 06 - Prorrogação de Vencimento | 09 - Protestar

        $tipo = 1;
        if(strlen($dados_entrada->cpf_cnpj)==14) $tipo = 2;

        $dados->pagador_tipo        = $tipo; // 1 - CPF | 2 - CNPJ
        $dados->pagador_cpf_cnpj    = $dados_entrada->cpf_cnpj;
        $dados->pagador_nome        = truncate_boleto($dados_entrada->nome_sacado,40);
        $dados->pagador_endereco    = truncate_boleto($dados_entrada->logradouro.' '.$dados_entrada->numero.' '.$dados_entrada->complemento,40);
        $dados->pagador_bairro      = truncate_boleto($dados_entrada->bairro,15);
        $dados->pagador_cep         = substr($dados_entrada->cep,0,5);
        $dados->pagador_sufixo_cep  = substr($dados_entrada->cep,4,3);
        $dados->pagador_cidade      = truncate_boleto($dados_entrada->cidade,15);
        $dados->pagador_uf          = $dados_entrada->uf;

        $dados->sacado_tipo         = $tipo; // 1 - CPF | 2 - CNPJ
        $dados->sacado_cpf_cnpj     = $dados_entrada->cpf_cnpj;
        $dados->sacado_nome         = truncate_boleto($dados_entrada->nome_sacado,40);

        $dados->informacao1         = '';
        $dados->informacao2         = '';
        $dados->informacao3         = '';
        $dados->informacao4         = '';
        $dados->informacao5         = '';

        $this->ecrever_lote($dados);
    }

    public function ecrever_lote($dados){
        $arquivo = $this->carregar_arquivo();
        $qtd_lotes = $this->qtd_linhas($arquivo);
        $this->escrever_lote_header($arquivo,$dados,$qtd_lotes);
        $this->escrever_lote_registro_p($arquivo,$dados,$qtd_lotes);
        $this->escrever_lote_registro_q($arquivo,$dados,$qtd_lotes);
        $this->escrever_lote_registro_r($arquivo,$dados,$qtd_lotes);
        $this->escrever_lote_registro_s($arquivo,$dados,$qtd_lotes);
        $this->escrever_lote_trailler($arquivo,$dados,$qtd_lotes);
    }

    private function escrever_lote_header($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote          = $this->pad_left($lotes,4);
        $controle_registro      = '1';

        $servico_operacao       = 'R';
        $servico_servico        = '01';
        $servico_CNAB           = $this->em_branco(2);
        $servico_layout         = '040';

        $CNAB                   = $this->em_branco(1);

        $empresa_tipo           = $this->tipo;
        $empresa_numero         = $this->pad_left($this->cnpj,15);
        $convenio               = $this->em_branco(20);

        $empresa_agencia_cod    = $this->pad_left($this->agencia,5);
        $empresa_agencia_dv     = ' ';
        $empresa_conta_cod      = $this->pad_left($this->conta,12);
        $empresa_conta_dv       = $this->conta_dv;
        $empresa_dv             = ' ';
        $empresa_nome           = $this->pad_right($this->nome_empresa,30,' ');

        $informacao1            = $this->em_branco(40);
        $informacao2            = $this->em_branco(40);

        $num_remessa_ret        = $this->pad_left($dados->id_documento,8);
        $data_gravacao          = date('dmY');
        $data_credito           = '00000000';
        $CNAB2                  = $this->em_branco(33);

        $string_para_escrita    = $controle_banco.$controle_lote.$controle_registro;
        $string_para_escrita   .= $servico_operacao.$servico_servico.$servico_CNAB.$servico_layout;
        $string_para_escrita   .= $CNAB;
        $string_para_escrita   .= $empresa_tipo.$empresa_numero.$convenio.$empresa_agencia_cod.$empresa_agencia_dv.$empresa_conta_cod.$empresa_conta_dv.$empresa_dv.$empresa_nome;
        $string_para_escrita   .= $informacao1.$informacao2;
        $string_para_escrita   .= $num_remessa_ret.$data_gravacao.$data_credito.$CNAB2;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever_lote_registro_p($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote          = $this->pad_left($lotes,4);
        $controle_registro      = '3';

        $servico_n_registro     = '00001';
        $servico_segmento       = 'P';
        $servico_CNAB           = $this->em_branco(1);
        $servico_cod_mov        = $dados->tipo_movimento;

        $cc_agencia             = $this->pad_left($this->agencia,5);
        $cc_agencia_dv          = ' ';
        $cc_conta_cod           = $this->pad_left($this->conta,12);
        $cc_conta_dv            = $this->conta_dv;
        $dv                     = ' ';

        $nosso_numero           = $this->pad_left($dados->nosso_numero,10);
        $parcela                = '01';
        $modalidade             = '01';
        $tipo_formulario        = '4'; // 1 - Auto Copiativo | 3 - Auto Envelopavel | 4 - A4 Sem Envelopamento | 6 - A4 Sem Envelopamento 3 vias
        $em_branco              = $this->em_branco(5);

        $cobranca_carteira      = '1';
        $cobranca_cadastramento = '0';
        $cobranca_documento     = ' ';
        $cobranca_emissao       = '2';
        $cobranca_distribuicao  = '2';

        $numero_documento       = $this->pad_left($dados->id_documento,15);
        $vencimento             = $dados->data_vencimento;
        $valor                  = $this->pad_left($dados->valor,15);
        $agencia_cobradora      = '00000';
        $dv                     = ' ';
        $especie_titulo         = $this->especie;
        $aceite                 = 'N';
        $data_emissao_titulo    = $dados->data_emissao;
        $juros_cod_mora         = $dados->cod_juros_mora;
        $juros_data_juros_mora  = $dados->data_juros_mora;
        $juros_mora             = $this->pad_left($dados->valor_juros_mora,15);

        $desconto1_cod          = $dados->desconto1_cod;
        $desconto1_data         = $dados->desconto1_data;
        $desconto1_valor        = $this->pad_left($dados->desconto1_valor,15);

        $valor_iof              = $this->pad_left('0',15);
        $valor_abatimento       = $this->pad_left('0',15);

        $uso_beneficiario       = $this->pad_right($dados->descricao,25,' ');
        $codigo_p_protesto      = $dados->protesto;
        $prazo_p_protesto       = '00';
        $codigo_baixa_devolucao = '0';
        $prazo_baixa_devolucao  = $this->em_branco(3);
        $codigo_moeda           = '09'; // Real
        $numero_contrato        = '0000000000';
        $CNAB                   = ' ';

        $string_para_escrita    = $controle_banco.$controle_lote.$controle_registro;
        $string_para_escrita   .= $servico_n_registro.$servico_segmento.$servico_CNAB.$servico_cod_mov;
        $string_para_escrita   .= $cc_agencia.$cc_agencia_dv.$cc_conta_cod.$cc_conta_dv.$dv;
        $string_para_escrita   .= $nosso_numero.$parcela.$modalidade.$tipo_formulario.$em_branco;
        $string_para_escrita   .= $cobranca_carteira.$cobranca_cadastramento.$cobranca_documento.$cobranca_emissao.$cobranca_distribuicao;
        $string_para_escrita   .= $numero_documento.$vencimento.$valor.$agencia_cobradora.$dv.$especie_titulo.$aceite.$data_emissao_titulo.$juros_cod_mora.$juros_data_juros_mora.$juros_mora;
        $string_para_escrita   .= $desconto1_cod.$desconto1_data.$desconto1_valor;
        $string_para_escrita   .= $valor_iof.$valor_abatimento;
        $string_para_escrita   .= $uso_beneficiario.$codigo_p_protesto.$prazo_p_protesto.$codigo_baixa_devolucao.$prazo_baixa_devolucao.$codigo_moeda.$numero_contrato.$CNAB;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever_lote_registro_q($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote_servico  = $this->pad_left($lotes,4);
        $controle_tipo_registro = '3';

        $servico_sequencia      = '00002';
        $servico_codigo         = 'Q';
        $servico_febraban       = ' ';
        $servico_cod_movimento  = $dados->cod_movimento;

        $pagador_tipo           = $dados->pagador_tipo;
        $pagador_inscricao      = $this->pad_left($dados->pagador_cpf_cnpj,15);
        $pagador_nome           = $this->pad_right($dados->pagador_nome,40,' ');
        $pagador_endereco       = $this->pad_right($dados->pagador_endereco,40,' ');
        $pagador_bairro         = $this->pad_right($dados->pagador_bairro,15,' ');
        $pagador_cep            = $this->pad_right($dados->pagador_cep,5,' ');
        $pagador_sufixo_cep     = $this->pad_right($dados->pagador_sufixo_cep,3,' ');
        $pagador_cidade         = $this->pad_right($dados->pagador_cidade,15,' ');
        $pagador_uf             = $this->pad_right($dados->pagador_uf,2,' ');

        $sacado_tipo            = $dados->sacado_tipo;
        //$sacado_inscricao       = $this->pad_left($dados->sacado_cpf_cnpj,15);
        //$sacado_nome            = $this->pad_right($dados->sacado_nome,40,' ');
		$sacado_inscricao       = $this->pad_left("",15);
		$sacado_nome            = $this->pad_right("",40,' ');

        $codigo_compensacao     = '000';
        $nn                     = $this->em_branco(20);
        $CNAB                   = $this->em_branco(8);

        $string_para_escrita    = $controle_banco.$controle_lote_servico.$controle_tipo_registro;
        $string_para_escrita   .= $servico_sequencia.$servico_codigo.$servico_febraban.$servico_cod_movimento;
        $string_para_escrita   .= $pagador_tipo.$pagador_inscricao.$pagador_nome.$pagador_endereco.$pagador_bairro.$pagador_cep.$pagador_sufixo_cep.$pagador_cidade.$pagador_uf;
        $string_para_escrita   .= $sacado_tipo.$sacado_inscricao.$sacado_nome;
        $string_para_escrita   .= $codigo_compensacao.$nn.$CNAB;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever_lote_registro_r($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote_servico  = $this->pad_left($lotes,4);
        $controle_tipo_registro = '3';

        $servico_sequencia      = '00003';
        $servico_codigo         = 'R';
        $servico_febraban       = ' ';
        $servico_cod_movimento  = $dados->cod_movimento;

        $desconto2_cod          = $dados->desconto2_cod;
        $desconto2_data         = $dados->desconto2_data;
        $desconto2_valor        = $this->pad_left($dados->desconto2_valor,15);

        $desconto3_cod          = $dados->desconto3_cod;
        $desconto3_data         = $dados->desconto3_data;
        $desconto3_valor        = $this->pad_left($dados->desconto3_valor,15);

        $multa_cod              = $dados->multa_cod;
        $multa_data             = $dados->multa_data;
        $multa_valor            = $this->pad_left($dados->multa_valor,15);

        $informacoes_pagador    = $this->em_branco(10);
        $informacao3            = $this->em_branco(40);
        $informacao4            = $this->em_branco(40);
        $CNAB                   = $this->em_branco(20);

        $data_limite_pagamento  = $dados->data_limite_pag;
        $debito_banco           = '000';
        $debito_agencia         = '00000';
        $debito_agencia_dv      = ' ';
        $debito_cc              = '000000000000';
        $debito_cc_dv           = ' ';
        $dv                     = ' ';
        $indent_emissao_aviso   = '0';
        $CNAB2                  = $this->em_branco(9);

        $string_para_escrita    = $controle_banco.$controle_lote_servico.$controle_tipo_registro;
        $string_para_escrita   .= $servico_sequencia.$servico_codigo.$servico_febraban.$servico_cod_movimento;
        $string_para_escrita   .= $desconto2_cod.$desconto2_data.$desconto2_valor;
        $string_para_escrita   .= $desconto3_cod.$desconto3_data.$desconto3_valor;
        $string_para_escrita   .= $multa_cod.$multa_data.$multa_valor;
        $string_para_escrita   .= $informacoes_pagador.$informacao3.$informacao4.$CNAB;
        $string_para_escrita   .= $data_limite_pagamento.$debito_banco.$debito_agencia.$debito_agencia_dv.$debito_cc.$debito_cc_dv.$dv.$indent_emissao_aviso.$CNAB2;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever_lote_registro_s($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote_servico  = $this->pad_left($lotes,4);
        $controle_tipo_registro = '3';

        $servico_sequencia      = '00004';
        $servico_codigo         = 'S';
        $servico_febraban       = ' ';
        $servico_cod_movimento  = $dados->cod_movimento;

        $tipo_de_impressao      = '3';
        $informacao5            = $this->pad_right($dados->informacao1,40,' ');
        $informacao6            = $this->pad_right($dados->informacao2,40,' ');
        $informacao7            = $this->pad_right($dados->informacao3,40,' ');
        $informacao8            = $this->pad_right($dados->informacao4,40,' ');
        $informacao9            = $this->pad_right($dados->informacao5,40,' ');
        $CNAB                   = $this->em_branco(22);

        $string_para_escrita    = $controle_banco.$controle_lote_servico.$controle_tipo_registro;
        $string_para_escrita   .= $servico_sequencia.$servico_codigo.$servico_febraban.$servico_cod_movimento;
        $string_para_escrita   .= $tipo_de_impressao.$informacao5.$informacao6.$informacao7.$informacao8.$informacao9.$CNAB;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever_lote_trailler($arquivo,$dados,$auto_incremento_remessa){
        $controle_banco         = $this->banco;
        $lotes                  = intval(($auto_incremento_remessa-1)/6)+1;
        $controle_lote_servico  = $this->pad_left($lotes,4);
        $controle_tipo_registro = '5';

        $CNAB                   = $this->em_branco(9);
        $qtd_registros          = $this->pad_left($this->qtd_linhas($arquivo),6);

        $total_cobranca_s_qtd   = $this->pad_left('1',6);
        $total_cobranca_s_valor = $this->pad_left($dados->valor,17);

        $total_cobranca_v_qtd   = $this->pad_left('0',6);
        $total_cobranca_v_valor = $this->pad_left('0',17);

        $total_cobranca_c_qtd   = $this->pad_left('0',6);
        $total_cobranca_c_valor = $this->pad_left('0',17);

        $total_cobranca_d_qtd   = $this->pad_left('0',6);
        $total_cobranca_d_valor = $this->pad_left('0',17);

        $n_aviso                = $this->em_branco(8);
        $CNAB2                  = $this->em_branco(117);

        $string_para_escrita    = $controle_banco.$controle_lote_servico.$controle_tipo_registro;
        $string_para_escrita   .= $CNAB.$qtd_registros;
        $string_para_escrita   .= $total_cobranca_s_qtd.$total_cobranca_s_valor;
        $string_para_escrita   .= $total_cobranca_v_qtd.$total_cobranca_v_valor;
        $string_para_escrita   .= $total_cobranca_c_qtd.$total_cobranca_c_valor;
        $string_para_escrita   .= $total_cobranca_d_qtd.$total_cobranca_d_valor;
        $string_para_escrita   .= $n_aviso.$CNAB2;
        $string_para_escrita   .= "\n";

        $this->escrever($arquivo,$string_para_escrita);
    }

    public function escrever_trailler(){
        $arquivo = $this->carregar_arquivo();

        $controle_banco         = $this->banco;
        $controle_lote          = '9999';
        $controle_registro      = '9';

        $CNAB                   = $this->em_branco(9);
        $qtd_lotes              = $this->pad_left(intval(intval($this->qtd_linhas($arquivo)-1)/5),6);
        $qtd_registros          = $this->pad_left($this->qtd_linhas($arquivo),6);
        $qtd_contas             = '000000';

        $CNAB2                  = $this->em_branco(205);

        $string_para_escrita    = $controle_banco.$controle_lote.$controle_registro;
        $string_para_escrita   .= $CNAB.$qtd_lotes.$qtd_registros.$qtd_contas.$CNAB2;

        $this->escrever($arquivo,$string_para_escrita);
    }

    private function escrever($arquivo,$escrita){
        /*
        $handle = fopen($arquivo,'w') or die('Não foi possível escrever no arquivo: '.$arquivo);
        fwrite($handle,$escrita);
        fclose($handle);
        */
        file_put_contents($arquivo,$escrita,FILE_APPEND | LOCK_EX);
    }

    private function em_branco($qtd=null){
        if($qtd!=null){
            $retorno = '';
            for($i=0;$i<$qtd;$i++){
                $retorno .= ' ';
            }
            return $retorno;
        }else{
            return null;
        }

    }

    private function pad_left($string,$qtd=0,$comp='0'){
        $retorno = $string;
        $lenght_string = strlen($retorno);
        for($i=$lenght_string;$i<$qtd;$i++){
            $retorno = $comp.$retorno;
        }
        return $retorno;
    }

    private function pad_right($string,$qtd=0,$comp='0'){
        $retorno = $string;
        $lenght_string = strlen($retorno);
        for($i=$lenght_string;$i<$qtd;$i++){
            $retorno = $retorno.$comp;
        }
        return $retorno;
    }

    private function retornar_quantidade_remessas(){
        return count(glob($this->diretorio."*.REM"));
    }

    private function qtd_linhas($arquivo){
        $retorno = 0;
        $handle = fopen($arquivo, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            $retorno++;
        }
        return $retorno-1;
    }
}
