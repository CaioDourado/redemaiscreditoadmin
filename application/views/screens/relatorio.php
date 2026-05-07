<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-google">
                        <div class="panel-heading">Clientes Ativos</div>
                        <div class="panel-body border-top">
                            <p>Matriz <span class="pull-right"><?php echo $dados->qtd_matriz; ?></span></p>
                            <p style="margin: 0">Franquias <span class="pull-right"><?php echo $dados->qtd_franquia; ?></span></p>
                        </div>
                        <div class="panel-footer text-right">
                            <?php echo $dados->total_qtd; ?> Clientes
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-google">
                        <div class="panel-heading">Faturamento Estimado</div>
                        <div class="panel-body border-top">
                            <p>Matriz <span class="pull-right"><?php echo dinheiro($dados->val_matriz); ?></span></p>
                            <p style="margin: 0">Franquias <span class="pull-right"><?php echo dinheiro($dados->val_franquia); ?></span></p>
                        </div>
                        <div class="panel-footer text-right">
                            <?php echo dinheiro($dados->total_val); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo anchor('relatorio/fornecedores','Relatório de Fornecedores',array('class'=>'btn btn-info')); ?>
            <?php echo anchor('relatorio/pagamentos','Relatório de Pagamentos',array('class'=>'btn btn-info')); ?>
            <?php echo anchor('relatorio/abertura_clientes','Relatório de Abertura de Clientes',array('class'=>'btn btn-info')); ?>
        <?php break;
    case 'fornecedores': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados Para Consulta</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="inicio">Data Inicial</label>
                                    <input type="text" class="form-control text-center" value="<?php echo data_pt($inicio, false); ?>" name="inicio">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fim">Data Final</label>
                                    <input type="text" class="form-control text-center" value="<?php echo data_pt($fim, false); ?>" name="fim">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo form_submit('submit','Consultar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
            <?php if($relatorio != null): ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Resumo</div>
                    <div class="table-responsive">
                        <table class="panel-table table-hover no-margin">
                            <thead>
                                <tr>
                                    <th style="width: 80px;"></th>
                                    <th>Fornecedor</th>
                                    <th class="text-right">Qtd</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $c=1; foreach($resumo as $i => $r): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $c; ?></td>
                                        <td><?php echo $i; ?></td>
                                        <td class="text-right"><?php echo $r->qtd; ?></td>
                                        <td class="text-right"><?php echo dinheiro($r->total); ?></td>
                                    </tr>
                                <?php $c++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-google">
                    <div class="panel-heading">Crediticia e Cadastral</div>
                    <div class="table-responsive">
                        <table class="panel-table table-hover no-margin">
                            <thead>
                                <tr>
                                    <th style="width: 80px;"></th>
                                    <th>Fornecedor</th>
                                    <th>Consulta</th>
                                    <th class="text-right">Qtd</th>
                                    <th class="text-right">Preço Médio</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($relatorio as $i => $l): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $i+1; ?></td>
                                        <td><?php echo $l->fornecedor; ?></td>
                                        <td><?php echo $l->slug; ?></td>
                                        <td class="text-right"><?php echo $l->qtd; ?></td>
                                        <td class="text-right"><?php echo dinheiro($l->preco_medio); ?></td>
                                        <td class="text-right"><?php echo dinheiro($l->total); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-google">
                    <div class="panel-heading">Veicular</div>
                    <div class="table-responsive">
                        <table class="panel-table table-hover no-margin">
                            <thead>
                            <tr>
                                <th style="width: 80px;"></th>
                                <th>Fornecedor</th>
                                <th>Consulta</th>
                                <th class="text-right">Qtd</th>
                                <th class="text-right">Preço Médio</th>
                                <th class="text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($veicular as $i => $l): ?>
                                <tr>
                                    <td class="text-right"><?php echo $i+1; ?></td>
                                    <td><?php echo $l->fornecedor; ?></td>
                                    <td><?php echo $l->slug; ?></td>
                                    <td class="text-right"><?php echo $l->qtd; ?></td>
                                    <td class="text-right"><?php echo dinheiro($l->preco_medio); ?></td>
                                    <td class="text-right"><?php echo dinheiro($l->total); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-google">
                    <div class="panel-heading">Negativações e Baixas</div>
                    <div class="table-responsive">
                        <table class="panel-table table-hover no-margin">
                            <thead>
                            <tr>
                                <th style="width: 80px;"></th>
                                <th>Fornecedor</th>
                                <th>Consulta</th>
                                <th class="text-right">Qtd</th>
                                <th class="text-right">Preço Médio</th>
                                <th class="text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($negativacoes as $i => $l): ?>
                                <tr>
                                    <td class="text-right"><?php echo $i+1; ?></td>
                                    <td><?php echo $l->fornecedor; ?></td>
                                    <td><?php echo $l->slug; ?></td>
                                    <td class="text-right"><?php echo $l->qtd; ?></td>
                                    <td class="text-right"><?php echo dinheiro($l->preco_medio); ?></td>
                                    <td class="text-right"><?php echo dinheiro($l->total); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php break;
    case 'pagamentos': ?>

        <?php break;
    case 'abertura_clientes': ?>
            
        <?php break;
    default:
        break;
endswitch;
