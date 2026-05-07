<h5 style="margin-bottom: 0;">Franquia: <?php echo $franquia->nome; ?></h5>
<p style="margin:0; font-size: 10px"><?php echo $franquia->cidade; ?> - <?php echo $franquia->uf; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $franquia->email; ?></p>
<p style="margin:0; font-size: 10px"><?php echo $franquia->telefone1; ?></p>

<table class="table-fatura-header">
    <thead>
    <tr>
        <th style="text-align: left">Período de uso</th>
        <th style="text-align: right"></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>de <?php echo data_pt($inicio,false) ?> a <?php echo data_pt($fim,false); ?></td>
        <td style="text-align: right;"><?php //echo dinheiro($valor); ?></td>
    </tr>
    </tbody>
</table>

<!--h5 style="margin-top: 20px;font-family: Verdana,sans-serif;">Detalhamento de Fatura : <span style="font-weight: 100;"><?php echo $fatura->nome; ?></span></h5-->

<table class="table-fatura">
    <thead>
        <tr>
            <th>Nome</th>
            <th style="text-align: right">Quantidade</th>
            <th style="text-align: right">Custo (R$)</th>
            <th style="text-align: right">Venda (R$)</th>
            <th style="text-align: right">Lucro (R$)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="5"><b>Consultas</b></td></tr>
        <?php foreach($consultas as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->custo); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0) echo dinheiro($c->venda); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0)  echo dinheiro($c->venda - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th></th>
            <th class="text-right"><?php echo $totais['consultas']['qtd']; ?></th>
            <th class="text-right"><?php echo dinheiro($totais['consultas']['custo']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['consultas']['venda']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['consultas']['lucro']); ?></th>
        </tr>
        <tr><td colspan="5"><b>Veiculares</b></td></tr>
        <?php foreach($veiculares as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->custo); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0) echo dinheiro($c->venda); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0)  echo dinheiro($c->venda - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th></th>
            <th class="text-right"><?php echo $totais['veiculares']['qtd']; ?></th>
            <th class="text-right"><?php echo dinheiro($totais['veiculares']['custo']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['veiculares']['venda']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['veiculares']['lucro']); ?></th>
        </tr>
        <tr><td colspan="5"><b>Negativações</b></td></tr>
        <?php foreach($negativacoes as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->custo); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0) echo dinheiro($c->venda); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0)  echo dinheiro($c->venda - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th></th>
            <th class="text-right"><?php echo $totais['negativacoes']['qtd']; ?></th>
            <th class="text-right"><?php echo dinheiro($totais['negativacoes']['custo']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['negativacoes']['venda']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['negativacoes']['lucro']); ?></th>
        </tr>
        <!--tr><td colspan="5"><b>Baixas</b></td></tr>
        <?php foreach($baixas as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->custo); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0) echo dinheiro($c->venda); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0)  echo dinheiro($c->venda - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th></th>
            <th class="text-right"><?php echo $totais['baixas']['qtd']; ?></th>
            <th class="text-right"><?php echo dinheiro($totais['baixas']['custo']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['baixas']['venda']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['baixas']['lucro']); ?></th>
        </tr-->
        <tr><td colspan="5"><b>Cartas</b></td></tr>
        <?php foreach($cartas as $i => $c): ?>
            <tr>
                <td><?php echo $c->nome; ?></td>
                <td  style="text-align: right"><?php echo $c->qtd; ?></td>
                <td  style="text-align: right"><?php echo dinheiro($c->custo); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0) echo dinheiro($c->venda); ?></td>
                <td  style="text-align: right"><?php if($c->consultor==0)  echo dinheiro($c->venda - $c->custo); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th></th>
            <th class="text-right"><?php echo $totais['cartas']['qtd']; ?></th>
            <th class="text-right"><?php echo dinheiro($totais['cartas']['custo']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['cartas']['venda']); ?></th>
            <th class="text-right"><?php echo dinheiro($totais['cartas']['lucro']); ?></th>
        </tr>
    </tbody>
</table>