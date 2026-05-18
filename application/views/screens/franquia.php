<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Franquias Cadastradas</div>
                <div class="table-responsive">
                    <table class="panel-table table-hover no-margin">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="w-min140">Nome</th>
                                <th>CNPJ</th>
                                <th class="text-right">Unidades</th>
                                <th class="text-right">Clientes</th>
                                <th class="w-min150">Criação</th>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($franquias as $index => $franquia): ?>
                                <tr>
                                    <td class="text-right"><?php echo $index+1; ?></td>
                                    <td><?php echo $franquia->nome_ou_fantasia; ?></td>
                                    <td><?php echo $franquia->cnpj; ?></td>
                                    <td class="text-right"><?php echo $franquia->unidades; ?></td>
                                    <td class="text-right"><?php echo $franquia->clientes; ?></td>
                                    <td><?php echo data_pt($franquia->criado_em,true); ?></td>
                                    <td class="text-center">
                                        <?php echo anchor('franquia/perfil/'.$franquia->id_franquia,'Perfil'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'perfil': ?>
            <div class="row">
                <div class="col-md-3">
                    <?php echo anchor('franquia/alterar/'.$id_franquia,'<i class="fa fa-pencil"></i> Alterar',array('class'=>'btg btg-block')); ?>
                    <?php echo anchor('franquia/cadastrar_unidade/'.$id_franquia,'<i class="fa fa-plus"></i> Unidade',array('class'=>'btg btg-block')); ?>
                    <?php echo anchor('franquia/valores_e_precos/'.$id_franquia,'<i class="fa fa-money"></i> Valores e Preços',array('class'=>'btg btg-block')); ?>
                    <?php echo anchor('franquia/gerar_faturamento/'.$id_franquia,'<i class="fa fa-arrow-right"></i> Gerar Faturamento',array('class'=>'btg btg-block')); ?>
                    <?php echo anchor('franquia/clientes/'.$id_franquia,'<i class="fa fa-users"></i> Clientes',array('class'=>'btg btg-block')); ?>
                    <?php echo anchor('franquia/gerar_fatura/'.$id_franquia,'<i class="fa fa-file"></i> Gerar Fatura',array('class'=>'btg btg-block')); ?>
                </div>
                <div class="col-md-9">
                    <div class="panel panel-google">
                        <div class="panel-heading">Perfil de Franquia</div>
                        <div class="panel-body border-top">
                            <dl style="margin: 0">
                                <dt>Nome Fantasia</dt><dd><?php echo $franquia->nome_ou_fantasia; ?></dd>
                                <dt>Razão Social</dt><dd><?php echo $franquia->razao_social; ?></dd>
                                <dt>CNPJ</dt><dd><?php echo $franquia->cnpj; ?></dd>
                                <dt>Criação</dt><dd><?php echo data_pt($franquia->criado_em); ?></dd>
                            </dl>
                        </div>
                    </div>
                    <!--div class="panel panel-google">
                        <div class="panel-heading">Unidades</div>
                        <div class="table-responsive">
                            <table class="panel-table table-hover no-margin">
                                <thead>
                                <tr>
                                    <th class="text-right"></th>
                                    <th>Nome</th>
                                    <th class="text-right">Clientes</th>
                                    <th class="text-right">Criação</th>
                                    <th class="text-right">Opções</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($unidades as $index => $unidade): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $index+1; ?></td>
                                        <td><?php echo $unidade->nome; ?></td>
                                        <td class="text-right"><?php echo $unidade->clientes; ?></td>
                                        <td class="text-right"><?php echo data_pt($unidade->criado_em,true); ?></td>
                                        <td class="text-right">
                                            <?php echo anchor('franquia/perfil_unidade/'.$unidade->id_unidade_franquia,'Perfil de Unidade'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div-->
                </div>
            </div>

            <br>

            <div class="panel panel-google">
                <div class="panel-heading">Boletos</div>
                <div class="table-responsive">
                    <table class="panel-table table-hover no-margin">
                        <thead>
                            <tr>
                                <th class="text-right">#</th>
                                <th class="text-right">ID</th>
                                <th class="text-right">NN</th>
                                <th class="text-center">Geracao</th>
                                <th class="text-center">Vencimento</th>
                                <th>Descricao</th>
                                <th class="text-center">Pagamento</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Valor</th>
                                <th class="text-center">Opcoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($boletos)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum boleto encontrado para o CNPJ desta franquia.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($boletos as $index => $boleto): ?>
                                <?php
                                    $status_boleto = 'Em aberto';
                                    if(isset($boleto->cancelado)&&$boleto->cancelado==1) $status_boleto = 'Cancelado';
                                    if(isset($boleto->pago)&&$boleto->pago==1) $status_boleto = 'Pago';
                                ?>
                                <tr>
                                    <td class="text-right"><?php echo $index+1; ?></td>
                                    <td class="text-right"><?php echo $boleto->id_boleto; ?></td>
                                    <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                    <td class="text-center"><?php echo data_pt($boleto->criado_em,false); ?></td>
                                    <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                    <td><?php echo $boleto->descricao_boleto; ?></td>
                                    <td class="text-center">
                                        <?php if($boleto->data_pagamento!="0000-00-00"&&$boleto->data_pagamento!=""): echo data_pt($boleto->data_pagamento,false); endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $status_boleto; ?></td>
                                    <td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
                                    <td class="text-center">
                                        <?php echo anchor('boleto/visualizar/'.$boleto->hash,'Visualizar',array('target'=>'_blank')); ?> |
                                        <?php echo anchor('boleto/baixar/'.$boleto->hash,'Baixar'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-google">
                <div class="panel-heading">Faturas</div>
                <div class="panel-body border-top">

                </div>
            </div>
        <?php break;
    case 'perfil_unidade': ?>
            <div class="text-right" style="margin-bottom: 10px">
                <?php echo anchor('franquia/perfil/'.$unidade->id_franquia_fk,'Voltar',array('class'=>'btn btn-default')); ?>
            </div>
            <h3 class="page-header" style="margin-top: 0"><?php echo $unidade->nome; ?></h3>
            <div class="panel panel-google">
                <div class="panel-heading">Clientes</div>
                <div class="panel-body no-padding">
                    <table class="table table-hover no-margin">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Mens.</th>
                                <th>Vcto</th>
                                <th>Opções</th>
                                <th>Criação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $index => $cliente): ?>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'cadastrar': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados Básicos da Franquia</div>
                <div class="panel-body border-top">
                    <div class="form-group">
                        <label for="nome_ou_fantasia">Nome Fantasia</label>
                        <input type="text" class="form-control" name="nome_ou_fantasia">
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nome_ou_fantasia">Razão Social</label>
                                <input type="text" class="form-control" name="razao_social">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" class="form-control" name="cnpj">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('franquia','Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
        <?php break;
    case 'cadastrar_unidade': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Cadastro de Unidade</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="nome">Nome da Unidade</label>
                            <input type="text" class="form-control" name="nome">
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('franquia/perfil/'.$franquia->id_franquia,'Voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'valores_e_precos': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Defina os valores dos produtos</div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="valor_minimo">Valor Mínimo</label>
                                <input type="text" class="form-control dinheiro text-right" name="valor_minimo" value="0,00">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="text-right">Valor Mínimo</th>
                            <th class="text-right" style="width: 150px !important">Valor Estipulado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($consultas as $index => $consulta): ?>
                            <tr>
                                <td><?php echo $consulta->nome; ?></td>
                                <td class="text-right"><?php echo dinheiro($consulta->venda); ?></td>
                                <td><input type="text" class="form-control dinheiro text-right" name="consultas[]"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'gerar_faturamento': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Geração de Faturas em Massa</div>
                    <div class="panel-body border-top">
                        <div class="form-group">
                            <label for="dia_vcto">Dia de Vencimento</label>
                            <select name="dia_vcto" class="form-control">
                                <?php foreach($dias as $index => $dia): ?>
                                    <option value="<?php echo $index; ?>"><?php echo $dia; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('franquia/perfil/'.$id_franquia,'Voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'clientes': ?>
            <div class="form-group">
                <input type="text" class="form-control input-lg" placeholder="Pesquisa de Cliente" id="pesquisa_tabela">
            </div>
            <div class="panel panel-google">
                <div class="panel-heading">Gerenciamento de Clientes</div>
                <div class="table-responsive">
                    <table class="panel-table table-striped table-hover tabela_pesquisavel">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-min350">Nome</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Mens.</th>
                            <th class="text-center">Vcto</th>
                            <th class="text-center">Criação</th>
                            <th class="text-center">Opções</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($clientes as $index => $cliente): ?>
                            <tr class="<?php echo class_status($cliente->status); ?>">
                                <td class="text-right"><?php echo $index+1; ?></td>
                                <td><?php echo anchor('cliente/perfil/'.$cliente->id_cliente,strtoupper($cliente->nome_ou_fantasia)); ?></td>
                                <td class="text-center"><?php if($cliente->status!=1) echo retornar_status($cliente->status); ?></td>
                                <td class="text-right"><?php echo dinheiro($cliente->mensalidade); ?></td>
                                <td class="text-center"><?php echo $cliente->dia_vencimento; ?></td>
                                <td class="text-center"><?php echo data_pt($cliente->criado_em,false); ?></td>
                                <td class="text-center w-min150">
                                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'<i class="fa fa-user"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('cliente/produtos_valores/'.$cliente->id_cliente,'<i class="fa fa-money"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('cliente/alterar/'.$cliente->id_cliente,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                    <?php
                                    if($cliente->status==1)
                                        echo anchor('cliente/inativar/'.$cliente->id_cliente,'<i class="fa fa-close"></i>',array('class'=>'btn btn-danger btn-xs'));
                                    else
                                        echo anchor('cliente/reativar/'.$cliente->id_cliente,'<i class="fa fa-refresh"></i>',array('class'=>'btn btn-success btn-xs'));
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'gerar_fatura': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados Para Geração da Fatura</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="valor_individual">Valor Individual</label>
                                    <input type="text" class="form-control text-right dinheiro" name="valor_individual" value="16,80">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="data_inicial">Data Inicial</label>
                                    <input type="text" class="form-control text-center" name="data_inicial" value="<?php echo '05/'.date('m/Y',strtotime(date('Y-m-d').'-1 month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="data_final">Data Final</label>
                                    <input type="text" class="form-control text-center" name="data_final" value="<?php echo '05/'.date('m/Y'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php echo form_submit('submit','Gera Fatura',array('class'=>'btn btn-success btn-block','style'=>'margin-top: 23px')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('franquia/perfil/'.$id_franquia,'Voltar',array('class'=>'btn btn-default')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>

            <div class="panel panel-google">
                <div class="panel-heading">Faturas de <?php echo $franquia->nome_ou_fantasia; ?></div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Vencimento</th>
                            <th class="text-right">Valor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($faturas)>0): ?>
                            <?php foreach($faturas as $i => $fatura): ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo $fatura->nome; ?></td>
                                    <td><?php echo data_pt($fatura->inicio,false); ?></td>
                                    <td><?php echo data_pt($fatura->fim,false); ?></td>
                                    <td><?php echo data_pt($fatura->vencimento,false); ?></td>
                                    <td class="text-right"><?php echo dinheiro($fatura->valor); ?></td>
                                    <td class="text-right"><?php echo anchor('franquia/fatura_visualizar/'.$fatura->id_adm_franquia_fatura,'Visualizar',array('target'=>'_blank')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
endswitch;
