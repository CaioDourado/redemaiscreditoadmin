<div class="container-pagina">
    <div class="titulo-pagina">+ Crédito Localiza CPF</div>
    <div class="titulo">Dados da Consulta</div>
    <div class="table-responsive">
        <table class="table table-list">
            <tbody>
            <tr><td>CPF Procurado</td><td><?php echo $relatorio->nome_procurado; ?></td></tr>
            <tr><td>Data</td><td><?php echo $relatorio->data_hora; ?></td></tr>
            </tbody>
        </table>
    </div>
    <div class="titulo">CPFs Retornados</div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
                <tr>
                    <th class="text-right">#</th>
                    <th>Nome</th>
                    <th class="text-center">CPF</th>
                    <th class="text-right">Idade</th>
                    <th>Cidade</th>
                    <th class="text-center">UF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($relatorio->pessoas as $index => $pessoa): ?>
                    <tr>
                        <td class="text-right"><?php echo $index + 1; ?></td>
                        <td><?php echo $pessoa->nome; ?></td>
                        <td class="text-center"><?php echo $pessoa->cpf; ?></td>
                        <td class="text-right"><?php echo $pessoa->idade; ?></td>
                        <td><?php echo $pessoa->cidade; ?></td>
                        <td class="text-center"><?php echo $pessoa->uf; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</div>