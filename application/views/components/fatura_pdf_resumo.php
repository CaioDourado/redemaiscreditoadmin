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
        <td style="text-align: right;"><?php echo dinheiro($fatura->valor); ?></td>
    </tr>
    </tbody>
</table>

<!--h5 style="margin-top: 20px;font-family: Verdana,sans-serif;">Detalhamento de Fatura : <span style="font-weight: 100;"><?php echo $fatura->nome; ?></span></h5-->

<table class="table-fatura">
    <thead>
        <tr>
            <th style="padding-left: 0">#</th>
            <th>Nome</th>
            <th style="text-align: right">Valor Unitário(R$)</th>
            <th style="text-align: right">Quantidade</th>
            <th style="text-align: right">Total(R$)</th>
        </tr>
    </thead>
    <tbody>
        <?php $franquia = $fatura->mensalidade; $total = 0; $qtd =0; ?>
        <?php foreach($itens as $index => $item): ?>
            <?php if($franquia>0) $franquia -= $item->total; ?>
            <?php if($franquia<=0) $franquia = 0; ?>
            <?php $total += $item->total; ?>
            <?php $qtd += $item->qtd; ?>
            <tr>
                <td style="padding-left: 0"><?php echo $index+1; ?></td>
                <td><?php echo $item->nome; ?></td>
                <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                <td class="text-right"><?php echo $item->qtd; ?></td>
                <td class="text-right"><?php echo dinheiro($item->total); ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="3" style="border-bottom: none"></td>
            <td class="text-right" style="border-bottom: none"><b><?php echo $qtd; ?></b></td>
            <td class="text-right" style="border-bottom: none"><b><?php echo dinheiro($total); ?></b></td>
        </tr>

        <tr><td colspan="5" style="padding-top: 20px"></td></tr>
        <?php $total += ($fatura->mensalidade-$fatura->franquia); ?>
        <?php if($total<$fatura->mensalidade) $total = $fatura->mensalidade; ?>

        <tr>
            <td colspan="2" style="padding-left: 0">Mensalidade Fixa</td>
            <td class="text-right"><?php echo dinheiro($fatura->mensalidade - $fatura->franquia); ?></td>
            <td></td>
            <td class="text-right"><?php echo dinheiro($total); ?></td>
        </tr>

        <?php if($fatura->credito>0): ?>
            <tr>
                <td colspan="2" style="padding-left: 0">Desconto (Crédito)</td>
                <td class="text-right"><?php echo dinheiro($fatura->credito); ?></td>
                <td></td>
                <td class="text-right"><?php echo dinheiro(abs($total - $fatura->credito)); ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="4" style="padding-left: 0;border-bottom: none;"><strong>Total</strong></td>
            <td class="text-right" style="border-bottom: none;"><strong><?php
                    if($fatura->credito>0)
                        echo dinheiro(abs($total - $fatura->credito));
                    else
                        echo dinheiro($total);
                    ?></strong></td>
        </tr>
    </tbody>
</table>