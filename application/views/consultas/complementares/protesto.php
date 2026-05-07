<div class="titulo">Protestos em Cartório - Detalhamento</div>
<?php if($relatorio->protestos!=null): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th class="text-center">Data</th>
                <th class="text-center">Cartório</th>
                <th class="text-right">Valor</th>
                <th class="text-center">Telefone</th>
                <th>Endereço</th>
                <th class="text-center">UF</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($relatorio->protestos as $index => $protesto): ?>
                <tr>
                    <td class="text-center"><?php echo $protesto->data; ?></td>
                    <td><?php echo $protesto->nome; ?></td>
                    <td class="text-right"><?php echo $protesto->valor; ?></td>
                    <td class="text-center"><?php echo $protesto->telefone; ?></td>
                    <td><?php echo $protesto->endereco; ?></td>
                    <td class="text-center"><?php echo $protesto->uf; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <table class="table table-condensed table-list"><tbody><tr><td class="text-success">NADA CONSTA</td></tr></tbody></table>
<?php endif; ?>