<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Criação de Código</div>
                <div class="panel-body border-top">
                    <?php echo anchor('vault/gerar_codigo','Gerar Código',array('class'=>'btn btn-default btn-block')); ?>
                </div>
            </div>
            <div class="panel panel-google">
                <div class="panel-heading">Histórico de Códigos</div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hash</th>
                            <th class="text-right">Data</th>
                            <th class="text-right">Criação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($hashs as $i => $h): ?>
                            <tr>
                                <td><?php echo $h->id_vault; ?></td>
                                <td><?php echo $h->hash; ?></td>
                                <td class="text-right"><?php echo data_pt($h->data,false); ?></td>
                                <td class="text-right"><?php echo data_pt($h->criado_em,true); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
endswitch;