<div class="container-pagina">
    <div class="titulo-pagina">+ Crédito Pefin</div>
    <div class="row">
        <div class="col-md-4">
            <div class="titulo">Dados da Consulta</div>
            <div class="table-responsive">
                <table class="table table-list">
                    <tbody>
                    <tr><td>CPF Procurado</td><td><?php echo $relatorio->cpf_procurado; ?></td></tr>
                    <tr><td>Data</td><td><?php echo $relatorio->data_hora; ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="titulo">Resumo da Consulta</div>
            <div class="table-responsive">
                <table class="table table-condensed table-list">
                    <tbody>
                    <tr><td>CPF</td><td><?php echo $relatorio->cpf; ?></td></tr>
                    <tr><td>Nome</td><td><?php echo $relatorio->nome; ?></td></tr>
                    <tr><td>Data Nascimento</td><td><?php echo $relatorio->data_nascimento; ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">

        </div>
    </div>

    <?php if($endereco!=null) echo $endereco; ?>

    <div class="titulo">Resumo de Restrições</div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th></th>
                <th>Restrição</th>
                <th class="text-center">Data do Antigo</th>
                <th class="text-center">Data do Recente</th>
                <th class="text-right">Qtd. Total</th>
                <th class="text-right">Valor Total (R$)</th>
            </tr>
            </thead>
            <tbody>

            <?php if($relatorio->resumo['pendencias']!=null): ?>
                <tr>
                    <td class="text-center text-danger"><i class="fa fa-close"></i></td>
                    <td class="text-danger"><b>CREDIÁRIO</b></td>
                    <td class="text-center"><?php echo $relatorio->resumo['pendencias']->mais_antigo; ?></td>
                    <td class="text-center"><?php echo $relatorio->resumo['pendencias']->mais_recente; ?></td>
                    <td class="text-right"><?php echo $relatorio->resumo['pendencias']->qtd; ?></td>
                    <td class="text-right"><?php echo dinheiro($relatorio->resumo['pendencias']->valor); ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td class="text-center text-success"><i class="fa fa-check"></i></td>
                    <td class="text-success"><b>RESTRIÇÃO COMERCIAL</b></td>
                    <td colspan="4"></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="titulo">Dados do Pesquisado</div>
    <div class="table-responsive">
        <table class="table table-condensed table-list">
            <tbody> 
            <tr><td>CPF</td><td><?php echo $relatorio->cpf; ?></td></tr>
            <tr><td>Nome</td><td><?php echo $relatorio->nome; ?></td></tr>
            <tr><td>Data Nascimento</td><td><?php echo $relatorio->data_nascimento; ?></td></tr>
            <tr><td>Nome da Mãe</td><td><?php echo $relatorio->mae; ?></td></tr>
            </tbody>
        </table>
    </div>
    <div class="titulo">Crediário - Detalhamento</div>
    <?php if($relatorio->ocorrencias!=null): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-consulta">
                <thead>
                <tr>
                    <th>Informante</th>
                    <th class="text-right">Contrato</th>
                    <th class="text-center">Data Débito</th>
                    <th class="text-center">Data Disponível</th>
                    <th class="text-right">Valor (R$)</th>
                    <th>Cidade/UF</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($relatorio->ocorrencias as $index => $pendencia): ?>
                    <tr>
                        <td><?php echo $pendencia->credor; ?></td>
                        <td class="text-right"><?php echo $pendencia->contrato; ?></td>
                        <td class="text-center"><?php echo $pendencia->data_vencimento; ?></td>
                        <td class="text-center"><?php echo $pendencia->data_inclusao; ?></td>
                        <td class="text-right"><?php echo dinheiro(only_numbers($pendencia->valor)/100); ?></td>
                        <td><?php echo $pendencia->origem; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <table class="table table-condensed table-list"><tbody><tr><td class="text-success">NADA CONSTA</td></tr></tbody></table>
    <?php endif; ?>

    <?php if($protestos!=null) echo $protestos; ?>
</div>
