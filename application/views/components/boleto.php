<?php

    // DADOS PARA CRIAÇÃO DO BOLETO

    $beneficiario = strtoupper('Rede Mais Crédito');
    $cpfcnpj_ben = EMPRESA_CNPJ;
    $endereco_ben = strtoupper(EMPRESA_ENDERECO);
    $bairro_ben = strtoupper(EMPRESA_BAIRRO);
    $cepcidade_ben = strtoupper(EMPRESA_CEP.' - '.EMPRESA_CIDADE.'/'.EMPRESA_UF);
    $telefone_ben = '(32) 98454-2951 | (32) 98455-1023';

    $cooperativaContratante = $conta->agencia;
    $codBeneficiario = $conta->codigo_cedente;


    // Do pagador
    $pagador = strtoupper($boleto->nome_sacado);
    $nome_fantasia = $cliente->nome_ou_fantasia;
    $razao_social = $cliente->razao_social;
    $cpfcnpj_pag = $boleto->cpf_cnpj;
    $endereco_pag = strtoupper($boleto->logradouro.' '.$boleto->numero.','.$boleto->complemento);
    $bairro_pag = strtoupper($boleto->bairro);
    $cepcidade_pag = strtoupper($boleto->cep.' '.$boleto->cidade.'/'.$boleto->uf);
    $telefone_pag = '-';

    // Especificações do Boleto

    $datadoc = $boleto->criado_em;
    $especidedoc = 'DM';
    $aceite = 'N';
    $numerodoc = $boleto->id_boleto;
    $nossonumero_normal = $boleto->nosso_numero;
    $nossonumero = substr($boleto->nosso_numero_formatado,0,7).'-'.substr($boleto->nosso_numero_formatado,7,1);
    $carteira = '1';
    $especie = 'R$';
    $dataprocessamento = $boleto->criado_em;

    $cooperativaemitente = 'SICOOB COOPEMATA';

    $mensagem = '';

    $vencimento = date("d/m/Y",strtotime($boleto->data_vencimento));
    $valor = $boleto->valor_boleto;

    $locaisdepagamento = 'Pagável em qualquer banco ou lotérica até a data de vencimento';

    $descricao_1 = $boleto->descricao_boleto;
    $descricao_2 = $boleto->descricao_boleto2;
    $descricao_3 = $boleto->descricao_boleto3;
    $descricao_4 = $boleto->descricao_boleto4;
    $instrucao   = $boleto->instrucao_boleto;
    $instrucao2  = $boleto->instrucao_boleto2;
    $instrucao3  = $boleto->instrucao_boleto3;
    $instrucao4  = $boleto->instrucao_boleto4;

    $desconto_abatimento = '&nbsp;';
    $outras_deducoes = '&nbsp;';
    $mora_multa = '&nbsp;';
    $outros_acrescimos = '&nbsp;';
    $valor_cobrado = '&nbsp;';

    $codigo_de_barras_digitavel = $boleto->linha_digitavel;
    $codigo_de_barras = $boleto->codigo_de_barras;
?>

<page>
    <!--div>
        <img src="<?php echo base_url(); ?>/assets/imgs/logofinal.png" style="width:80px;">
    </div-->
    <div style="width:95%;display:block">
        <table style="width:100%;margin:0;padding:0">
            <tr>
                <td style="width: 50%;border:1px solid #000;padding-bottom:0">
                    <table style="font-size: 8px">
                        <tbody style="vertical-align: top">
                            <tr><td style="vertical-align: top">BENEFICIARIO:   </td><td><?php echo $beneficiario; ?></td></tr>
                            <tr><td style="vertical-align: top">CPF/CNPJ:       </td><td><?php echo $cpfcnpj_ben; ?></td></tr>
                            <tr><td style="vertical-align: top">ENDEREÇO:       </td><td><?php echo $endereco_ben; ?></td></tr>
                            <tr><td style="vertical-align: top">BAIRRO:         </td><td><?php echo $bairro_ben; ?></td></tr>
                            <tr><td style="vertical-align: top">CEP/CIDADE:     </td><td><?php echo $cepcidade_ben; ?></td></tr>
                            <tr><td style="vertical-align: top">TELEFONE:       </td><td><?php echo $telefone_ben; ?></td>
                            <?php if($razao_social!=null): ?> <tr><td>-</td><td></td></tr> <?php endif; ?>
                        </tbody>
                    </table>
                </td>
                <td style="width: 50%;border:1px solid #000;padding-bottom:0">
                    <table style="font-size: 8px">
                        <tr><td style="vertical-align: top">PAGADOR:        </td><td><?php echo $pagador; ?></td></tr>
                        <?php if($razao_social!=null): ?>   <tr><td style="vertical-align: top">FANTASIA:        </td><td><?php echo strtoupper($nome_fantasia); ?></td></tr><?php endif; ?>
                        <tr><td style="vertical-align: top">CPF/CNPJ:       </td><td><?php echo $cpfcnpj_pag; ?></td></tr>
                        <tr><td style="vertical-align: top">ENDEREÇO:       </td><td><?php echo $endereco_pag; ?></td></tr>
                        <tr><td style="vertical-align: top">BAIRRO:         </td><td><?php echo $bairro_pag; ?></td></tr>
                        <tr><td style="vertical-align: top">CEP/CIDADE:     </td><td><?php echo $cepcidade_pag; ?></td></tr>
                        <tr><td style="vertical-align: top">TELEFONE:       </td><td><?php echo $telefone_pag; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width:100%">
            <tr>
                <td style="width: 100%;border:1px solid #000">
                    <table style="font-size: 8px">
                        <tr><td style="height: 10px"><b>Dados do Boleto</b></td></tr>
                        <tr><td style="height: 10px"><?php echo $descricao_1; ?></td></tr>
                        <tr><td style="height: 10px"><?php echo $descricao_2; ?></td></tr>
                        <tr><td style="height: 10px"><?php echo $descricao_3; ?></td></tr>
                        <tr><td style="height: 10px"><?php echo $descricao_4; ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width:100%">
            <tr>
                <td style="width: 50%;border:1px solid #000">
                    <table style="width:100%;font-size: 10px">
                        <tr>
                            <td style="width:40%;">Data Doc.: <b><?php echo date("d/m/Y",strtotime($datadoc)); ?> </b></td>
                            <td style="width:40%;">Espécie do doc.: <b><?php echo $especidedoc; ?> </b></td>
                            <td style="width:20%;">Aceite: <b><?php echo $aceite; ?> </b></td>
                        </tr>
                        <tr>
                            <td style="width:40%;">Nº do Doc: <b><?php echo $numerodoc; ?> </b></td>
                            <td style="width:40%;">Nosso número: <b><?php echo $nossonumero_normal; ?> </b></td>
                            <td style="width:20%;">Carteira: <b><?php echo $carteira; ?> </b></td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;border:1px solid #000;background-color: #CCC">
                    <table style="width: 100%;font-size: 10px">
                        <tr><td>Cooperativa Emitente do Título</td></tr>
                        <tr><td style="text-align: right"><b><?php echo $cooperativaemitente; ?> </b></td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width:100%;border-bottom: 1px dashed #000;padding-bottom: 15px">
            <tr>
                <td style="width: 50%;border:1px solid #000;background-color: #CCC">
                    <table style="width:100%;font-size:11px">
                        <tr>
                            <td style="width: 50%">
                                Vencimento <b><?php echo $vencimento; ?> </b>
                            </td>
                            <td style="width: 50%">
                                Valor: R$ <b><?php echo dinheiro($valor); ?> </b>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;margin:0;padding:0">
                    <table style="width:100%;height:100%;margin:0;padding:0">
                        <tr style="margin:0;padding:0">
                            <td style="width: 20%;border-left: 1px solid #000;border-top:1px solid #000;padding:0;margin:0"></td>
                            <td style="width: 60%;font-size:8px;text-align:center;padding:0;margin:0">Autenticação Mecânica  - <b>Recibo do Pagador</b></td>
                            <td style="width: 20%;border-right: 1px solid #000;border-top:1px solid #000;padding:0;margin:0"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    <br>
    <table style="width: 100%;margin:0;padding:0;" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:20%;border-right: 1px solid #000;text-align: center;"><img src="<?php echo base_url(); ?>/assets/imgs/logofinal.png" style="width:80px;"></td>
            <td style="width:10%;font-size:12px;text-align:center;"><?php echo $conta->banco_numero; ?> </td>
            <td style="width:70%;border-left: 1px solid #000;font-size: 12px;text-align: center;"><?php echo $codigo_de_barras_digitavel; ?> </td>
        </tr>
    </table>
    <table style="width: 100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 70%;border: 1px solid #000;border-right: none;border-bottom: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td style="padding:0;margin:0">Local do pagamento</td></tr>
                    <tr><td style="text-right;padding:0;margin:0"><b><?php echo $locaisdepagamento; ?> </b></td></tr>
                </table>
            </td>
            <td style="width: 30%;border: 1px solid #000;border-bottom: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Vencimento</td></tr>
                    <tr><td style="text-align: right"><b><?php echo $vencimento; ?> </b></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;border: 1px solid #000;border-bottom: none;border-right: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Beneficiário</td></tr>
                    <tr><td style="text-right"><b><?php echo $beneficiario; ?> </b></td></tr>
                </table>
            </td>
            <td style="width: 30%;border: 1px solid #000;border-bottom: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Cooperativa / Beneficiário</td></tr>
                    <tr><td style="text-align: right"><b><?php echo $cooperativaContratante; ?> / <?php echo $codBeneficiario; ?> </b></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;border: 1px solid #000;border-right: none;border-bottom: none">
                <table style="width: 100%;font-size: 10px" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Data Doc</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Nº Documento</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Espécie</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Aceite</td>
                        <td style="padding: 3px 5px 2px 5px">Data Proc.</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo data_pt($datadoc,false); ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo $numerodoc; ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo $especie; ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo $aceite; ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;text-align: right"><b><?php echo data_pt($dataprocessamento,false); ?> </b></td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%;border: 1px solid #000;border-bottom: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Nosso Número</td></tr>
                    <tr><td style="text-align: right"><b><?php echo $nossonumero; ?> </b></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;border: 1px solid #000;border-right: none;border-bottom: none">
                <table style="width: 100%;font-size: 10px" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Uso do Banco</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Quantidade</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Carteira</td>
                        <td style="border-right: 1px solid #000;padding: 3px 5px 2px 5px">Espécie</td>
                        <td style="padding: 3px 5px 2px 5px">Valor</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo data_pt($datadoc,false); ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b></b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo $carteira; ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;border-right: 1px solid #000;text-align: right"><b><?php echo $especidedoc; ?> </b></td>
                        <td style="padding: 2px 5px 3px 5px;text-align: right"><b></b></td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%;border: 1px solid #000;border-bottom: none">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Valor Documento</td></tr>
                    <tr><td style="text-align: right"><b><?php echo dinheiro($valor); ?> </b></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;border: 1px solid #000;border-right: none;border-bottom: none">
                <table style="width: 100%;font-size: 10px; vertical-align: top">
                    <tr>
                        <td style="vertical-align: top;height: 130px">
                            Instruções (texto de responsabilidade do Beneficiário)
                            <br>
                            <br>
                            <b><?php echo $instrucao; ?> </b>
                            <br>
                            <b><?php echo $instrucao2; ?> <?php echo $instrucao3; ?></b>
                            <br>
                            <b><?php echo $instrucao4; ?> </b>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%;border: 1px solid #000;border-bottom: none">
                <table style="width: 100%;font-size: 10px" cellpadding="0" cellspacing="0">
                    <tr><td style="padding: 0 5px">(-) Desconto / Abatimento</td></tr>
                    <tr><td style="text-align: right; border-bottom: 1px solid #000"><b><?php echo $desconto_abatimento; ?> </b></td></tr>
                    <tr><td style="padding: 0 5px">(-) Outras deduções</td></tr>
                    <tr><td style="text-align: right; border-bottom: 1px solid #000"><b><?php echo $outras_deducoes; ?> </b></td></tr>
                    <tr><td style="padding: 0 5px">(+) Mora / Multa</td></tr>
                    <tr><td style="text-align: right; border-bottom: 1px solid #000"><?php echo $mora_multa; ?></td></tr>
                    <tr><td style="padding: 0 5px">(+) Outros acréscimos</td></tr>
                    <tr><td style="text-align: right; border-bottom: 1px solid #000"><?php echo $outros_acrescimos; ?></td></tr>
                    <tr><td style="padding: 0 5px">(=) Valor cobrado</td></tr>
                    <tr><td style="text-align: right"><?php echo $valor_cobrado; ?></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000">
                <table style="width: 100%;font-size: 10px">
                    <tr><td>Pagador</td></tr>
                    <tr><td><b><?php echo $pagador; ?>  - <?php echo $cpfcnpj_pag; ?> </b></td></tr>
                    <tr><td><b></b><b><?php echo $endereco_pag; ?> , <?php echo $bairro_pag; ?></td></tr>
                    <tr><td><b><?php echo $cepcidade_pag; ?></b></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 5px">
                <barcode type="I25" code="<?php echo $codigo_de_barras; ?>" size="0.7" height="2"></barcode>
            </td>
            <td style="text-align: center;vertical-align: top;font-size: 10px">Autenticação Mecânica</td>
        </tr>
    </table>
</page>