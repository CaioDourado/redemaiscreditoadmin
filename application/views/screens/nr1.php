<?php defined('BASEPATH') OR exit('No direct script access allowed');

function nr1_e($valor){
    return html_escape((string) $valor);
}

switch($content):
    case 'index': ?>
        <style>
            .nr1-kpi-card { position:relative; min-height:116px; margin-bottom:18px; padding:19px 20px; overflow:hidden; background:#fff; border:1px solid #e2e8ed; border-top:4px solid #1f4c6d; border-radius:4px; box-shadow:0 3px 10px rgba(33,55,72,.10); }
            .nr1-kpi-card:after { position:absolute; top:0; right:0; width:76px; height:100%; content:""; opacity:.5; background:linear-gradient(135deg, transparent 0, transparent 45%, #f4f7f8 46%, #f4f7f8 100%); }
            .nr1-kpi-card .nr1-kpi-label { display:block; margin-bottom:8px; color:#667783; font-size:11px; font-weight:700; line-height:1.2; letter-spacing:.25px; text-transform:uppercase; }
            .nr1-kpi-card .nr1-kpi-value { color:#183c56; font-size:30px; font-weight:700; line-height:1; }
            .nr1-kpi-card .nr1-kpi-icon { position:absolute; z-index:1; top:18px; right:17px; display:flex; width:38px; height:38px; align-items:center; justify-content:center; border-radius:50%; background:#eaf1f5; color:#1f4c6d; font-size:17px; }
            .nr1-kpi-matriz { border-top-color:#04a66d; }
            .nr1-kpi-matriz .nr1-kpi-value, .nr1-kpi-matriz .nr1-kpi-icon { color:#078758; }
            .nr1-kpi-matriz .nr1-kpi-icon { background:#e3f6ee; }
            .nr1-kpi-franquia { border-top-color:#42b9dc; }
            .nr1-kpi-franquia .nr1-kpi-value, .nr1-kpi-franquia .nr1-kpi-icon { color:#2585a5; }
            .nr1-kpi-franquia .nr1-kpi-icon { background:#e7f7fc; }
            .nr1-kpi-solicitacoes { border-top-color:#f0a018; }
            .nr1-kpi-solicitacoes .nr1-kpi-value, .nr1-kpi-solicitacoes .nr1-kpi-icon { color:#ba7707; }
            .nr1-kpi-solicitacoes .nr1-kpi-icon { background:#fff4df; }
        </style>
        <div class="row">
            <div class="col-md-3"><div class="nr1-kpi-card"><span class="nr1-kpi-icon"><i class="fa fa-users"></i></span><span class="nr1-kpi-label">Clientes com NR-1 ativo</span><strong class="nr1-kpi-value"><?php echo $resumo['total']; ?></strong></div></div>
            <div class="col-md-3"><div class="nr1-kpi-card nr1-kpi-matriz"><span class="nr1-kpi-icon"><i class="fa fa-building"></i></span><span class="nr1-kpi-label">Clientes da matriz</span><strong class="nr1-kpi-value"><?php echo $resumo['matriz']; ?></strong></div></div>
            <div class="col-md-3"><div class="nr1-kpi-card nr1-kpi-franquia"><span class="nr1-kpi-icon"><i class="fa fa-industry"></i></span><span class="nr1-kpi-label">Clientes de franquias</span><strong class="nr1-kpi-value"><?php echo $resumo['franquia']; ?></strong></div></div>
            <div class="col-md-3"><div class="nr1-kpi-card nr1-kpi-solicitacoes"><span class="nr1-kpi-icon"><i class="fa fa-file-text-o"></i></span><span class="nr1-kpi-label">Solicitações registradas</span><strong class="nr1-kpi-value"><?php echo $resumo['solicitacoes']; ?></strong></div></div>
        </div>

        <form method="get" action="<?php echo site_url('nr1'); ?>">
            <div class="panel panel-google">
                <div class="panel-heading">Filtros</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Origem do cliente</label>
                            <select name="origem" class="form-control">
                                <option value="">Matriz e franquias</option>
                                <option value="matriz" <?php if($origem === 'matriz') echo 'selected'; ?>>Somente matriz</option>
                                <option value="franquia" <?php if($origem === 'franquia') echo 'selected'; ?>>Somente franquias</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('nr1', 'Limpar', array('class' => 'btn btn-default')); ?>
                    <button type="submit" class="btn btn-success">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="panel panel-google">
            <div class="panel-heading">Clientes NR-1</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Origem</th>
                        <th>Contato</th>
                        <th>Solicitação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(count($clientes) === 0): ?>
                        <tr><td colspan="6" class="text-center text-muted">Nenhum cliente com NR-1 ativo para este filtro.</td></tr>
                    <?php endif; ?>
                    <?php foreach($clientes as $cliente): ?>
                        <tr>
                            <td><b><?php echo nr1_e($cliente->nome_ou_fantasia); ?></b><?php if($cliente->razao_social): ?><br><small><?php echo nr1_e($cliente->razao_social); ?></small><?php endif; ?></td>
                            <td><?php echo nr1_e($cliente->cpf_cnpj); ?></td>
                            <td>
                                <?php if((int) $cliente->id_franquia_fk > 0): ?>
                                    <span class="label label-info">Franquia</span><br><small><?php echo nr1_e($cliente->franquia_nome ? $cliente->franquia_nome : $cliente->franquia_razao_social); ?></small>
                                <?php else: ?>
                                    <span class="label label-success">Matriz</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo nr1_e($cliente->email); ?><br><small><?php echo nr1_e($cliente->celular ? $cliente->celular : $cliente->telefone); ?></small></td>
                            <td>
                                <?php if($cliente->id_nr1_ivi_empresa !== null): ?>
                                    <span class="label label-primary"><?php echo nr1_e($status_labels[$cliente->solicitacao_status]); ?></span><br>
                                    <small><?php echo (int) $cliente->quantidade_vidas; ?> vidas<?php if($cliente->valor_total !== null): ?> · R$ <?php echo number_format($cliente->valor_total, 2, ',', '.'); ?><?php endif; ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Sem solicitação registrada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo anchor('nr1/editar/'.$cliente->id_cliente, '<i class="fa fa-pencil"></i> Editar', array('class' => 'btn btn-primary btn-sm')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php break;
    case 'editar': ?>
        <form method="post" action="<?php echo site_url('nr1/editar/'.$cliente->id_cliente); ?>">
            <div class="panel panel-google">
                <div class="panel-heading">Cliente e disponibilização do NR-1</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Nome / Fantasia</label><input type="text" name="nome_ou_fantasia" class="form-control" value="<?php echo nr1_e(set_value('nome_ou_fantasia', $cliente->nome_ou_fantasia)); ?>"><?php echo form_error('nome_ou_fantasia'); ?></div></div>
                        <div class="col-md-4"><div class="form-group"><label>E-mail</label><input type="email" name="email" class="form-control" value="<?php echo nr1_e(set_value('email', $cliente->email)); ?>"><?php echo form_error('email'); ?></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo nr1_e(set_value('telefone', $cliente->telefone)); ?>"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Celular</label><input type="text" name="celular" class="form-control" value="<?php echo nr1_e(set_value('celular', $cliente->celular)); ?>"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p><b>Documento:</b> <?php echo nr1_e($cliente->cpf_cnpj); ?></p></div>
                        <div class="col-md-4"><p><b>Origem:</b> <?php echo (int) $cliente->id_franquia_fk > 0 ? 'Franquia — '.nr1_e($cliente->franquia_nome ? $cliente->franquia_nome : $cliente->franquia_razao_social) : 'Matriz'; ?></p></div>
                        <div class="col-md-4"><div class="checkbox"><label><input type="checkbox" name="nr1" value="1" <?php if(set_value('nr1', $cliente->nr1)) echo 'checked'; ?>> Manter card NR-1 disponível para este cliente</label></div></div>
                    </div>
                </div>
            </div>

            <?php if($cliente->id_nr1_ivi_empresa !== null): ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Andamento da solicitação</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3"><p><b>Empresa:</b><br><?php echo nr1_e($cliente->empresa_nome); ?></p></div>
                            <div class="col-md-2"><p><b>Vidas:</b><br><?php echo (int) $cliente->quantidade_vidas; ?></p></div>
                            <div class="col-md-2"><p><b>Valor total:</b><br>R$ <?php echo number_format($cliente->valor_total, 2, ',', '.'); ?></p></div>
                            <div class="col-md-2"><p><b>Solicitada em:</b><br><?php echo data_pt($cliente->solicitado_em); ?></p></div>
                            <div class="col-md-3"><div class="form-group"><label>Status</label><select name="status" class="form-control"><?php foreach($status_labels as $valor => $rotulo): ?><option value="<?php echo $valor; ?>" <?php if($cliente->status === $valor) echo 'selected'; ?>><?php echo $rotulo; ?></option><?php endforeach; ?></select></div></div>
                        </div>
                        <div class="form-group"><label>Observação</label><textarea name="observacao" rows="4" maxlength="2000" class="form-control"><?php echo nr1_e($cliente->observacao); ?></textarea></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info"><i class="fa fa-info-circle"></i> Este cliente tem o NR-1 habilitado, mas ainda não há solicitação de implantação registrada.</div>
            <?php endif; ?>

            <div class="text-right">
                <?php echo anchor('nr1', 'Cancelar', array('class' => 'btn btn-default')); ?>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar alterações</button>
            </div>
        </form>
        <?php break;
endswitch;
