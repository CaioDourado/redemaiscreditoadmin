<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
	case 'remessa': ?>
			<div class="page-header pb">
				<div class="ph-titulo"> <i class="fa fa-download"></i> Remessas </div>
				<div class="ph-subtitulo"> Visualização e Download de Remessas </div>
			</div>
			<div class="p-15">
				<form action="<?php echo base_url(); ?>index.php/boleto/remessa" method="GET">
					<div class="panel panel-google">
						<div class="panel-heading">Filtro</div>
						<div class="panel-body border-top">
							<div class="row">
								<div class="col-md-3">
									<div class="input-group">
										<label for="data">Data</label>
										<input type="text" class="form-control text-center" name="data" value="<?php echo date('d/m/Y'); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="panel-footer text-right">
							<?php echo form_submit('submit','Pesquisar',array('class'=>'btn btn-success')) ?>
						</div>
					</div>
				</form>
				<?php echo form_open(current_url()); ?>
				<div class="text-right">
					<?php echo anchor('boleto','Voltar',array('class'=>'btn btn-default')); ?>
					<?php echo form_submit('submit','Download',array('class'=>'btn btn-default')); ?>
				</div>
				<br>
				<div class="panel panel-google">
					<div class="panel-heading">Remessas</div>
					<table class="table panel-table">
						<thead>
						<tr>
							<th class="text-right" style="width: 50px">
								<input type="checkbox" onclick="check_all(this)">
							</th>
							<th class="text-right" style="width: 80px">ID</th>
							<th>Sacado</th>
							<th class="text-right">Valor</th>
							<th class="text-right">Criação</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($boletos as $i => $b): ?>
							<tr>
								<td class="text-center"><input type="checkbox" value="<?php echo $b->id_boleto; ?>" name="id_boletos[]"></td>
								<td class="text-right"><?php echo $b->id_boleto ?></td>
								<td><?php echo $b->nome_sacado; ?></td>
								<td class="text-right"><?php echo dinheiro($b->valor_boleto); ?></td>
								<td class="text-right"><?php echo data_pt($b->criado_em); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php echo form_close(); ?>
			</div>
		<?php break;
    case 'geracao_em_massa': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Geração de Boletos em Massa</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="dia_vcto">Dia de Vencimento</label>
                        <select name="dia_vcto" class="form-control">
                            <option value="15">Dia 15</option>
                            <option value="30">Dia 30</option>
                        </select>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'gerenciar': ?>
            <div class="row">
                <?php foreach($boletos as $index => $boleto): ?>
                    <div class="col-md-4">
                        <a href="<?php echo base_url().'index.php/boleto/mes/'.$boleto->ano.'/'.$boleto->mes; ?>">
                            <div class="panel panel-google">
                                <div class="panel-heading text-center"><?php echo meses_array($boleto->mes); ?> de <?php echo $boleto->ano; ?></div>
                                <div class="panel-body border-top">
                                    <p class="no-margin text-danger">Vencidos <span style="float: right"><?php echo $boleto->vencidos; ?></span></p>
                                    <p class="no-margin text-info">A Pagar <span style="float: right"><?php echo $boleto->a_pagar; ?></span></p>
                                    <p class="no-margin text-success">Pagos <span style="float: right"><?php echo $boleto->pagos; ?></span></p>
                                    <p class="no-margin text-muted">Cancelados <span style="float: right"><?php echo $boleto->cancelados; ?></span></p>
                                </div>
                                <?php if($this->session->userdata('adm_nivel')>1): ?>
                                    <div class="panel-footer text-right">
                                        <small><b><?php echo dinheiro($boleto->valor_pago); ?></b> / <?php echo dinheiro($boleto->valor_total); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php break;
    case 'envio_por_email': ?>
            <div class="text-right" style="margin-bottom: 10px"><?php echo anchor('boleto','Voltar',array('class'=>'btn btn-default')); ?></div>
            <div class="btn btn-info btn-lg btn-block" id="bt_enviar_emails">Enviar E-mails</div>
            <br>
            <div class="panel panel-google">
                <div class="panel-heading">Envio de Boletos por E-mail</div>
					<div class="table-responsive">
						<table class="panel-table table-hover table-striped no-margin">
							<thead>
								<tr>
									<th class="text-center"><input type="checkbox" onclick="check_all(this)"></th>
									<th class="text-right">ID</th>
									<th class="text-right">NN</th>
									<th>Nome</th>
									<th class="text-right">E-mail</th>
									<th class="text-center">Vencimento</th>
									<th class="text-right">Valor</th>
									<th class="text-right"></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($boletos['apagar'] as $index => $boleto): ?>
									<tr>
										<td class="text-center"><input type="checkbox" class="checkbox_email" hash="<?php echo $boleto->hash; ?>"></td>
										<td class="text-right"><?php echo $boleto->id_boleto; ?></td>
										<td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
										<td><?php echo $boleto->nome_sacado; ?></td>
										<td class="text-right"><?php echo $boleto->email; ?></td>
										<td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
										<td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
										<td class="text-right">
											<span class="status_envio text-info">Aguardando</span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
            </div>
        <?php break;
    case 'mes': ?>
            <div class="panel panel-google panel-info">
                <div class="panel-heading">Boletos A Pagar</div>
                <div class="table-responsive">
                    <table class="panel-table table-hover table-condensed no-margin">
                        <thead>
                        <tr>
                            <th class="text-right">ID</th>
                            <th class="text-right">NN</th>
                            <th>Nome</th>
                            <th class="text-center">Vencimento</th>
                            <?php if($this->session->userdata('adm_nivel')>1): ?>
                                <th class="text-right">Valor</th>
                            <?php endif; ?>
                            <th class="text-center">Opções</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($boletos['apagar'] as $index => $boleto): ?>
                            <tr>
                                <td class="text-right"><?php echo $boleto->id_boleto; ?></td>
                                <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                <td><?php echo $boleto->nome; ?></td>
                                <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                <?php if($this->session->userdata('adm_nivel')>1): ?>
                                    <td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <?php echo anchor('boleto/visualizar/'.$boleto->hash,'<i class="fa fa-search"></i>',array('class'=>'btn btn-info btn-xs','target'=>'_blank')); ?>
                                    <?php echo anchor('boleto/enviar_email/'.$boleto->hash,'<i class="fa fa-envelope-square""></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-google panel-danger">
                <div class="panel-heading">Boletos Vencidos</div>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed no-margin">
                        <thead>
                            <tr>
                                <th class="text-right">ID</th>
                                <th class="text-right">NN</th>
                                <th>Nome</th>
                                <th class="text-center">Vencimento</th>
                                <?php if($this->session->userdata('adm_nivel')>1): ?>
                                    <th class="text-right">Valor</th>
                                <?php endif; ?>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($boletos['vencidos'] as $index => $boleto): ?>
                                <tr>
                                    <td class="text-right"><?php echo $boleto->id_boleto; ?></td>
                                    <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                    <td><?php echo $boleto->nome; ?></td>
                                    <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                    <?php if($this->session->userdata('adm_nivel')>1): ?>
                                        <td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <?php echo anchor('boleto/visualizar/'.$boleto->hash,'<i class="fa fa-search"></i>',array('class'=>'btn btn-info btn-xs','target'=>'_blank')); ?>
                                        <?php echo anchor('boleto/enviar_email/'.$boleto->hash,'<i class="fa fa-envelope-square""></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-google panel-success">
                <div class="panel-heading">Boletos Pagos</div>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed no-margin">
                        <thead>
                            <tr>
                                <th class="text-right">ID</th>
                                <th class="text-right">NN</th>
                                <th>Nome</th>
                                <th class="text-center">Vencimento</th>
                                <?php if($this->session->userdata('adm_nivel')>1): ?>
                                    <th class="text-right">Valor</th>
                                <?php endif; ?>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($boletos['pagos'] as $index => $boleto): ?>
                            <tr>
                                <td class="text-right"><?php echo $boleto->id_boleto; ?></td>
                                <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                <td><?php echo $boleto->nome; ?></td>
                                <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                <?php if($this->session->userdata('adm_nivel')>1): ?>
                                    <td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <?php echo anchor('boleto/visualizar/'.$boleto->hash,'<i class="fa fa-search"></i>',array('class'=>'btn btn-info btn-xs','target'=>'_blank')); ?>
                                    <?php echo anchor('boleto/enviar_email/'.$boleto->hash,'<i class="fa fa-envelope-square""></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'geral': ?>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <?php echo anchor('boleto/baixar_remessa_hoje','Baixar Remessa',array('class'=>'btn btn-info btn-block')); ?>
                </div>
            </div>
            <br>
            <table class="table table-condensed table-hover no-margin table-bordered">
                <thead>
                <tr>
                    <th class="text-right">NN</th>
                    <th class="text-center">Geração</th>
                    <th class="text-center">Vencimento</th>
                    <th>Cliente</th>
                    <th class="text-center">Pagamento</th>
                    <th class="text-center">Valor</th>
                    <th class="text-center">Opções</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($boletos as $index => $boleto): ?>
                    <tr>
                        <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                        <td class="text-center"><?php echo data_pt($boleto->criado_em,false); ?></td>
                        <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                        <td><?php echo strtoupper(!empty($boleto->nome_sacado) ? $boleto->nome_sacado : (isset($boleto->nome) ? $boleto->nome : '')); ?></td>
                        <td class="text-center"><?php if($boleto->data_pagamento!="0000-00-00") echo $boleto->data_pagamento; ?></td>
                        <td class="text-center"><?php echo $boleto->valor_boleto; ?></td>
                        <td class="text-center">
                            <?php echo anchor('boleto/visualizar/'.$boleto->hash,'<i class="fa fa-search"></i>',array('class'=>'btn btn-info btn-xs','target'=>'_blank')); ?>
                            <?php echo anchor('boleto/enviar_email/'.$boleto->hash,'<i class="fa fa-envelope-square""></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php break;
    case 'cadastrar': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Cadastrar Boleto</div>
                <div class="panel-body border-top">
                    <?php echo form_select('id_cliente_fk','Cliente',$clientes); ?>
                    <?php echo form_input('boleto_descricao','Descrição'); ?>
                    <div class="row">
                        <div class="col-md-4"><?php echo form_input('data_vencimento','Data Vencimento','','data text-center'); ?></div>
                        <div class="col-md-4"><?php echo form_input('valor_boleto','Valor','0,00','dinheiro text-right'); ?></div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('boleto','Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'retorno': ?>
            <?php if($retorno!=null): ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados de Retorno</div>
                    <table class="panel-table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="text-right">ID</th>
                                <th class="text-right">NN</th>
                                <th>CPF/CNPJ</th>
                                <th>Nome</th>
                                <th class="text-center">Vencimento</th>
                                <th class="text-center">Pagamento</th>
                                <th class="text-right">Valor</th>
                                <th class="text-right">Valor Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($retorno as $index => $boleto): ?>
                                <tr>
                                    <td class="text-right"><?php echo $boleto->id; ?></td>
                                    <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                    <td><?php echo $boleto->cpf_cnpj; ?></td>
                                    <td><?php echo $boleto->nome; ?></td>
                                    <td class="text-center"><?php echo data_pt($boleto->vencimento,false); ?></td>
                                    <td class="text-center"><?php echo data_pt($boleto->data_ocorrencia,false); ?></td>
                                    <td class="text-right"><?php echo dinheiro($boleto->valor); ?></td>
                                    <?php if($boleto->valor_pago>0): ?>
                                        <td class="text-right"><?php echo dinheiro($boleto->valor_pago); ?></td>
                                    <?php else: ?>
                                        <td class="text-right text-danger">Baixado</td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="panel-footer text-right">
                        <?php echo anchor('boleto/retorno','Voltar',array('class'=>'btn btn-default')); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo form_open(current_url(),array('enctype'=>'multipart/form-data')); ?>
                    <div class="panel panel-google">
                        <div class="panel-heading">Enviar Arquivo de Retorno</div>
                        <div class="panel-body border-top">
                            <div class="form-group">
                                <label for="arquivo">Escolha um arquivo para Envio</label>
                                <input type="file" class="form-control" name="arquivo">
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <?php echo form_hidden('confirm','1'); ?>
                            <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            <?php endif; ?>
        <?php break;
    case 'pagos': ?>
            <div class="row">
                <div class="col-md-9">
                    <?php foreach($dias as $index => $dia): ?>
                        <div class="panel panel-google">
                            <div class="panel-heading"><?php echo data_pt(str_replace('_','-',$index),false); ?></div>
                            <div class="table-responsive">
                                <table class="table panel-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Nome</th>
                                            <th class="text-center">Vencimento</th>
                                            <th class="text-center">Pagamento</th>
                                            <th class="text-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($dia['boletos'] as $indice => $boleto): ?>
                                            <tr>
                                                <td class="text-right"><?php echo $indice+1; ?></td>
                                                <td><?php echo $boleto->nome_sacado; ?></td>
                                                <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                                <td class="text-center"><?php echo data_pt($boleto->data_pagamento,false); ?></td>
                                                <td class="text-right"><?php echo dinheiro($boleto->valor_pago); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-google">
                        <div class="panel-heading">Resumo por Mês</div>
                        <div class="table-responsive">
                            <table class="table panel-table">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-right">Qtd.</th>
                                        <th class="text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($meses as $mes => $item): ?>
                                        <tr>
                                            <td><?php echo str_replace('_','/',$mes); ?></td>
                                            <td class="text-right"><?php echo $item['qtd']; ?></td>
                                            <td class="text-right"><?php echo dinheiro($item['valor']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-google">
                        <div class="panel-heading">Resumo por dia</div>
                        <div class="table-responsive">
                            <table class="table panel-table">
                                <thead>
                                    <tr><th>Dia</th><th class="text-right">Valor</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach($dias as $index => $dia): ?>
                                    <tr>
                                        <td><?php echo data_pt(str_replace('_','-',$index),false); ?></td>
                                        <td class="text-right"><?php echo dinheiro($dia['total']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php break;
    case 'baixar': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Baixa de Boleto</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Pagador</label>
                                <input type="text" class="form-control" disabled value="<?php echo $boleto->nome_sacado; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Valor</label>
                                <input type="text" class="form-control text-right" disabled value="<?php echo dinheiro($boleto->valor_boleto); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Data Pagamento</label>
                                <input type="text" class="form-control text-center" value="<?php echo date('d/m/Y'); ?>" name="data_pagamento">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Valor Pago</label>
                                <input type="text" class="form-control text-right" value="<?php echo dinheiro($boleto->valor_boleto); ?>" name="valor_pago">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #CCC">
                    <div class="form-group">
                        <label for="senha">Senha Gerencial</label>
                        <input type="password" class="form-control text-center" name="senha">
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente/perfil/'.$boleto->id_cliente_fk,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
endswitch;
