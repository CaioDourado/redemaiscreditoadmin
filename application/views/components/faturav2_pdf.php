<h5 style="margin-bottom: 0;"><?php echo $cliente->nome_ou_fantasia; ?></h5>
<p style="margin:0; font-size: 10px"><?php echo $cliente->logradouro.' '.$cliente->numero.', '.$cliente->complemento; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $cliente->bairro; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $cliente->cep.' '.$cliente->cidade.' '.$cliente->uf; ?></p>

<table class="table-fatura-header">
    <thead>
        <tr>
            <th style="text-align: left">Período de uso</th>
            <th>Vencimento</th>
            <th style="text-align: right">Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>de <?php echo data_pt($fatura->inicio,false) ?> a <?php echo data_pt($fatura->fim,false); ?></td>
            <td style="text-align: center;"><?php echo data_pt($fatura->vencimento,false); ?></td>
            <td style="text-align: right;">105,65</td>
        </tr>
    </tbody>
</table>

<!--h5 style="margin-top: 20px;font-family: Verdana,sans-serif;">Detalhamento de Fatura : <span style="font-weight: 100;"><?php echo $fatura->nome; ?></span></h5-->

<table class="table-fatura">
    <thead>
        <tr>
            <th style="padding-left: 0">Data</th>
            <th>Hora</th>
            <th>Serviço</th>
            <th>Dados Utilizados</th>
            <th>Valor(R$)</th>
            <th>Total(R$)</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td style="padding-left: 0">16/05/2023</td>
                <td>15:59:59</td>
                <td>+ Crédito Pefin PF [+ Protesto + Endereço + Score]</td>
                <td>CPF: 11746842679</td>
                <td class="text-right">10,35</td>
                <td class="text-right">10,35</td>
            </tr>
            <tr>
                <td style="padding-left: 0">16/05/2023</td>
                <td>15:59:59</td>
                <td>+ Crédito Varejo PJ [+ Endereço]</td>
                <td>CPF: 11746842679</td>
                <td class="text-right">4,85</td>
                <td class="text-right">15,20</td>
            </tr>
            <tr>
                <td style="padding-left: 0">16/05/2023</td>
                <td>15:59:59</td>
                <td>+ Crédito Score Plus PF</td>
                <td>CPF: 11746842679</td>
                <td class="text-right">10,15</td>
                <td class="text-right">25,35</td>
            </tr>
            <tr>
                <td style="padding-left: 0">16/05/2023</td>
                <td>15:59:59</td>
                <td>+ Crédito Score Plus PF</td>
                <td>CPF: 11746842679</td>
                <td class="text-right">10,15</td>
                <td class="text-right">35,50</td>
            </tr>
            <tr>
                <td style="padding-left: 0">16/05/2023</td>
                <td>15:59:59</td>
                <td>+ Crédito Pefin PF [+ Protesto + Endereço + Score]</td>
                <td>CPF: 11746842679</td>
                <td class="text-right">10,35</td>
                <td class="text-right">45,65</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left: 0">Franquia</td>
                <td class="text-right"></td>
                <td class="text-right">- 29,90</td>
            </tr>

            <tr><td colspan="6" style="padding-top: 20px"></td></tr>

            <tr>
                <td colspan="4" style="padding-left: 0">Excedente Total</td>
                <td class="text-right"></td>
                <td class="text-right">15,75</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left: 0">Mensalidade Fixa</td>
                <td class="text-right"></td>
                <td class="text-right">89,90</td>
            </tr>

            <tr>
                <td colspan="5" style="padding-left: 0;border-bottom: none;"><strong>Total</strong></td>
                <td class="text-right" style="border-bottom: none;"><strong>105,65</strong></td>
            </tr>
    </tbody>
</table>