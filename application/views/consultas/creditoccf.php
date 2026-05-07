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
                    <tr><td>Quantidade</td><td><?php echo $relatorio->quantidade; ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">

        </div>
    </div>

    <?php if($endereco!=null) echo $endereco; ?>

    <div class="titulo">CCF BACEN - Detalhamento</div>
    <?php if(count($relatorio->ocorrencias)>0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
                <tr>
                    <th></th>
                    <th>Banco</th>
                    <th class="text-right">Agencia</th>
                    <th class="text-right">Motivo Devolução</th>
                    <th class="text-right">Qtd</th>
                    <th class="text-center">Última OCorrencia</th>
                    <th>Dados Agência</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($relatorio->ocorrencias as $index => $ocorrencia): ?>
                    <tr>
                        <td><?php echo $index+1; ?></td>
                        <td><?php echo $ocorrencia->banco_numero.' - '.$ocorrencia->banco; ?></td>
                        <td class="text-right"><?php echo $ocorrencia->agencia; ?></td>
                        <td class="text-right"><?php echo $ocorrencia->motivo_devolucao; ?></td>
                        <td class="text-right"><?php echo $ocorrencia->qtd; ?></td>
                        <td class="text-center"><?php echo $ocorrencia->ultima_ocorrencia; ?></td>
                        <td><?php echo $ocorrencia->dados_agencia; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-consulta"><tbody><tr><td><b>SEM OCORRENCIAS</b></td></tr></tbody></table></div>
    <?php endif; ?>

    <div class="titulo">PASSAGENS COMERCIAIS - Detalhamento</div>
    <?php if(count($relatorio->passagens)>0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-consulta">
                <thead>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-center">Data</th>
                        <th class="text-center">Hora</th>
                        <th>Cliente</th>
                        <th class="text-center">Telefone</th>
                        <th>Cidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($relatorio->passagens as $index => $passagem): ?>
                        <tr>
                            <td class="text-right"><?php echo $index+1; ?></td>
                            <td class="text-center"><?php echo $passagem->data; ?></td>
                            <td class="text-center"><?php echo $passagem->hora; ?></td>
                            <td><?php echo $passagem->cliente; ?></td>
                            <td class="text-center"><?php echo $passagem->telefone; ?></td>
                            <td><?php echo $passagem->cidade; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-consulta"><tbody><tr><td><b>SEM PASSAGEM COMERCIAL</b></td></tr></tbody></table></div>
    <?php endif; ?>

    <?php if($protestos!=null) echo $protestos; ?>
</div>