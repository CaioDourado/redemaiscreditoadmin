<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <form action="<?php echo base_url().'index.php/pesquisa'; ?>" method="GET">
                <div class="row">
                    <div class="col-md-9">
                        <input type="text" class="form-control input-lg" placeholder="Pesquisa de Cliente por Nome, CPF ou CNPJ" name="pesquisa" value="<?php echo $pesquisa; ?>"><br>
                    </div>
                    <div class="col-md-3">
                        <?php echo form_submit('submit','Pesquisar',array('class'=>'btn btn-info btn-lg btn-block')) ?>
                    </div>
                </div>
            </form>
            <?php if($retorno!=null): ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Clientes retornados de <u><?php echo $pesquisa; ?></u></div>
                    <table class="panel-table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>CPF/CNPJ</th>
                                <th>Nome</th>
                                <th>Cidade</th>
                                <th>Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($retorno as $i => $r): ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo $r->cpf_cnpj; ?></td>
                                    <td><?php echo ucwords($r->nome_ou_fantasia); ?></td>
                                    <td><?php echo ucwords($r->cidade); ?></td>
                                    <td><?php echo anchor('cliente/perfil/'.$r->id_cliente,'Visualizar'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php break;
endswitch;
