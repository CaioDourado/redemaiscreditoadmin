<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'admin': ?>
            <?php
                $maior_abertura = 1;
                foreach($aberturas_mensais as $abertura){
                    $maior_abertura = max($maior_abertura, $abertura['matriz'], $abertura['franquia']);
                }
                $largura_grafico = 1120;
                $altura_grafico = 164;
                $inicio_x = 52;
                $fim_x = 1090;
                $topo_y = 18;
                $base_y = 112;
                $largura_grupo = ($fim_x - $inicio_x) / count($aberturas_mensais);
            ?>
            <div class="panel panel-google">
                <div class="panel-heading">
                    Clientes abertos por mês
                    <span class="pull-right"><i class="fa fa-square" style="color:#00a86b"></i> Matriz &nbsp; <i class="fa fa-square" style="color:#42b9dc"></i> Franquias</span>
                </div>
                <div class="panel-body border-top" style="padding:15px 15px 5px;">
                    <p class="text-muted">Aberturas dos últimos 12 meses.</p>
                    <div style="overflow-x:auto;">
                        <svg viewBox="0 0 <?php echo $largura_grafico; ?> <?php echo $altura_grafico; ?>" style="display:block; min-width:760px; width:100%; height:auto;" role="img" aria-label="Clientes abertos por mês, separados entre matriz e franquias">
                            <?php for($linha = 0; $linha <= 4; $linha++): ?>
                                <?php $y = $topo_y + (($base_y - $topo_y) * $linha / 4); ?>
                                <line x1="<?php echo $inicio_x; ?>" y1="<?php echo $y; ?>" x2="<?php echo $fim_x; ?>" y2="<?php echo $y; ?>" stroke="#e7ecef" stroke-width="1" />
                                <text x="<?php echo $inicio_x - 10; ?>" y="<?php echo $y + 4; ?>" text-anchor="end" font-size="11" fill="#7a8793"><?php echo (int) round($maior_abertura * (4 - $linha) / 4); ?></text>
                            <?php endfor; ?>
                            <line x1="<?php echo $inicio_x; ?>" y1="<?php echo $base_y; ?>" x2="<?php echo $fim_x; ?>" y2="<?php echo $base_y; ?>" stroke="#bdc8cf" stroke-width="1" />
                            <?php foreach($aberturas_mensais as $indice => $abertura): ?>
                                <?php
                                    $x_grupo = $inicio_x + ($largura_grupo * $indice);
                                    $largura_barra = 16;
                                    $altura_matriz = ($base_y - $topo_y) * $abertura['matriz'] / $maior_abertura;
                                    $altura_franquia = ($base_y - $topo_y) * $abertura['franquia'] / $maior_abertura;
                                    $x_matriz = $x_grupo + ($largura_grupo / 2) - 19;
                                    $x_franquia = $x_grupo + ($largura_grupo / 2) + 3;
                                ?>
                                <rect x="<?php echo round($x_matriz, 2); ?>" y="<?php echo round($base_y - $altura_matriz, 2); ?>" width="<?php echo $largura_barra; ?>" height="<?php echo round($altura_matriz, 2); ?>" rx="2" fill="#00a86b"><title>Matriz: <?php echo $abertura['matriz']; ?> clientes em <?php echo $abertura['label']; ?></title></rect>
                                <rect x="<?php echo round($x_franquia, 2); ?>" y="<?php echo round($base_y - $altura_franquia, 2); ?>" width="<?php echo $largura_barra; ?>" height="<?php echo round($altura_franquia, 2); ?>" rx="2" fill="#42b9dc"><title>Franquias: <?php echo $abertura['franquia']; ?> clientes em <?php echo $abertura['label']; ?></title></rect>
                                <?php if($abertura['matriz'] > 0): ?><text x="<?php echo round($x_matriz + 8, 2); ?>" y="<?php echo round($base_y - $altura_matriz - 7, 2); ?>" text-anchor="middle" font-size="11" fill="#35606a"><?php echo $abertura['matriz']; ?></text><?php endif; ?>
                                <?php if($abertura['franquia'] > 0): ?><text x="<?php echo round($x_franquia + 8, 2); ?>" y="<?php echo round($base_y - $altura_franquia - 7, 2); ?>" text-anchor="middle" font-size="11" fill="#35606a"><?php echo $abertura['franquia']; ?></text><?php endif; ?>
                                <text x="<?php echo round($x_grupo + ($largura_grupo / 2), 2); ?>" y="<?php echo $base_y + 23; ?>" text-anchor="middle" font-size="11" fill="#64727d"><?php echo $abertura['label']; ?></text>
                            <?php endforeach; ?>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="panel panel-google">
                <div class="panel-heading">Cadastros por semana</div>
                <div class="panel-body border-top" style="padding-bottom:8px;"><span class="text-muted">Últimas 15 semanas, agrupadas pelo domingo de início da semana.</span></div>
                <div class="table-responsive">
                    <table class="panel-table table-hover table-striped no-margin" style="min-width:1050px;">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <?php foreach($aberturas_semanais as $semana): ?><th class="text-center"><?php echo $semana['label']; ?></th><?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Matriz</b></td>
                                <?php foreach($aberturas_semanais as $semana): ?><td class="text-center text-success"><?php echo $semana['matriz']; ?></td><?php endforeach; ?>
                            </tr>
                            <tr>
                                <td><b>Franquias</b></td>
                                <?php foreach($aberturas_semanais as $semana): ?><td class="text-center text-info"><?php echo $semana['franquia']; ?></td><?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <form action="<?php echo base_url().'index.php/pesquisa'; ?>" method="GET">
                <div class="row">
                    <div class="col-md-9">
                        <input type="text" class="form-control input-lg" placeholder="Pesquisa de Cliente por Nome, CPF ou CNPJ" name="pesquisa"><br>
                    </div>
                    <div class="col-md-3">
                        <?php echo form_submit('submit','Pesquisar',array('class'=>'btn btn-info btn-lg btn-block')) ?>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-google">
                        <div class="panel-heading">Clientes</div>
                        <div class="table-responsive">
                            <table class="panel-table table-hover table-striped no-margin">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-right">Qtd</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($status  as $index => $l): ?>
                                        <tr>
                                            <td><?php echo $l['status']; ?></td>
                                            <td class="text-right"><?php echo $l['qtd']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-google">
                        <div class="panel-heading">Inadimplencia (60 dias)</div>
                        <div class="table-responsive">
                            <table class="panel-table table-hover table-striped no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th class="w-min350">Nome</th>
                                        <th class="text-right">Boletos</th>
                                        <th class="text-right">Valor</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($inadimplentes as $index => $l): ?>
                                        <tr>
                                            <td class="text-right""><?php echo $index +1; ?></td>
                                            <td><?php echo $l->nome; ?></td>
                                            <td class="text-right"><?php echo $l->qtd; ?></td>
                                            <td class="text-right"> <?php echo dinheiro($l->valor); ?></td>
                                            <td class="text-center">

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php break;
    case 'super': ?>

        <?php break;
    case 'gerencia': ?>

        <?php break;
endswitch;
