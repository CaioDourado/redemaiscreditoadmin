<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados dos Últimos 7 dias</div>
                <?php foreach($dados as $index => $dia): ?>
                    <div class="panel-body border-top text-center"><h4>Dados dia <?php echo str_replace('_','/',$index); ?></h4></div>
                    <div class="table-responsive">
                        <table class="panel-table table-condensed table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-right"></th>
                                    <th></th>
                                    <th>Tipo</th>
                                    <th class="w-min350">Cliente</th>
                                    <th class="w-min150 text-center">Data</th>
                                    <th class="text-right">Valor</th>
                                    <th class="text-right">Custo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dia as $index => $item): ?>
                                    <tr class="<?php echo $item->status; ?>">
                                        <td class="text-right" style="width: 50px"><?php echo $index+1; ?></td>
                                        <td><?php echo retornar_tipo($item->nome); ?></td>
                                        <td><?php echo str_replace("credito","",$item->slug); ?></td>
                                        <td><?php echo anchor('cliente/perfil/'.$item->id_cliente_fk,$item->nome_cliente); ?></td>
                                        <td class="text-center"><?php echo data_pt($item->criado_em); ?></td>
                                        <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                        <td class="text-right"><?php echo dinheiro($item->custo); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php break;
    case 'index': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Dados dos Últimos 7 dias</div>
                <div class="panel-body">
                    <?php foreach($dados as $index => $dia): ?>
                        <h6>Dados dia <?php echo str_replace('_',' ',$index); ?></h6>
                        <div class="table-responsive">
                            <table class="table table-condensed table-hover table-bordered" style="font-size: 12px">
                                <thead>
                                    <tr>
                                        <th style="width: 50px" class="text-right"></th>
                                        <th style="width: 30px"></th>
                                        <th style="width: 130px">Tipo</th>
                                        <th>Cliente</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-right">Valor</th>
                                        <th class="text-right">Custo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($dia as $index => $item): ?>
                                        <tr>
                                            <td class="text-right" style="width: 50px"><?php echo $index+1; ?></td>
                                            <td class="text-center" style="width: 30px">
                                                <?php if(isset($item->status)) echo $item->status; ?>
                                            </td>
                                            <td><?php echo $item->nome; ?></td>
                                            <td><?php echo anchor('cliente/perfil/'.$item->id_cliente_fk,$item->nome_cliente); ?></td>
                                            <td class="text-center"><?php echo data_pt($item->criado_em); ?></td>
                                            <td class="text-right"><?php echo dinheiro($item->valor); ?></td>
                                            <td class="text-right"><?php echo dinheiro($item->custo); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php break;
endswitch;
