<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
	case 'gerenciar': ?>
			<div class="panel panel-google">
				<div class="panel-heading">Consultas no Portifólio</div>
				<div class="table-responsive">
					<table class="panel-table table-striped table-hover tabela_pesquisavel">
						<thead>
							<tr>
								<th></th>
								<th>ID</th>
								<th>Grupo</th>
								<th>Tipo</th>
								<th>Nome</th>
								<th>Slug</th>
								<th>Entrada</th>
								<th>Valor</th>
								<th class="text-right">Opções</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($consultas as $i => $c): ?>
								<tr>
									<td><?php echo $i+1; ?></td>
									<td><?php echo $c->id_portifolio; ?></td>
									<td><?php echo $c->categoria; ?></td>
									<td><?php echo $c->tipo; ?></td>
									<td><?php echo $c->nome; ?></td>
									<td><?php echo $c->slug; ?></td>
									<td><?php echo $c->input; ?></td>
									<td><?php echo $c->valor; ?></td>
									<td class="text-right"><?php echo anchor('portifolio/alterar/'.$c->id_portifolio,'Alterar'); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php break;
	case 'alterar': ?>
			<?php echo form_open(current_url()); ?>
				<div class="panel panel-google">
					<div class="panel-heading">Alteração de Consulta de Portifólio</div>
					<div class="panel-body border-top">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="status">Status</label>
									<select name="status" class="form-control">
										<option <?php if($consulta->status==="1"): echo 'SELECTED'; endif; ?> value="1">Ativo</option>
										<option <?php if($consulta->status==="2"): echo 'SELECTED'; endif; ?> value="2">Manutenção</option>
										<option <?php if($consulta->status==="0"): echo 'SELECTED'; endif; ?> value="0">Inativo</option>
									</select>
								</div>
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="franquia_status">Status p/ Franquia</label>
									<select name="franquia_status" class="form-control">
										<option <?php if($consulta->franquia_status==="1"): echo 'SELECTED'; endif; ?> value="1">Ativo</option>
										<option <?php if($consulta->franquia_status==="2"): echo 'SELECTED'; endif; ?> value="2">Manutenção</option>
										<option <?php if($consulta->franquia_status==="0"): echo 'SELECTED'; endif; ?> value="0">Inativo</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="franquia_valor">Valor p/ Franquia</label>
									<input type="text" class="form-control dinheiro text-right" name="franquia_valor" value="<?=$consulta->franquia_valor?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="nome">Nome</label>
									<input type="text" class="form-control" name="nome" value="<?=$consulta->nome?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="categoria">Categoria</label>
									<select name="categoria" class="form-control">
										<option <?php if($consulta->categoria==="credito"): echo 'SELECTED'; endif; ?> value="credito">Crédito</option>
										<option <?php if($consulta->categoria==="cadastro"): echo 'SELECTED'; endif; ?> value="cadastro">Cadastral</option>
										<option <?php if($consulta->categoria==="veiculo"): echo 'SELECTED'; endif; ?> value="veiculo">Veicular</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="tipo">Tipo Pessoa</label>
									<select name="tipo" class="form-control">
										<option <?php if($consulta->tipo==="pf"): echo 'SELECTED'; endif; ?> value="pf">Pessoa Física [PF]</option>
										<option <?php if($consulta->tipo==="pj"): echo 'SELECTED'; endif; ?> value="pj">Pessoa Jurídica [PJ]</option>
										<option <?php if($consulta->tipo===""): echo 'SELECTED'; endif; ?> value="">Não Definido</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="slug">Slug</label>
									<input type="text" class="form-control" name="slug" value="<?=$consulta->slug?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="template">Template</label>
									<input type="text" class="form-control" name="template" value="<?=$consulta->template?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="view">View</label>
									<input type="text" class="form-control" name="view" value="<?=$consulta->view?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="input">Input</label>
									<input type="text" class="form-control" name="input" value="<?=$consulta->input?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="input_form">Input Form</label>
									<input type="text" class="form-control" name="input_form" value="<?=$consulta->input_form?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="descricao">Descrição</label>
									<textarea name="descricao" rows="4" class="form-control"><?=$consulta->descricao?></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="resumo">Resumo</label>
									<textarea name="resumo" rows="4" class="form-control"><?=$consulta->resumo?></textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="montagem">Montagem</label>
									<select name="montagem[]" multiple class="form-control multiselect">
										<?php foreach ($slugs as $i => $slug):?>
											<option value="<?=$slug->slug?>" <?php if(in_array($slug->slug, $montagem)): echo "SELECTED"; endif; ?> ><?=$slug->slug?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="auxiliares">Auxiliares</label>
									<select name="auxiliares[]" multiple class="form-control multiselect">
										<?php foreach ($slugs as $i => $slug):?>
											<option value="<?=$slug->slug?>" <?php if(in_array($slug->slug, $auxiliares)): echo "SELECTED"; endif; ?> ><?=$slug->slug?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="valor">Valor</label>
									<input type="text" name="valor" class="form-control text-right dinheiro" value="<?=$consulta->valor?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="valor_ge">Valor GE</label>
									<input type="text" name="valor_ge" class="form-control text-right dinheiro" value="<?=$consulta->valor_ge?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="valor_prepago">Valor Pré Pago</label>
									<input type="text" name="valor_prepago" class="form-control text-right dinheiro" value="<?=$consulta->valor_prepago?>">
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer text-right">
						<?=anchor('portifolio','Voltar',array('class'=>'btn btn-default'))?>
						<?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')) ?>
					</div>
				</div>
			<?php echo form_close(); ?>
		<?php break;
	default:
		break;
endswitch;
