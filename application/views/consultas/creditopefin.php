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
                <?php if($relatorio->resumo['cheques_sem_fundo']!=null): ?>
                    <tr>
                        <td class="text-center text-danger"><i class="fa fa-close"></i></td>
                        <td class="text-danger"><b>CHEQUE SEM FUNDOS - BACEN</b></td>
                        <td class="text-center"><?php echo $relatorio->resumo['cheques_sem_fundo']->mais_antigo; ?></td>
                        <td class="text-center"><?php echo $relatorio->resumo['cheques_sem_fundo']->mais_recente; ?></td>
                        <td class="text-right"><?php echo $relatorio->resumo['cheques_sem_fundo']->qtd; ?></td>
                        <td class="text-center">-</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center text-success"><i class="fa fa-check"></i></td>
                        <td class="text-success"><b>CHEQUE SEM FUNDOS - BACEN</b></td>
                        <td colspan="4"></td>
                    </tr>
                <?php endif; ?>

                <?php if($relatorio->resumo['pendencias']!=null): ?>
                    <tr>
                        <td class="text-center text-danger"><i class="fa fa-close"></i></td>
                        <td class="text-danger"><b>RESTRIÇÃO COMERCIAL</b></td>
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
                <tr><td>Signo</td><td><?php echo $relatorio->signo; ?></td></tr>
                <tr><td>Nome da Mãe</td><td><?php echo $relatorio->mae; ?></td></tr>
            </tbody>
        </table>
    </div>
    <div class="titulo">CCF BACEN - Detalhamento</div>
    <?php if($relatorio->cheques_sem_fundo!=null): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-consulta">
                <thead>
                <tr>
                    <th>Banco</th>
                    <th class="text-right">Agencia</th>
                    <th class="text-center">Data</th>
                    <th>Motivo</th>
                    <th class="text-right">Qtd de Cheques</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($relatorio->cheques_sem_fundo as $index => $cheque): ?>
                        <tr>
                            <td><?php echo $cheque->banco.' - '.$cheque->informante; ?></td>
                            <td class="text-right"><?php echo $cheque->agencia; ?></td>
                            <td class="text-center"><?php echo $cheque->data; ?></td>
                            <td><?php echo $cheque->motivo; ?></td>
                            <td class="text-right"><?php echo $cheque->quantidade; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
            <table class="table table-condensed table-list"><tbody><tr><td class="text-success">NADA CONSTA</td></tr></tbody></table>
    <?php endif; ?>
    <div class="titulo">PEFIN - Detalhamento</div>
    <?php if($relatorio->pendencias_financeiras!=null): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-consulta">
                <thead>
                <tr>
                    <th class="text-center">Data</th>
                    <th class="text-right">Valor (R$)</th>
                    <th>Modalidade</th>
                    <th>Avalista</th>
                    <th class="text-right">Contrato</th>
                    <th>Origem</th>
                    <th>Filial</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($relatorio->pendencias_financeiras as $index => $pendencia): ?>
                        <tr>
                            <td class="text-center"><?php echo $pendencia->disponivel; ?></td>
                            <td class="text-right"><?php echo dinheiro(only_numbers($pendencia->valor)/100); ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><?php echo $pendencia->contrato; ?></td>
                            <td><?php echo $pendencia->informante; ?></td>
                            <td><?php echo $pendencia->cidade; ?></td>
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
