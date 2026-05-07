<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

class Boleto_model extends ModelAuth {
    protected $tabela = 'boleto';
    protected $campo_id = 'id_boleto';
    protected $campo_principal = 'descricao_boleto';

	public function by_data($data=null){
		if($data!=null):
			$sql = 'SELECT * FROM boleto WHERE criado_em BETWEEN "'.$data.' 00:00:00" AND "'.$data.' 23:59:59" ORDER BY criado_em ASC';
			return $this->db->query($sql);
		else:
			return null;
		endif;
	}

    public function retornar_de_mes($ano,$mes){
        if($mes<10) $mes = '0'.$mes;
        $inicio = $ano.'-'.$mes.'-01';
        $fim = date($ano.'-'.$mes.'-31');

        $sql  = 'SELECT tbmain.*, UPPER(tb2.nome_ou_fantasia) AS nome FROM boleto AS tbmain ';
        $sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
        $sql .= 'WHERE data_vencimento BETWEEN "'.$inicio.'" AND "'.$fim.'" ';
        return $this->db->query($sql);
    }

    public function retornar_pagos($inicio=null,$fim=null){
        $sql = 'SELECT * FROM boleto WHERE data_pagamento BETWEEN "'.$inicio.'" AND "'.$fim.'" AND codigo_retorno = "06" ORDER BY data_pagamento DESC ';
        return $this->db->query($sql);
    }

    public function retornar_por_mes(){
        $sql  = 'SELECT  ';
        $sql .= 'YEAR(data_vencimento) AS ano, ';
        $sql .= 'MONTH(data_vencimento) AS mes, ';
        $sql .= 'SUM(CASE WHEN (pago = 1) THEN 1 ELSE 0 END) AS pagos, ';
        $sql .= 'SUM(CASE WHEN (cancelado = 1 AND pago=0) THEN 1 ELSE 0 END) AS cancelados, ';
        $sql .= 'SUM(CASE WHEN (data_vencimento <= NOW() AND pago=0) THEN 1 ELSE 0 END) AS vencidos, ';
        $sql .= 'SUM(CASE WHEN (data_vencimento > NOW() AND pago=0) THEN 1 ELSE 0 END) AS a_pagar, ';
        $sql .= 'COUNT(*) AS quantidade_total, ';
        $sql .= 'SUM(CASE WHEN (pago = 1) THEN valor_liquido ELSE 0 END) AS valor_pago, ';
        $sql .= 'SUM(valor_boleto) AS valor_total ';
        $sql .= 'FROM boleto ';
        $sql .= 'GROUP BY YEAR(data_vencimento),MONTH(data_vencimento) ';
        $sql .= 'ORDER BY ano DESC, mes DESC ';
        return $this->db->query($sql);
    }

    public function retornar_ultimo_boleto(){
        $this->db->limit(1);
        $this->db->order_by($this->campo_id,'DESC');
        return $this->db->get($this->tabela);
    }

    public function retornar_ultimos($qtd=10){
		$sql  = 'SELECT tbmain.*, tb2.email FROM boleto AS tbmain ';
		$sql .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
		$sql .= 'ORDER BY id_boleto DESC limit '.$qtd;
		return $this->db->query($sql);
        //$this->db->order_by('id_boleto','DESC');
        //$this->db->limit($qtd);
        //return $this->db->get('boleto');
    }

    public function criar($id_cliente=null,$valor=null,$vencimento=null,$outros=null,$id_conta=1){
        if($id_cliente==null||$valor==null||$vencimento==null) return false;

        $cliente = $this->retornar_cliente($id_cliente)->row();
        $conta = $this->retornar_conta($id_conta)->row();
    
        $dados = array();
        $dados['id_cliente_fk'] =               $id_cliente;
        if($cliente->razao_social==null)
            $dados['nome_sacado'] =                 $this->formatar($cliente->nome_ou_fantasia);
        else
            $dados['nome_sacado'] =                 $this->formatar($cliente->razao_social);
        $dados['cpf_cnpj'] =                    $this->formatar($cliente->cpf_cnpj);

        $dados['logradouro'] =                  $this->formatar($cliente->logradouro);
        $dados['numero'] =                      $this->formatar($cliente->numero);
        $dados['complemento'] =                 $this->formatar($cliente->complemento);
        $dados['bairro'] =                      $this->formatar($cliente->bairro);
        $dados['cidade'] =                      $this->formatar($cliente->cidade);
        $dados['uf'] =                          $this->formatar($cliente->uf);
        $dados['cep'] =                         $this->formatar($cliente->cep);

        $novo_nosso_numero =                    $this->get_ultimo_nosso_numero()+1;
        $id_boleto_atual =                      $this->get_ultimo_id_boleto()+1;

        $dados['nosso_numero'] =                $this->formatar($novo_nosso_numero);
        $dados['seu_numero'] =                  $this->formatar($id_boleto_atual);
        $dados['codigo_sacado'] =               $this->formatar($cliente->id_cliente);
        $dados['carteira'] =                    $this->formatar($conta->carteira);
        $dados['codigo_moeda'] =                $this->formatar($conta->especie_moeda);
        $dados['especie'] =                     $this->formatar($conta->especie);
        $dados['aceite'] =                      $this->formatar($conta->aceite);
        $dados['codigo_de_barras'] =            '';
        $dados['digito_verificador'] =          '';


        $dados['hash'] =                        md5($id_boleto_atual);

            if(isset($outros['descricao_boleto'])) $dados['descricao_boleto'] =    $this->formatar($outros['descricao_boleto']);
            else $dados['descricao_boleto'] =    $this->formatar('Boleto de número '.$id_boleto_atual.' ddo cliente '.$cliente->id_pessoa);
            if(isset($outros['descricao_boleto2'])) $dados['descricao_boleto2'] =    $this->formatar($outros['descricao_boleto2']);
            if(isset($outros['descricao_boleto3'])) $dados['descricao_boleto3'] =    $this->formatar($outros['descricao_boleto3']);
            if(isset($outros['descricao_boleto4'])) $dados['descricao_boleto4'] =    $this->formatar($outros['descricao_boleto4']);

            if(isset($outros['observacao']))        $dados['observacao'] =    $this->formatar($outros['observacao']);

        $dados['cancelado'] =                   0;
        $dados['baixado'] =                     0;
        $dados['id_conta_banco'] =              $id_conta;
            if(isset($outros['nota_fiscal']))       $dados['nota_fiscal'] = 1;
            if(isset($outros['correio']))           $dados['correio'] = 1;

        $dados['valor_boleto'] =                $valor;
        $dados['valor_desconto'] =              0;
        $dados['valor_multa'] =                 0;
        $dados['valor_juros'] =                 0;
        $dados['valor_abatimento'] =            0;
            if(isset($outros['valor_desconto'])) $dados['valor_desconto'] = $outros['valor_desconto'];
            if(isset($outros['valor_multa'])) $dados['valor_multa'] = $outros['valor_multa'];
            if(isset($outros['valor_juros'])) $dados['valor_juros'] = $outros['valor_juros'];
            if(isset($outros['valor_abatimento'])) $dados['valor_abatimento'] = $outros['valor_abatimento'];

        $dados['pago'] =                        0;
        $dados['codigo_retorno'] =              '';
        $dados['numero_remessa'] =              '';

        $dados['data_vencimento'] =             $vencimento;
        $dados['data_pagamento'] =              '';
        $dados['criado_em'] =                   date('Y-m-d');

        $dados_complementares = $this->criar_boleto_complementos($dados,$conta);

        $dados['nosso_numero_formatado'] = $dados_complementares['nosso_numero_formatado'];
        $dados['digito_verificador'] = $dados_complementares['digito_verificador'];
        $dados['codigo_de_barras'] = $dados_complementares["codigo_de_barras"];
        $dados['linha_digitavel'] = $dados_complementares["linha_digitavel"];

        $this->db->insert('boleto',$dados);
        if ($this->db->affected_rows() > 0) {
            $ultimo_boleto = $this->retornar_ultimo_boleto()->row();
            $this->load->model('remessa_model','remessa');
            $this->remessa->ecrever_lote_de_boleto($ultimo_boleto);
            return true;
        } else {
            return false;
        }
    }

    public function criar_via_fatura($id_fatura){

    }

    public function gerar_remessa(){

    }

    public function baixar_remessa(){

    }

    public function ler_retorno(){

    }

    public function retornar($id_boleto=null){
		$this->db->where(array('id_boleto'=>$id_boleto));
		return $this->db->get('boleto');
    }

    public function retornar_nn($nosso_numero=null){

    }

    public function retornar_hash($hash=null){
        $this->db->where(array('hash'=>$hash));
        $this->db->limit(1);
        return $this->db->get('boleto');
    }

    public function retorna_conta($id_conta=null){
        $this->db->where(array('id_conta'=>$id_conta));
        $this->db->limit(1);
        return $this->db->get('conta');
    }

    public function retornar_cliente($id_cliente){
        $this->db->where(array('id_cliente'=>$id_cliente));
        $this->db->limit(1);
        return $this->db->get('cliente');
    }

    public function retornar_conta($id_conta){
        $this->db->where(array('id_conta'=>$id_conta));
        $this->db->limit(1);
        return $this->db->get('conta');
    }

    public function get_ultimo_boleto(){
        $this->db->order_by('id_boleto','DESC');
        $this->db->limit(1);
        $id = $this->db->get('boleto');
        if($id!=null){
            return $id;
        }else{
            return 0;
        }
    }

    private function get_ultimo_nosso_numero($banco=1){
        $this->db->select_max('nosso_numero');
        $this->db->where('id_conta_banco',$banco);
        $id = $this->db->get('boleto')->row()->nosso_numero;
        if($id!=null){
            return $id;
        }else{
            return 0;
        }
    }

    private function get_ultimo_id_boleto(){
        $this->db->select_max('id_boleto');
        return $this->db->get('boleto')->row()->id_boleto;
    }

    private function criar_boleto_complementos($dados=NULL,$conta_banco=NULL){
        if($dados!=NULL&&is_array($dados)&&$conta_banco!=NULL){
            $dados_retorno = array();
            $codigobanco = $conta_banco->banco_numero;
            $codigo_banco_com_dv = $this->geraCodigoBanco($codigobanco);
            $nummoeda = $conta_banco->especie_moeda;
            $fator_vencimento = $this->fator_vencimento($dados["data_vencimento"]);
            $valor = $this->formata_numero(limpar_string($dados["valor_boleto"]*100),10,0,"valor");
            $agencia = $this->formata_numero($conta_banco->agencia,4,0);
            $conta = $this->formata_numero($conta_banco->conta,8,0);
            $carteira = '1';
            $livre_zeros            ='000000';      // ZEROS
            $modalidadecobranca     = '01';         // COBRANÇA REGISTRADA
            $numeroparcela          = '001';        //
            $convenio = $this->formata_numero(only_numbers($conta_banco->codigo_cedente),7,0);
            $agencia_codigo = $agencia ." / ". $convenio;
            $dv_nosso_numero = $this->digito_verificador($dados['nosso_numero']);
            $nossonumero = $this->formata_numero($dados["nosso_numero"].$dv_nosso_numero,8,0);
            $campolivre  = "$modalidadecobranca$convenio$nossonumero$numeroparcela";

            $dv=$this->modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$carteira$agencia$campolivre");
            $linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$carteira$agencia$campolivre";

            $dados_retorno['nosso_numero_formatado'] = $nossonumero;
            $dados_retorno['digito_verificador'] = $dv_nosso_numero;
            $dados_retorno["codigo_de_barras"] = $linha;
            $dados_retorno["linha_digitavel"] = $this->monta_linha_digitavel($linha);

            return $dados_retorno;
        }else{
            return false;
        }
    }
    function geraCodigoBanco($numero) {
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->modulo_11($parte1);
        return $parte1 . "-" . $parte2;
    }
    function _dateToDays($year,$month,$day) {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
            $day +  1721119);
    }
    function fator_vencimento($data) {
        $data = data_pt($data);
        $data = explode("/",$data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
    }
    function formata_numero($numero,$loop,$insert,$tipo = "geral") {
        if ($tipo == "geral") {
            $numero = str_replace(",","",$numero);
            while(strlen($numero)<$loop){ $numero = $insert . $numero; }
        }
        if ($tipo == "valor") {
            $numero = str_replace(",","",$numero);
            while(strlen($numero)<$loop){ $numero = $insert . $numero; }
        }
        if ($tipo == "convenio") {
            while(strlen($numero)<$loop){ $numero = $numero . $insert; }
        }
        return $numero;
    }
    function formata_numdoc($num,$tamanho)
    {
        while(strlen($num)<$tamanho)
        {
            $num="0".$num;
        }
        return $num;
    }
    function digito_verificador($nosso_numero=NULL,$agencia=null,$convenio=null){
        if($agencia===null) $agencia = adm_env('SICOOB_AGENCIA', '');
        if($convenio===null) $convenio = adm_env('SICOOB_NUMERO_CLIENTE', '');
        $sequencia = $this->formata_numdoc($agencia,4).$this->formata_numdoc(str_replace("-","",$convenio),10).$this->formata_numdoc($nosso_numero,7);
        $cont=0;
        $calculoDv = 0;
        for($num=0;$num<=strlen($sequencia);$num++)
        {
            $cont++;
            if($cont == 1)
            {
                // constante fixa Sicoob » 3197
                $constante = 3;
            }
            if($cont == 2)
            {
                $constante = 1;
            }
            if($cont == 3)
            {
                $constante = 9;
            }
            if($cont == 4)
            {
                $constante = 7;
                $cont = 0;
            }
            $sub_calculodv = substr($sequencia,$num,1);
            $calculoDv = $calculoDv + (intval($sub_calculodv) * $constante);
        }
        $Resto = $calculoDv % 11;
        $Dv = 11 - $Resto;
        if ($Dv == 0) $Dv = 0;
        if($Dv>9) $Dv = 0;
        return $Dv;
    }
    function modulo_10($num) {
        $numtotal10 = 0;
        $fator = 2;

        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial10[$i] = $numeros[$i] * $fator;
            $numtotal10 .= $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            }
            else {
                $fator = 2;
            }
        }
        $soma = 0;
        for ($i = strlen($numtotal10); $i > 0; $i--) {
            $numeros[$i] = substr($numtotal10,$i-1,1);
            $soma += $numeros[$i];
        }
        $resto = $soma % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;
    }
    function modulo_11($num, $base=9, $r=0) {
        $soma = 0;
        $fator = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = "X";
            }
            if (strlen($num) == "43") {
                //então estamos checando a linha digitável
                if ($digito == "0" or $digito == "X" or $digito > 9) {
                    $digito = 1;
                }
            }
            return $digito;
        }
        elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
    function monta_linha_digitavel($linha) {
        $p1 = substr($linha, 0, 4);
        $p2 = substr($linha, 19, 5);
        $p3 = $this->modulo_10("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        $p1 = substr($linha, 24, 10);
        $p2 = $this->modulo_10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        $p1 = substr($linha, 34, 10);
        $p2 = $this->modulo_10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);

        $campo3 = "$p4.$p5";
        $campo4 = substr($linha, 4, 1);
        $campo5 = substr($linha, 5, 14);

        return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }

    private function formatar($entrada){
        return $this->replaceSpecialCarac(strtolower($entrada));
    }

    private function replaceSpecialCarac($str) {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
        //$str = preg_replace('/[^a-z0-9]/i', '_', $str);
        //$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
        return $str;
    }
}
