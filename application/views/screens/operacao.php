<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
        <form method="get" action="<?php echo site_url('operacao'); ?>">
            <div class="panel panel-google">
                <div class="panel-heading">Filtros</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Inicio</label>
                                <input type="date" name="inicio" class="form-control" value="<?php echo $filtros['inicio']; ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Fim</label>
                                <input type="date" name="fim" class="form-control" value="<?php echo $filtros['fim']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Consulta</label>
                                <select name="slug" class="form-control">
                                    <option value="">Todas</option>
                                    <?php foreach($slugs as $item): ?>
                                        <option value="<?php echo $item->slug; ?>" <?php if($filtros['slug'] == $item->slug) echo 'selected'; ?>><?php echo $item->slug; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fornecedor</label>
                                <select name="fornecedor" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach($fornecedores as $item): ?>
                                        <option value="<?php echo $item->fornecedor; ?>" <?php if($filtros['fornecedor'] == $item->fornecedor) echo 'selected'; ?>><?php echo $item->fornecedor; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Erro</label>
                                <input type="text" name="tipo_erro" class="form-control" value="<?php echo $filtros['tipo_erro']; ?>" placeholder="TIMEOUT">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('operacao', 'Limpar', array('class' => 'btn btn-default')); ?>
                    <button type="submit" class="btn btn-success">Atualizar</button>
                </div>
            </div>
        </form>

        <div class="panel panel-google">
            <div class="panel-heading">Saude por Consulta e Fornecedor</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                    <tr>
                        <th>Consulta</th>
                        <th>Fornecedor</th>
                        <th class="text-right">Tentativas</th>
                        <th class="text-right">Sucessos</th>
                        <th class="text-right">Falhas</th>
                        <th class="text-right">Timeouts</th>
                        <th class="text-right">Sucesso %</th>
                        <th class="text-right">Media</th>
                        <th class="text-right">Maior</th>
                        <th class="text-center">Ultima</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($resumo as $linha): ?>
                        <?php
                            $taxa = $linha->tentativas > 0 ? round(($linha->sucessos / $linha->tentativas) * 100, 1) : 0;
                            $classe = 'success';
                            if($taxa < 70 || $linha->media_ms > 10000) $classe = 'danger';
                            elseif($taxa < 90 || $linha->media_ms > 7000) $classe = 'warning';
                        ?>
                        <tr class="<?php echo $classe; ?>">
                            <td><?php echo $linha->slug; ?></td>
                            <td><?php echo $linha->fornecedor; ?></td>
                            <td class="text-right"><?php echo $linha->tentativas; ?></td>
                            <td class="text-right"><?php echo $linha->sucessos; ?></td>
                            <td class="text-right"><?php echo $linha->falhas; ?></td>
                            <td class="text-right"><?php echo $linha->timeouts; ?></td>
                            <td class="text-right"><?php echo number_format($taxa, 1, ',', '.'); ?>%</td>
                            <td class="text-right"><?php echo number_format($linha->media_ms / 1000, 2, ',', '.'); ?>s</td>
                            <td class="text-right"><?php echo number_format($linha->maior_ms / 1000, 2, ',', '.'); ?>s</td>
                            <td class="text-center"><?php echo data_pt($linha->ultima_tentativa); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-google">
            <div class="panel-heading">Principais Erros</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                    <tr>
                        <th>Consulta</th>
                        <th>Fornecedor</th>
                        <th>Tipo</th>
                        <th>Origem</th>
                        <th class="text-right">HTTP</th>
                        <th class="text-right">Qtd</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($erros as $linha): ?>
                        <tr>
                            <td><?php echo $linha->slug; ?></td>
                            <td><?php echo $linha->fornecedor; ?></td>
                            <td><?php echo $linha->tipo_erro; ?></td>
                            <td><?php echo $linha->erro_origem; ?></td>
                            <td class="text-right"><?php echo $linha->http_status; ?></td>
                            <td class="text-right"><?php echo $linha->qtd; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-google">
            <div class="panel-heading">Ultimas Tentativas</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                    <tr>
                        <th class="text-right">#</th>
                        <th>Consulta</th>
                        <th>Fornecedor</th>
                        <th class="text-right">HTTP</th>
                        <th>Erro</th>
                        <th>Origem</th>
                        <th class="text-center">Valido</th>
                        <th class="text-right">Tempo</th>
                        <th class="text-center">Quando</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($ultimas as $linha): ?>
                        <tr>
                            <td class="text-right"><?php echo $linha->id_consulta_fornecedor_metric; ?></td>
                            <td><?php echo $linha->slug; ?></td>
                            <td><?php echo $linha->fornecedor; ?></td>
                            <td class="text-right"><?php echo $linha->http_status; ?></td>
                            <td><?php echo $linha->tipo_erro; ?></td>
                            <td><?php echo $linha->erro_origem; ?></td>
                            <td class="text-center"><?php echo $linha->valido == 1 ? 'Sim' : 'Nao'; ?></td>
                            <td class="text-right"><?php echo number_format($linha->tempo_ms / 1000, 2, ',', '.'); ?>s</td>
                            <td class="text-center"><?php echo data_pt($linha->criado_em); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php break;
    case 'top3': ?>
        <form method="get" action="<?php echo site_url('operacao/top3'); ?>">
            <div class="panel panel-google">
                <div class="panel-heading">Filtros</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Inicio</label>
                                <input type="date" name="inicio" class="form-control" value="<?php echo $filtros['inicio']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fim</label>
                                <input type="date" name="fim" class="form-control" value="<?php echo $filtros['fim']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('operacao/top3', 'Limpar', array('class' => 'btn btn-default')); ?>
                    <button type="submit" class="btn btn-success">Atualizar</button>
                </div>
            </div>
        </form>

        <div class="panel panel-google">
            <div class="panel-heading">Top 3 Fornecedores por Consulta Disponivel</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                    <tr>
                        <th>Consulta</th>
                        <th class="text-right">Venda</th>
                        <th>1o Fornecedor</th>
                        <th>2o Fornecedor</th>
                        <th>3o Fornecedor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($top3_consultas as $consulta): ?>
                        <tr>
                            <td>
                                <b><?php echo $consulta->nome; ?></b><br>
                                <small><?php echo $consulta->slug; ?></small>
                            </td>
                            <td class="text-right">R$ <?php echo number_format($consulta->venda, 2, ',', '.'); ?></td>
                            <?php for($i=0; $i<3; $i++): ?>
                                <?php $provider = isset($consulta->fornecedores[$i]) ? $consulta->fornecedores[$i] : null; ?>
                                <td class="<?php echo $provider != null ? $provider->classe : 'warning'; ?>">
                                    <?php if($provider != null): ?>
                                        <b><?php echo $provider->fornecedor_nome; ?></b><br>
                                        <small>
                                            <?php echo $provider->rotulo; ?>
                                            <?php if($provider->sucesso_percentual !== null): ?>
                                                - <?php echo number_format($provider->sucesso_percentual, 1, ',', '.'); ?>%
                                            <?php else: ?>
                                                - sem amostra
                                            <?php endif; ?>
                                        </small><br>
                                        <small>
                                            Tentativas: <?php echo $provider->tentativas; ?>
                                            <?php if($provider->media_ms !== null): ?>
                                                | Media: <?php echo number_format($provider->media_ms / 1000, 2, ',', '.'); ?>s
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <small>Sem fornecedor</small>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php break;
endswitch;
