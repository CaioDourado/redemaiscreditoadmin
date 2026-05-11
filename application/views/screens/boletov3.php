<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
switch ($tela):
    case 'retorno': ?>
            <h1>Tela de Retorno</h1>
            <form action="<?php echo base_url()."index.php/boletoV3/retorno_req" ?>" method="GET">
                <input type="hidden" name="debug" value="1">
                <div class="panel panel-google">
                    <div class="panel-heading">Requisitar Retorno</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fim">Fim (a requisição sempre é feita de acordo com D-1)</label>
                                    <input type="text" class="form-control" name="fim" value="<?php echo date("d/m/Y") ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo form_submit("submit", "Pedir Retorno", array("class"=>"btn btn-success")); ?>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-google">
                        <div class="panel-heading border-top">Últimas Requisições</div>
						<table class="panel-table">
							<thead>
							<tr>
								<th>ID</th>
								<th>Solicitação</th>
								<th>Status</th>
								<th>Inicio</th>
								<th>Fim</th>
								<th>Criado em</th>
								<th class="text-right">Opções</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach($reqs as $i => $req): ?>
								<tr>
									<td><?php echo $req->id_retorno_req; ?></td>
									<td><?php echo $req->id_solicitacao; ?></td>
									<td><?php echo $req->status; ?></td>
									<td><?php echo data_pt($req->inicio, false); ?></td>
									<td><?php echo data_pt($req->fim, false); ?></td>
									<td><?php echo data_pt($req->criado_em, true); ?></td>
									<td class="text-right">
										<?php echo anchor('boletoV3/check_retorno/'.$req->id_solicitacao.'?debug=1','Verificar'); ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-google">
                        <div class="panel-heading">Últimos Boletos Liquidados</div>
						<table class="panel-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nome</th>
									<th>Valor</th>
									<th>Vencimento</th>
									<th>Pagamento</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($boletos as $i => $boleto): ?>
									<tr>
										<td><?php echo $boleto->id_boleto; ?></td>
										<td><?php echo $boleto->nome_sacado; ?></td>
										<td><?php echo dinheiro($boleto->valor_boleto); ?></td>
										<td><?php echo data_pt($boleto->data_vencimento,false); ?></td>
										<td><?php echo data_pt($boleto->data_pagamento,false); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
                    </div>
                </div>
            </div>

        <?php
    break;
endswitch;
