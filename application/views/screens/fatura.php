<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Faturas ADM de Franquias</div>
                <table class="panel-table table-condensed table-hover no-margin table-bordered">
                    <thead>
                    <tr>
                        <th class="text-right">#</th>
                        <th>Franquia</th>
                        <th>Periodo</th>
                        <th class="text-center">Vencimento</th>
                        <th class="text-right">Valor</th>
                        <th class="text-center">Fatura</th>
                        <th class="text-center">Boleto</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($faturas_adm) && count($faturas_adm)>0): ?>
                        <?php foreach($faturas_adm as $index => $fatura): ?>
                            <tr>
                                <td class="text-right"><?php echo $fatura->id_adm_franquia_fatura; ?></td>
                                <td><?php echo strtoupper($fatura->franquia_nome); ?></td>
                                <td><?php echo data_pt($fatura->inicio,false).' ate '.data_pt($fatura->fim,false); ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->vencimento,false); ?></td>
                                <td class="text-right"><?php echo dinheiro($fatura->valor); ?></td>
                                <td class="text-center">
                                    <?php echo anchor('franquia/fatura_visualizar/'.$fatura->id_adm_franquia_fatura,'Abrir Fatura',array('class'=>'btn btn-default btn-xs','target'=>'_blank')); ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if($fatura->id_boleto_fk!=null) echo anchor('boleto/visualizar/'.$fatura->hash_boleto,'Visualizar Boleto',array('class'=>'btn btn-info btn-xs','target'=>'_blank'));
                                    else echo '-';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhuma fatura ADM encontrada neste mes.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel panel-google">
                <div class="panel-heading">Dados de Faturas</div>
                <table class="panel-table table-condensed table-hover no-margin table-bordered">
                    <thead>
                    <tr>
                        <th class="text-right">#</th>
                        <th class="text-right">ID Cliente</th>
                        <th>Nome</th>
                        <th class="text-right">Consumo</th>
                        <th class="text-right">Valor</th>
                        <th class="text-center">Fatura</th>
                        <th class="text-center">Boleto</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($faturas as $index => $fatura): ?>
                        <tr>
                            <td class="text-right"><?php echo $fatura->id_fatura; ?></td>
                            <td class="text-right"><?php echo $fatura->id_cliente_fk; ?></td>
                            <td><?php echo strtoupper($fatura->nome); ?></td>
                            <td class="text-right"><?php echo dinheiro($fatura->consumo); ?></td>
                            <td class="text-right"><?php echo dinheiro($fatura->valor); ?></td>
                            <td class="text-center">
                                <?php echo anchor('fatura/pdf/'.$fatura->id_fatura,'Abrir Fatura',array('class'=>'btn btn-default btn-xs','target'=>'_blank')); ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if($fatura->valor>0):
                                    if($fatura->id_boleto_fk!=null) echo anchor('boleto/visualizar/'.$fatura->hash_boleto,'Visualizar Boleto',array('class'=>'btn btn-info btn-xs','target'=>'_blank'));
                                    else echo anchor('fatura/gerar_boleto/'.$fatura->id_fatura,'Gerar Boleto');
                                else:
                                    echo 'Não Autorizado';
                                endif;
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case '':
        break;
endswitch;
