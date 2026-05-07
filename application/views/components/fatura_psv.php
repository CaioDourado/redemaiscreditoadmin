<h5 style="margin-bottom: 0;"><?php echo $cliente->nome_ou_fantasia; ?></h5>
<p style="margin:0; font-size: 10px"><?php echo $cliente->logradouro.' '.$cliente->numero.', '.$cliente->complemento; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $cliente->bairro; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $cliente->cep.' '.$cliente->cidade.' '.$cliente->uf; ?></p>

<table class="table-fatura-header">
    <thead>
        <tr>
            <th style="text-align: left">Período</th>
            <th style="text-align: right">Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>de <?php echo data_pt($inicio,false) ?> a <?php echo data_pt($fim,false); ?></td>
            <td style="text-align: right;"><?php echo dinheiro($valor); ?></td>
        </tr>
    </tbody>
</table>

<table class="table-fatura">
    <thead>
        <tr>
            <th style="padding-left: 0">Cliente</th>
            <th style="text-align: right">Valor</th>
            <th style="text-align: right">Custo</th>
            <th style="text-align: right">Consumo</th>
            <th style="text-align: right">PSV</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $i => $c): ?>
            <tr>
                <td><?php echo strtoupper($c['cliente']); ?></td>
                <td class="text-right"><?php echo dinheiro($c['valor']); ?></td>
                <td class="text-right"><?php echo dinheiro($c['custo_cliente']); ?></td>
                <td class="text-right"><?php echo dinheiro($c['custo']); ?></td>
                <td class="text-right"><?php echo dinheiro($c['final']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr><td colspan="5" style="padding-top: 20px"></td></tr>
        <tr>
            <td colspan="4"></td>
            <td class="text-right"><?php echo dinheiro($valor); ?></td>
        </tr>
    </tbody>
</table>

<table class="table-fatura">
    <thead>
        <tr>
            <th style="padding-left: 0">Cliente</th>
            <th>Consulta</th>
            <th style="text-align: right">Valor</th>
            <th style="text-align: right">Custo</th>
            <th style="text-align: right">Líquido</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($consumo as $i => $c): ?>
            <tr>
                <td><?php echo $c->cliente; ?></td>
                <td><?php echo $c->nome; ?></td>
                <td class="text-right"><?php echo dinheiro($c->valor); ?></td>
                <td class="text-right"><?php echo dinheiro($c->custo); ?></td>
                <td class="text-right"><?php echo dinheiro($c->valor - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>