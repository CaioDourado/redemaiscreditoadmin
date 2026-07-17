<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
            <?php $banimento_ativo = isset($banimento_fornecedores_ativo) ? (bool) $banimento_fornecedores_ativo : true; ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Banimento automatico de fornecedores</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="no-margin">
                                Status:
                                <?php if($banimento_ativo): ?>
                                    <span class="label label-success">LIGADO</span>
                                <?php else: ?>
                                    <span class="label label-default">DESLIGADO</span>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">
                                Em modo de teste no administrativo. O sistema de consultas ainda nao consome esta configuracao.
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            <?php echo form_open('consulta/alternar_banimento_fornecedores', array('style'=>'display:inline')); ?>
                                <?php if($banimento_ativo): ?>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa fa-toggle-on"></i> Desligar banimento
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-toggle-off"></i> Ligar banimento
                                    </button>
                                <?php endif; ?>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-condensed table-hover no-margin table-bordered">
                <thead>
                <tr>
                    <th class="text-right">#</th>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Venda</th>
                    <th class="text-right">Venda Pré</th>
                    <th class="text-right">Venda GE</th>
                    <!--th class="text-center">Criação</th-->
					<th class="text-right">Franquia</th>
                    <th class="text-center">Opções</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($consultas as $index => $consulta): ?>
                    <tr>
                        <td class="text-right"><?php echo $index+1 ?></td>
                        <td><?php echo $consulta->nome; ?></td>
                        <td><?php echo $consulta->slug; ?></td>
                        <td class="text-center"><?php echo ativo_inativo($consulta->status); ?></td>
                        <td class="text-right"><?php echo dinheiro($consulta->venda); ?></td>
                        <td class="text-right"><?php echo dinheiro($consulta->venda_pre); ?></td>
                        <td class="text-right"><?php echo dinheiro($consulta->venda_ge); ?></td>
                        <!--td class="text-center"><?php echo data_pt($consulta->criado_em,false) ; ?></td-->
						<td class="text-right">
							<?php if($consulta->franquia_check==="1"): ?>
								Ativo - <?php echo dinheiro($consulta->franquia); ?>
							<?php else: ?>
								Invativo
							<?php endif; ?>
						</td>
                        <td class="text-center">
                            <?php echo anchor('consulta/alterar/'.$consulta->id_consulta,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                            <?php //echo anchor('consulta/excluir/'.$consulta->id_consulta,'<i class="fa fa-close"></i>',array('class'=>'btn btn-danger btn-xs')); ?>
							<?php
								if($consulta->status==1):
									echo anchor('consulta/bloquear/'.$consulta->id_consulta,'<i class="fa fa-ban"></i>',array('class'=>'btn btn-danger btn-xs'));
								else:
									echo anchor('consulta/bloquear/'.$consulta->id_consulta,'<i class="fa fa-repeat"></i>',array('class'=>'btn btn-default btn-xs'));
								endif;
							?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php break;
    case 'cadastrar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Cadastrar Consulta</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4"><?php echo form_input('nome','Nome',''); ?></div>
                            <div class="col-md-4"><?php echo form_input('slug','Slug',''); ?></div>
                            <div class="col-md-4"><?php echo form_select('id_grupo_consulta_fk','Grupo',$grupos); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><?php echo form_input('ordem','Órdem'); ?></div>
                            <div class="col-md-4"><?php echo form_input('icone','Ícone'); ?></div>
                            <div class="col-md-4"><?php echo form_select('status','Status',array('1'=>'Ativo','0'=>'Inativo')); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><?php echo form_input('venda','Venda','0,00','dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                            <div class="col-md-3"><?php echo form_input('venda_pre','Venda Pré','0,00','dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                            <div class="col-md-3"><?php echo form_input('qtd_ge','Qtd GE','0','text-right') ?></div>
                            <div class="col-md-3"><?php echo form_input('venda_ge','Venda GE','0,00','dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                        </div>
						<div class="row">
							<div class="col-md-6"></div>
							<div class="col-md-3">
								<?php echo form_select('franquia_check','Status Franquia',array('1'=>'Ativo','0'=>'Inativo'),''); ?>
							</div>
							<div class="col-md-3"><?php echo form_input('franquia','Franquia Valor','','dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
						</div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="descricao">Descrição (Irá Aparecer para o Cliente Visualizar)</label>
                                <textarea name="descricao" rows="4" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('consulta','Cancelar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','salvar',array('class'=>'btn btn-success')) ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'alterar': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Cadastrar Consulta</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4"><?php echo form_input('nome','Nome',$consulta->nome); ?></div>
                        <div class="col-md-4"><?php echo form_input('slug','Slug',$consulta->slug); ?></div>
                        <div class="col-md-4"><?php echo form_select('id_grupo_consulta_fk','Grupo',$grupos,'',$consulta->id_grupo_consulta_fk); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><?php echo form_input('ordem','Órdem',$consulta->ordem); ?></div>
                        <div class="col-md-4"><?php echo form_input('icone','Ícone',$consulta->icone); ?></div>
                        <div class="col-md-4"><?php echo form_select('status','Status',array('1'=>'Ativo','0'=>'Inativo'),'',$consulta->status); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('venda','Venda',dinheiro($consulta->venda),'dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                        <div class="col-md-3"><?php echo form_input('venda_pre','Venda Pré',dinheiro($consulta->venda_pre),'dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                        <div class="col-md-3"><?php echo form_input('qtd_ge','Qtd GE',$consulta->qtd_ge,'text-right') ?></div>
                        <div class="col-md-3"><?php echo form_input('venda_ge','Venda GE',dinheiro($consulta->venda_ge),'dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
                    </div>
					<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-3">
							<?php echo form_select('franquia_check','Status Franquia',array('1'=>'Ativo','0'=>'Inativo'),'',$consulta->franquia_check); ?>
						</div>
						<div class="col-md-3"><?php echo form_input('franquia','Franquia Valor',dinheiro($consulta->franquia),'dinheiro text-right','<i class="fa fa-money"></i>') ?></div>
					</div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="descricao">Descrição (Irá Aparecer para o Cliente Visualizar)</label>
                            <textarea name="descricao" rows="4" class="form-control"><?php echo $consulta->descricao; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('consulta','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','salvar',array('class'=>'btn btn-success')) ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
endswitch;
