<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'atual': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Faturamento de Consultas</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="text-right w-100">Valor Fatura</th>
                                <th class="text-right w-100">Consumo</th>
                                <th class="text-right w-100">Custo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($faturamento_geral as $index => $item): ?>
                                <tr>
                                    <td><?php echo $item['nome']; ?></td>
                                    <td class="text-right"><?php echo dinheiro($item['valor_final']); ?></td>
                                    <td class="text-right"><?php echo dinheiro($item['valor']); ?></td>
                                    <td class="text-right"><?php echo dinheiro($item['custo']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-blue">
                <div class="panel-heading">Faturamento de Consultas</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="text-right">Mensalidade(Frq)</th>
                                <th class="text-right w-100">Qtd</th>
                                <th class="text-right w-100">Consumo</th>
                                <th class="text-right w-100">Custo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($faturamento_consultas as $index => $item): ?>
                                <tr>
                                    <td><?php echo $item->nome_ou_fantasia; ?></td>
                                    <td class="text-right"><?php echo dinheiro($item->mensalidade); ?> (<?php echo dinheiro($item->franquia); ?>)</td>
                                    <td class="text-right"><?php echo $item->qtd; ?></td>
                                    <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                    <td class="text-right"><?php echo dinheiro($item->custo); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-blue">
                <div class="panel-heading">Faturamento de Cartas</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="text-right w-100">Qtd</th>
                            <th class="text-right w-100">Consumo</th>
                            <th class="text-right w-100">Custo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($faturamento_cartas as $index => $item): ?>
                            <tr>
                                <td><?php echo $item->nome_ou_fantasia; ?></td>
                                <td class="text-right"><?php echo $item->qtd; ?></td>
                                <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                <td class="text-right"><?php echo dinheiro(0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-blue">
                <div class="panel-heading">Faturamento de Negativações</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="text-right w-100">Qtd</th>
                            <th class="text-right w-100">Consumo</th>
                            <th class="text-right w-100">Custo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($faturamento_negativacoes as $index => $item): ?>
                            <tr>
                                <td><?php echo $item->nome_ou_fantasia; ?></td>
                                <td class="text-right"><?php echo $item->qtd; ?></td>
                                <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                <td class="text-right"><?php echo dinheiro($item->custo); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-blue">
                <div class="panel-heading">Faturamento de Baixas de Negativação</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="text-right w-100">Qtd</th>
                            <th class="text-right w-100">Consumo</th>
                            <th class="text-right w-100">Custo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($faturamento_negativacoes_baixa as $index => $item): ?>
                            <tr>
                                <td><?php echo $item->nome_ou_fantasia; ?></td>
                                <td class="text-right"><?php echo $item->qtd; ?></td>
                                <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                <td class="text-right"><?php echo dinheiro($item->custo); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'parcial': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Clientes</div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed table-hover no-margin table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="text-center">Criação</th>
                                <th class="text-center">Vcto</th>
                                <th class="text-right">Mensalidade</th>
                                <th class="text-right">Franquia</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $index => $cliente): ?>
                                <?php if($cliente->id_cliente>4): ?>
                                    <tr>
                                        <td><?php echo strtoupper($cliente->nome_ou_fantasia); ?></td>
                                        <td class="text-center"><?php echo data_pt($cliente->criado_em); ?></td>
                                        <td class="text-center"><?php echo $cliente->dia_vencimento; ?></td>
                                        <td class="text-right"><?php echo dinheiro($cliente->mensalidade); ?></td>
                                        <td class="text-right"><?php echo dinheiro($cliente->franquia); ?></td>
                                        <td class="text-center">
                                            <?php echo anchor('faturamento/geracao/'.$cliente->id_cliente,'Geração'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'geracao': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Dados do Cliente</div>
                <div class="panel-body">
                    <p><?php echo $cliente->nome_ou_fantasia; ?></p>
                </div>
            </div>
        <?php break;
    case 'gerar_faturamento': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Geração de Faturas em Massa</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="dia_vcto">Dia de Vencimento</label>
                            <select name="dia_vcto" class="form-control">
                                <?php foreach($dias as $index => $dia): ?>
                                    <option value="<?php echo $index; ?>"><?php echo $dia; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
endswitch;
