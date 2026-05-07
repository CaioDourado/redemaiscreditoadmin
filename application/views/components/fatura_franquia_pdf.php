<h5 style="margin-bottom: 0;">Franquia: <?php echo $franquia->nome; ?></h5>
<p style="margin:0; font-size: 10px"><?php echo $franquia->cidade; ?> - <?php echo $franquia->uf; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $franquia->email; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $franquia->telefone1; ?></p>

<table class="table-fatura-header">
    <thead>
    <tr>
        <th style="text-align: left">Período de uso</th>
        <th style="text-align: right">Valor</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>de <?php echo data_pt($fatura->inicio,false) ?> a <?php echo data_pt($fatura->fim,false); ?></td>
        <td style="text-align: right;"><?php echo dinheiro($fatura->valor); ?></td>
    </tr>
    </tbody>
</table>

<table class="table-fatura">
    <thead>
    <tr>
        <th>Nome</th>
        <th style="text-align: right">Valor Unitário(R$)</th>
        <th style="text-align: right">Quantidade</th>
        <th style="text-align: right">Total(R$)</th>
    </tr>
    </thead>
    <tbody>
        <tr><td colspan="4"><b>Consultas</b></td></tr>
        <?php foreach($consultas as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->und); ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->total); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr><td colspan="4"><b>Veiculares</b></td></tr>
        <?php foreach($veiculares as $i => $v): ?>
            <tr>
                <td><?php echo $v->nome; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($v->und); ?></td>
                <td  style="text-align: right"><?php echo $v->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($v->total); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr><td colspan="4"><b>Negativações</b></td></tr>
        <?php foreach($negativacoes as $i => $n): ?>
            <tr>
                <td><?php echo $n->nome; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($n->und); ?></td>
                <td  style="text-align: right"><?php echo $n->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($n->total); ?></td>
            </tr>
        <?php endforeach; ?>
		<tr><td colspan="4"><b>Baixas</b></td></tr>
		<?php foreach($baixas as $i => $b): ?>
			<tr>
				<td><?php echo $b->nome; ?></td>
				<td  style="text-align: right"><?php echo dinheiro($b->und); ?></td>
				<td  style="text-align: right"><?php echo $b->qtd; ?></td>
				<td  style="text-align: right"><?php echo dinheiro($b->total); ?></td>
			</tr>
		<?php endforeach; ?>
        <tr><td colspan="4"><b>Cartas Extra Judiciais</b></td></tr>
        <?php foreach($cartas as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->und); ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->total); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><b>Clientes</b></td>
            <td style="text-align: right"><?php echo dinheiro($fatura->clientes_valor/$fatura->clientes_qtd); ?></td>
            <td style="text-align: right"><?php echo $fatura->clientes_qtd; ?></td>
            <td style="text-align: right"><?php echo dinheiro($fatura->clientes_valor); ?></td>
        </tr>
        <tr><td colspan="4">.</tr>
        <tr><td colspan="3"><b>Total</b></td><td style="text-align: right"><?php echo dinheiro($fatura->valor); ?></td></tr>
    </tbody>
</table>
