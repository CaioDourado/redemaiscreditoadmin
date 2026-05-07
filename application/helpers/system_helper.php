<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

function form_validation(){
    if(validation_errors()!=NULL){
        $ci =& get_instance();
        echo $ci->load->view('components/alert',array('type'=>'danger','title'=>'Erros de Validação','content'=>validation_errors()),true);
    }
}

function get_msgs(){
    $ci =& get_instance();
    if($ci->session->flashdata('erro')!=null)
        echo $ci->load->view('components/alert',array('type'=>'danger','title'=>'Erro','content'=>$ci->session->flashdata('erro')),true);
    if($ci->session->flashdata('sucesso')!=null)
        echo $ci->load->view('components/alert',array('type'=>'success','title'=>'Sucesso','content'=>$ci->session->flashdata('sucesso')),true);
    if($ci->session->flashdata('cuidado')!=null)
        echo $ci->load->view('components/alert',array('type'=>'warning','title'=>'Oops','content'=>$ci->session->flashdata('cuidado')),true);
    if($ci->session->flashdata('normal')!=null)
        echo $ci->load->view('components/alert',array('type'=>'info','title'=>'Mensagem do Sistema','content'=>$ci->session->flashdata('normal')),true);
}

function set_msg($mensagem=NULL,$tipo='erro'){
    $ci =& get_instance();
    $ci->session->set_flashdata($tipo,$mensagem);
}

function get_token_content($token=null){
    include_once APPPATH . 'third_party/Token.php';
    $ci =& get_instance();
    if($token==null) $token = $ci->session->userdata('token');

    if($token!=null){
        try{
            return Token::decode($token,CHAVE_GERAL,true);
        }catch(Exception $e){
            return null;
        }
    }else{
        return null;
    }
}

function sim_nao($entrada){
    if($entrada==1) return 'Sim';
    return 'Não';
}

function ativo_inativo($entrada){
    if($entrada==1) return 'Ativo';
    return 'Inativo';
}

function data_pt($hora,$comhora=true,$comsegundos=true){
    if($hora!=null){
        if($comhora){
            if($comsegundos) return date('d/m/Y H:i:s',strtotime($hora));
            return date('d/m/Y H:i',strtotime($hora));
        }return date('d/m/Y',strtotime($hora));
    }else{
        return false;
    }
}

function data_db($entrada,$comhora=true){
    if($entrada!=null){
        $array_entrada = explode(" ",$entrada);
        $array_data = explode("/",$array_entrada[0]);
        if($comhora){
            return $array_data[2].'-'.$array_data[1].'-'.$array_data[0].' '.$array_entrada[1];
        }else{
            return $array_data[2].'-'.$array_data[1].'-'.$array_data[0];
        }
    }else{
        return null;
    }
}

function data_break($input){
	$arr = explode("T",$input);
	if(count($arr)>1){
		return $arr[0];
	}else{
		return $input;
	}
}

function data_8digitos($entrada){
    $dia = substr($entrada,0,2);
    $mes = substr($entrada,2,2);
    $ano = substr($entrada,4,4);

    return $ano.'-'.$mes.'-'.$dia;
}

function dinheiro($valor=NULL){
    if($valor==null) return '0,00';
    else return number_format($valor,2,',',' ');
}

function money($val){
    if($val==null) return '0.00';
    else return number_format($val,2,'.','');
}

function tooltip($msg=null,$position='top'){
    return 'data-toggle="tooltip" data-placement="'.$position.'" title="'.$msg.'"';
}

function tooltip_a($msg=null,$position='top'){
    return array('data-toggle'=>'tooltip','data-placement'=>$position,'title'=>$msg);
}

function only_numbers($input){
    return preg_replace( '/[^0-9]/', '', $input );
}

function form_input($name,$label,$value='',$class='',$icon=NULL){
    $retorno  = '';
    $retorno .= '<div class="form-group">';
    $retorno .= '<label for="'.$name.'">'.$label.'</label>';
    $input = '<input type="text" class="form-control '.$class.'" name="'.$name.'" value="'.$value.'">';
    if(isset($icon)){
        $retorno .= '<div class="input-group">';
        $retorno .= '<span class="input-group-addon">'.$icon.'</span>';
            $retorno .= $input;
        $retorno .= '</div>';
    }else{
        $retorno .= $input;
    }
    $retorno .= '</div>';
    return $retorno;
}

function form_select($name,$label,$options=null,$class='',$select=null,$icon=null){
    $retorno  = '';
    $retorno .= '<div class="form-group">';
        $retorno .= '<label for="'.$name.'">'.$label.'</label>';
        if(isset($icon)){
            $retorno .= '<div class="input-group">';
            $retorno .= '<span class="input-group-addon">'.$icon.'</span>';
        }
            $retorno .= '<select name="'.$name.'" id="" class="form-control '.$class.'">';
                if(is_array($options)){
                    if(count($options)>0){
                        foreach($options as $index => $option):
                            if($select!=null){
                                if($select==$index) $retorno .= '<option value="'.$index.'" selected>'.$option.'</option>';
                                else $retorno .= '<option value="'.$index.'">'.$option.'</option>';
                            }else{
                                $retorno .= '<option value="'.$index.'">'.$option.'</option>';
                            }
                        endforeach;
                    }
                }
            $retorno .= '</select>';
        if(isset($icon)) $retorno .= '</div>';
    $retorno .= '</div>';
    return $retorno;
}

function meses_array($mes=null){
    $retorno = array();

    $retorno['01'] = 'Janeiro';
    $retorno['02'] = 'Fevereiro';
    $retorno['03'] = 'Março';
    $retorno['04'] = 'Abril';
    $retorno['05'] = 'Maio';
    $retorno['06'] = 'Junho';
    $retorno['07'] = 'Julho';
    $retorno['08'] = 'Agosto';
    $retorno['09'] = 'Setembro';
    $retorno['10'] = 'Outubro';
    $retorno['11'] = 'Novembro';
    $retorno['12'] = 'Dezembro';

    if($mes!=null){
        if($mes<10) $mes = '0'.$mes;
        return $retorno[$mes];
    }
    return $retorno;
}

function get_mes($entrada){
    switch($entrada):
        case '01': return 'janeiro'; break;
        case '02': return 'fevereiro'; break;
        case '03': return 'março'; break;
        case '04': return 'abril'; break;
        case '05': return 'maio'; break;
        case '06': return 'junho'; break;
        case '07': return 'julho'; break;
        case '08': return 'agosto'; break;
        case '09': return 'setembro'; break;
        case '10': return 'outubro'; break;
        case '11': return 'novembro'; break;
        case '12': return 'dezembro'; break;
    endswitch;
}

function limpar_string($string=NULL){
    if($string!=NULL):
        $retorno = $string;
        $retorno = str_replace('.','',$retorno);
        $retorno = str_replace('/','',$retorno);
        $retorno = str_replace('-','',$retorno);
        $retorno = str_replace(' ','',$retorno);
        $retorno = str_replace('_','',$retorno);
        $retorno = str_replace('(','',$retorno);
        $retorno = str_replace(')','',$retorno);
        return $retorno;
    else:
        return false;
    endif;
}

function truncate_boleto($entrada,$tam=10){
    if(strlen($entrada)>$tam){
        return substr($entrada,0,$tam);
    }else{
        return $entrada;
    }
}

function truncate($entrada,$tam=10){
    if(strlen($entrada)>$tam){
        return substr($entrada,0,$tam);
    }else{
        return $entrada;
    }
}

function retornar_status($status=null){
    $array_status = array('1'=>'Ativo','0'=>'Inativo','2'=>'Inadimplente');
    if($status!=null) return $array_status[$status];
    return $array_status;
}

function class_status($status){
    $array_status = array('1'=>'','0'=>'danger','2'=>'warning');
    if($status!=null) return $array_status[$status];
    return $array_status;
}

function retornar_tipo($entrada){
    switch($entrada):
        case 'Consulta': return ''; break;
        case 'Negativação': return '<i class="fa fa-minus-circle"></i>'; break;
        case 'Baixa': return '<i class="fa fa-arrow-circle-down"></i>'; break;
        case 'Carta': return '<i class="fa fa-envelope"></i>'; break;
    endswitch;
}

function cod_natureza($entrada=null){
    $array =  array(
        1 => "ADIANTAMENTO DE CONTA",
        3  => "ALUGUEL",
        42 => "ARRECADADOR",
        6  => "CDC BENS",
        10 => "CDC MOTOS",
        9  => "CDC VEÍCULOS LEVES",
        15 => "CDC VEÍCULOS PESADOS",
        23 => "CHEQUE ELETRÔNICO",
        7  => "CONDOMÍNIO",
        49 => "CONFISS DIV",
        21 => "CONSÓRCIO AÉREO",
        20 => "CONSÓRCIO BENS",
        11 => "CONSÓRCIO CONTEMPLADO",
        16 => "CONSÒRCIO IMÓVEIS",
        18 => "CONSÒRCIO VEÍCULO",
        17 => "CONSÓRCIO VEÍCULOS PESADO",
        8  => "CREDIÁRIO",
        14 => "CRÉDITO CARTÃO",
        12 => "CRÉDITO PESSOAL",
        22 => "DÍVIDAS CHEQUES",
        24 => "DUPLICATA",
        2  => "EMPRÉSTIMO",
        27 => "EMPRÉSTIMO CONSIGNADO",
        25 => "EMPRÉSTIMO CONTA",
        26 => "ENERGIA ELÉRICA",
        43 => "FATURA DE ÁGUA",
        28 => "FATURA GÁS",
        29 => "FINANCIAMENTO",
        30 => "HOSPITAIS",
        13 => "IMPEDIDO BC",
        31 => "INSTITUTO DE ENSINO",
        4  => "LEASING",
        34 => "LEASING MOTOS",
        33 => "LEASING VEÍCULO",
        35 => "LEASING VEÍCULOS PESADOS",
        36 => "MENSALIDADE ESCOLAR",
        37 => "NOTA FISCAL",
        39 => "OPERAÇÕES AJUIZADAS",
        38 => "OPERAÇÕES AGRÍCOLAS",
        5  => "OPERAÇÕES DE CÂMBIO",
        32 => "OPERAÇÕES IMOBILIÁRIAS",
        40 => "OUTRAS OPERAÇÕES",
        56 => "RENEGOCIAÇÃO DE DÍVIDA",
        41 => "REPASSES",
        44 => "SEGURO FIANÇA LOCATÍCIA",
        45 => "SEGURO GARANTIA",
        46 => "SEGURO QUEBRA",
        47 => "SEGURO RISCO DECORRIDO",
        48 => "SEGURO SAÚDE",
        53 => "SERVIÇO DE DADOS",
        55 => "SERVIÇOS DE TELEFONIA",
        54 => "TELEFONE MÓVEL",
        51 => "TELEFONIA MÓVEL",
        57 => "TELEFONIA FIXA",
        52 => "TELEFONE FIXO",
        50 => "TÍTULO DESCONTA",
        58 => "VENDA DE MERCADORIA",
    );

    if($entrada!=null) return $array[$entrada];
    return $array;
}

function cod_natureza_scpc($entrada=null){
    $array =  array(
        "01" => "CRÉDITO DIRETO AO CONSUMIDOR",
        "02" => "CHEQUE COBRANÇA DEVOLVIDO",
        "03" => "LOCADORA",
        "04" => "CONSÓRCIO",
        "05" => "IMOBILIÁRIA ADMINISTRAÇÃO DE BENS",
        "06" => "CRÉDITOS IMOBILIARIOS",
        "07" => "OUTRAS ATVIDADES ECONÔMICAS",
        "08" => "NÃO GRAVA CONSULTA",
        "09" => "CRÉDITO DE VEÍCULO",
        "10" => "CRÉDITO PESSOAL",
        "11" => "TÍTULO PROTESTADO",
        "13" => "OUTROS",
        "14" => "CARTÃO DE CRÉDITO",
        "99" => "OUTROS CRÉDITOS"
    );

    if($entrada!=null) return $array[$entrada];
    return $array;
}

function valida_cnpj($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

    // Valida tamanho
    if (strlen($cnpj) != 14)
        return false;

    // Verifica se todos os digitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj))
        return false;

    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
    {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
        return false;

    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
    {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

function valida_cpf($cpf) {

    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;

}

function retornar_estados(){
    return array(
        'AC'=>'Acre',
        'AL'=>'Alagoas',
        'AP'=>'Amapá',
        'AM'=>'Amazonas',
        'BA'=>'Bahia',
        'CE'=>'Ceará',
        'DF'=>'Distrito Federal',
        'ES'=>'Espírito Santo',
        'GO'=>'Goiás',
        'MA'=>'Maranhão',
        'MT'=>'Mato Grosso',
        'MS'=>'Mato Grosso do Sul',
        'MG'=>'Minas Gerais',
        'PA'=>'Pará',
        'PB'=>'Paraíba',
        'PR'=>'Paraná',
        'PE'=>'Pernambuco',
        'PI'=>'Piauí',
        'RJ'=>'Rio de Janeiro',
        'RN'=>'Rio Grande do Norte',
        'RS'=>'Rio Grande do Sul',
        'RO'=>'Rondônia',
        'RR'=>'Roraima',
        'SC'=>'Santa Catarina',
        'SP'=>'São Paulo',
        'SE'=>'Sergipe',
        'TO'=>'Tocantins'
    );
}

function diferenca_datas($dt1, $dt2){
    $firstDate  = new DateTime($dt1);
    $secondDate = new DateTime($dt2);
    $intvl = $firstDate->diff($secondDate);
    return $intvl->days;
}

function enviar_email_sendgrid($to,$assunto,$mensagem,$anexos=null){
	$apiKey = adm_env('SENDGRID_API_KEY', '');
	if($apiKey===''){
		return false;
	}

	$headers = [
		'Authorization: Bearer ' . $apiKey,
		'Content-Type: application/json',
	];

	$atachs = [];
	if($anexos!==null){
		foreach($anexos as $i => $anexo):
			$atachs[] = [
				'content' => base64_encode(file_get_contents($anexo['caminho'])),
				'filename' => $anexo['nome'],
				'type' => 'application/pdf',
				'disposition' => 'attachment',
			];
		endforeach;
	}

	$payload = [
		'personalizations' => [[
			'to' => [['email' => $to]],
			'subject' => $assunto,
			'bcc' => [['email' => 'gigiomangia@hotmail.com']]
		]],
		'from' => ['email' => 'boletos@redemaiscredito.com.br', 'name' => 'Rede Mais Crédito'],
		'reply_to' => ['email' => 'redemaiscredito@gmail.com'],
		'content' => [[
			'type' => 'text/html',
			'value' => $mensagem
		]],
		'attachments' => $atachs
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	curl_close($ch);

	if ($curl_error) {
		return [
			'status' => 'erro',
			'mensagem' => 'Erro cURL: ' . $curl_error,
		];
	}

	if ($http_code === 202) {
		return [
			'status' => 'ok',
			'mensagem' => 'E-mail enviado com sucesso!',
		];
	} else {
		// Tenta decodificar resposta com mensagem de erro
		$decoded = json_decode($response, true);
		$mensagemErro = 'Erro desconhecido.';

		if (isset($decoded['errors']) && is_array($decoded['errors'])) {
			$mensagemErro = $decoded['errors'][0]['message'] ?? $mensagemErro;
		}

		return [
			'status' => 'erro',
			'mensagem' => 'Erro ao enviar e-mail. HTTP ' . $http_code . ': ' . $mensagemErro,
		];
	}
}
