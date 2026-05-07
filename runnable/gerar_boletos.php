<?php
    // VARIAVEIS DE CONEXÃO COM BANCO DE DADOS
    $servername = "216.172.172.60";
    $username = "redema95_admin";
    $password = "gigiovani2019";
    $dbname = "redema95_redemaiscredito";

    // CRIANDO CONEXÃO
    $conn = new mysqli($servername, $username, $password, $dbname);

    // SETANDO O CONTEUDO QUE VOLTAR DO BANCO DE DADOS COMO UTF8
    mysqli_set_charset($conn,'utf8');

    // VERIFICANDO CONEXÃO
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

    // Pegar Consultas
    // Pegar Consultas Veiculares
    // Pegar Negativações
    // Pegar Baixas
    // Pegar Cartas

    $hoje = 20;
    //$hoje = date('d');
    $dia_faturamento = $hoje+10;
    $inicio_faturamento = date('Y-m-d',strtotime(date('Y-m').'-'.$hoje.'-1 month')).' 00:00:00';
    $fim_faturamento = date('Y-m').'-'.$hoje.' 23:59:59';

    $sql_clientes = 'SELECT * FROM cliente WHERE dia_vencimento = '.$dia_faturamento.' AND status = 1 AND id_cliente>4 ORDER BY nome_ou_fantasia ASC';
    $sql_faturamento  = 'SELECT id_cliente_fk,entrada,nome,grupo,valor,data ';
    $sql_faturamento .= 'FROM ( ';
    $sql_faturamento .= 'SELECT id_cliente_fk,pesquisa AS entrada,nome,slug AS grupo,valor,criado_em AS data FROM consulta_efetuada WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
    $sql_faturamento .= 'UNION ALL ';
    $sql_faturamento .= 'SELECT id_cliente_fk,"" AS entrada,"+ Crédito Veicular" AS nome,"veicular" AS grupo,valor,criado_em AS data FROM consulta_veicular WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
    $sql_faturamento .= 'UNION ALL ';
    $sql_faturamento .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ Crédito Carta Extra Judicial" AS nome,"carta" AS grupo, valor_carta AS valor, criado_em AS data FROM carta WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
    $sql_faturamento .= 'UNION ALL ';
    $sql_faturamento .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ Crédito Negativação" AS nome,"negativacao" AS grupo, valor, criado_em AS data FROM negativacao WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
    $sql_faturamento .= 'UNION ALL ';
    $sql_faturamento .= 'SELECT id_cliente_fk,CONCAT("CPF/CNPJ: ",cpf_cnpj) AS entrada,"+ Crédito Baixa" AS nome,"baixa" AS grupo, valor, criado_em AS data FROM negativacao_baixa WHERE criado_em BETWEEN "'.$inicio_faturamento.'" AND "'.$fim_faturamento.'" ';
    $sql_faturamento .= ') AS tbmain ';
    $sql_faturamento .= 'LEFT JOIN cliente AS tb2 ON tbmain.id_cliente_fk = tb2.id_cliente ';
    $sql_faturamento .= 'WHERE tbmain.id_cliente_fk > 4 AND tb2.dia_vencimento = '.$dia_faturamento.' AND tb2.status = 1 ';
    $sql_faturamento .= 'ORDER BY data ASC ';

    echo $sql_clientes;
    echo '<br><br>';
    echo $sql_faturamento;

    $faturas = array();

    $clientes_result = $conn->query($sql_clientes);
    $consultas_result = $conn->query($sql_faturamento);

    while($linha = $clientes_result->fetch_assoc()) {
        $fatura_atual = new stdClass();
        $fatura_atual->id_cliente = $linha['id_cliente'];
        $fatura_atual->valor = 0;
        $fatura_atual->qtd_dias_faturamento = 30;
        $fatura_atual->vencimento = date('Y-m').'-'.$dia_faturamento;
        $diferenca_dias = intval(abs(strtotime($linha['criado_em'])-strtotime(date('Y-m-d H:i:s')))/86400);
        if($diferenca_dias>30){
            $fatura_atual->inicio = date('Y-m-d',strtotime(date('Y-m').'-'.$hoje.'-1 month'));
            $fatura_atual->fim = date('Y-m').'-'.$hoje;
            $fatura_atual->nome = 'Mensalidade + Consumo ('.ucfirst(get_mes(date('m'))).')';
            $fatura_atual->tipo = 'Completa';
            $fatura_atual->mensalidade = $linha['mensalidade'];
            $fatura_atual->franquia = $linha['franquia'];
            $fatura_atual->valor = $linha['mensalidade'];
            $fatura_atual->consumo = 0;
        }else{
            $fatura_atual->inicio = date('Y-m-d',strtotime($linha['criado_em']));
            $fatura_atual->fim = date('Y-m').'-'.$hoje;
            $fatura_atual->nome = 'Pró-rata '.date('d/m/Y',strtotime($fatura_atual->inicio)).' à '.date('d/m/Y',strtotime($fatura_atual->fim));
            $fatura_atual->tipo = 'Pró-rata';
            $fatura_atual->mensalidade = dinheiro(($linha['mensalidade']/30)*$diferenca_dias);
            $fatura_atual->franquia = dinheiro(($linha['franquia']/30)*$diferenca_dias);
            $fatura_atual->valor = $fatura_atual->mensalidade;
            $fatura_atual->consumo = 0;
        }
        $fatura_atual->itens = array();
        $faturas[$linha['id_cliente']] = $fatura_atual;
    }

    while($linha = $consultas_result->fetch_assoc()){
        $fatura_item_atual = new stdClass();
        $fatura_item_atual->id_cliente_fk = $linha['id_cliente_fk'];
        $fatura_item_atual->nome = $linha['nome'];
        $fatura_item_atual->descricao = $linha['entrada'];
        $fatura_item_atual->grupo = $linha['grupo'];
        $fatura_item_atual->valor = $linha['valor'];
        $fatura_item_atual->data = $linha['data'];
        $faturas[$linha['id_cliente_fk']]->consumo += $linha['valor'];
        array_push($faturas[$linha['id_cliente_fk']]->itens,$fatura_item_atual);
    }

    // CADASTRO DE DADOS NO BANCO DE DADOS

        // CADASTRAR FATURA
        foreach($faturas as $index => $fatura):
            $id_fatura = inserir_fatura($conn,$fatura);
            foreach($fatura->itens as $indice => $item):
                inserir_fatura_item($conn,$item,$id_fatura);
            endforeach;
        endforeach;


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

    function inserir_fatura($conexao,$fatura){
        if($fatura->consumo>$fatura->franquia) $fatura->valor = dinheiro($fatura->valor+($fatura->consumo-$fatura->franquia));
        $sql  = 'INSERT INTO fatura (id_cliente_fk,nome,inicio,fim,tipo,mensalidade,franquia,consumo,valor,vencimento) VALUES (';
            $sql .= $fatura->id_cliente.',';
            $sql .= '"'.$fatura->nome.'",';
            $sql .= '"'.$fatura->inicio.'",';
            $sql .= '"'.$fatura->fim.'",';
            $sql .= '"'.$fatura->tipo.'",';
            $sql .= '"'.$fatura->mensalidade.'",';
            $sql .= '"'.$fatura->franquia.'",';
            $sql .= '"'.$fatura->consumo.'",';
            $sql .= '"'.$fatura->valor.'",';
            $sql .= '"'.$fatura->vencimento.'"';
        $sql .= ')';
        $conexao->query($sql);
        return $conexao->insert_id;
    }

    function inserir_fatura_item($conexao,$item,$id_fatura){
        $sql  = 'INSERT INTO fatura_item (id_fatura_fk,id_cliente_fk,nome,descricao,grupo,valor,data) VALUES (';
            $sql .= $id_fatura.',';
            $sql .= $item->id_cliente_fk.',';
            $sql .= '"'.$item->nome.'",';
            $sql .= '"'.$item->descricao.'",';
            $sql .= '"'.$item->grupo.'",';
            $sql .= '"'.$item->valor.'",';
            $sql .= '"'.$item->data.'"';
        $sql .= ')';
        $conexao->query($sql);
    }

    function dinheiro($entrada){
        return number_format((float)$entrada, 2, '.', '');
    }
?>