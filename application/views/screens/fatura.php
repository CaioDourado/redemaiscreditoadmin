<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
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
