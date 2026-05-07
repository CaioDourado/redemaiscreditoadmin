<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'admin': ?>
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