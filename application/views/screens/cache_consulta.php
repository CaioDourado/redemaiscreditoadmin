<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index':
        $total = count($caches);
        $ativos = 0;
        $expirados = 0;
        $usos = 0;
        foreach($caches as $cache){
            if(strtotime($cache->expira_em)>=time()) $ativos++; else $expirados++;
            $usos += (int) $cache->usado_qtd;
        }
?>
        <form method="get" action="<?php echo site_url('cache_consulta'); ?>">
            <div class="panel panel-google">
                <div class="panel-heading">Pesquisar documento</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group" style="margin-bottom:0">
                                <label for="cache-documento">CPF ou CNPJ</label>
                                <input type="text" class="form-control" id="cache-documento" name="documento"
                                    value="<?php echo html_escape($documento_informado); ?>"
                                    placeholder="Digite o CPF ou CNPJ" maxlength="18" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-4" style="padding-top:25px">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Pesquisar caches</button>
                            <?php echo anchor('cache_consulta', 'Limpar', array('class'=>'btn btn-default')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if($erro!==null): ?>
            <div class="alert alert-warning"><?php echo html_escape($erro); ?></div>
        <?php endif; ?>

        <?php if($documento!=='' && $erro===null): ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">Documento</div>
                        <div class="panel-body"><strong><?php echo strtoupper(html_escape($tipo_documento)); ?></strong><br><?php echo html_escape($documento); ?></div>
                    </div>
                </div>
                <div class="col-sm-2"><div class="panel panel-info"><div class="panel-heading">Total</div><div class="panel-body"><strong><?php echo $total; ?></strong></div></div></div>
                <div class="col-sm-2"><div class="panel panel-success"><div class="panel-heading">Validos</div><div class="panel-body"><strong><?php echo $ativos; ?></strong></div></div></div>
                <div class="col-sm-2"><div class="panel panel-warning"><div class="panel-heading">Expirados</div><div class="panel-body"><strong><?php echo $expirados; ?></strong></div></div></div>
                <div class="col-sm-2"><div class="panel panel-default"><div class="panel-heading">Usos</div><div class="panel-body"><strong><?php echo $usos; ?></strong></div></div></div>
            </div>

            <?php if($total>0): ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Caches encontrados</div>
                    <div class="table-responsive">
                        <table class="panel-table table-hover table-striped no-margin">
                            <thead>
                            <tr>
                                <th class="text-right">#</th>
                                <th>Consulta</th>
                                <th>Fornecedor</th>
                                <th class="text-center">HTTP</th>
                                <th class="text-right">Usos</th>
                                <th class="text-center">Criado</th>
                                <th class="text-center">Renovado</th>
                                <th class="text-center">Ultimo uso</th>
                                <th class="text-center">Expira</th>
                                <th class="text-center">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($caches as $cache): ?>
                                <?php $valido = strtotime($cache->expira_em)>=time(); ?>
                                <tr>
                                    <td class="text-right"><?php echo (int) $cache->id_consulta_cache; ?></td>
                                    <td>
                                        <strong><?php echo html_escape($cache->consulta_nome!==null ? $cache->consulta_nome : $cache->slug); ?></strong><br>
                                        <small><?php echo html_escape($cache->slug); ?></small>
                                    </td>
                                    <td><?php echo html_escape($cache->fornecedor); ?></td>
                                    <td class="text-center"><?php echo $cache->status_http!==null ? (int) $cache->status_http : '-'; ?></td>
                                    <td class="text-right"><?php echo (int) $cache->usado_qtd; ?></td>
                                    <td class="text-center"><?php echo $cache->criado_em ? data_pt($cache->criado_em, true) : '-'; ?></td>
                                    <td class="text-center"><?php echo $cache->atualizado_em ? data_pt($cache->atualizado_em, true) : '-'; ?></td>
                                    <td class="text-center"><?php echo $cache->ultimo_uso_em ? data_pt($cache->ultimo_uso_em, true) : '-'; ?></td>
                                    <td class="text-center"><?php echo $cache->expira_em ? data_pt($cache->expira_em, true) : '-'; ?></td>
                                    <td class="text-center"><span class="label label-<?php echo $valido ? 'success' : 'warning'; ?>"><?php echo $valido ? 'VALIDO' : 'EXPIRADO'; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer text-right">
                        <p class="text-muted pull-left" style="margin:7px 0 0">A exclusao remove somente o cache. Historico e cobrancas permanecem intactos.</p>
                        <form method="post" action="<?php echo site_url('cache_consulta/excluir_documento'); ?>" style="display:inline"
                            onsubmit="return confirm('Excluir todos os <?php echo $total; ?> caches deste documento? As proximas consultas chamarao os fornecedores novamente.');">
                            <input type="hidden" name="csrf_token" value="<?php echo html_escape($csrf_token); ?>">
                            <input type="hidden" name="documento" value="<?php echo html_escape($documento); ?>">
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Excluir todos os caches</button>
                        </form>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Nenhum cache encontrado para este documento.</div>
            <?php endif; ?>
        <?php endif; ?>
<?php
        break;
endswitch;
