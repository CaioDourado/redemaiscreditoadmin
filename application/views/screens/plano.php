<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
            <table class="table table-condensed table-hover no-margin table-bordered">
                <thead>
                    <tr>
                        <th class="text-right">#</th>
                        <th>Nome</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Mensalidade</th>
                        <th class="text-right">Negativação</th>
                        <th class="text-right">Tarifa Banco</th>
                        <th class="text-center">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($planos as $index => $plano): ?>
                        <tr>
                            <td class="text-right"><?php echo $index+1 ?></td>
                            <td><?php echo $plano->nome; ?></td>
                            <td class="text-center"><?php echo ativo_inativo($plano->status); ?></td>
                            <td class="text-right"><?php echo dinheiro($plano->mensalidade); ?></td>
                            <td class="text-right"><?php echo dinheiro($plano->negativacao); ?></td>
                            <td class="text-right"><?php echo dinheiro($plano->tarifa_bancaria); ?></td>
                            <td class="text-center">
                                <?php echo anchor('plano/perfil/'.$plano->id_plano,'<i class="fa fa-user"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                <?php echo anchor('plano/alterar/'.$plano->id_plano,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                <?php echo anchor('plano/excluir/'.$plano->id_plano,'<i class="fa fa-close"></i>',array('class'=>'btn btn-danger btn-xs')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php break;
    case 'cadastrar': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Cadastrar Plano</div>
                <div class="panel-body">
                    <?php echo form_open(current_url()); ?>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('nome','Nome','') ?></div>
                        <div class="col-md-3"><?php echo form_select('status','status',array('1'=>'Ativo','0'=>'Inativo')); ?></div>
                        <div class="col-md-3"><?php echo form_select('tipo','Tipo',$tipos); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('mensalidade','Mensalidade','0,00','text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('licenca','Licença','0,00','text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('negativacao','Negativação','0,00','text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('tarifa_bancaria','Tarifa Bancaria','0,00','text-right'); ?></div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('plano','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>

        <?php break;
    case '':
        break;
endswitch;
