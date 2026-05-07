<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'cadastrar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados do Representante</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3"><?php echo form_select('tipo_pessoa','Tipo Remun.',array('1'=>'PSV','2'=>'Franquia')); ?></div>
                            <div class="col-md-3"><?php echo form_input('psv','PSV %','20','text-right') ?></div>
                            <div class="col-md-3"><?php echo form_input('psv','Valor p/ Cliente','0,00','text-right') ?></div>
                        </div>
                    </div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-6"><?php echo form_input('nome_ou_fantasia','Nome ou Nome Fantasia',''); ?></div>
                            <!--div class="col-md-3"><?php //echo form_select('tipo_pessoa','Tipo de Pessoa',array('1'=>'Física','2'=>'Jurídica')); ?></div-->
                            <div class="col-md-3"><?php echo form_input('cpf_cnpj','CPF/CNPJ','') ?></div>
                            <div class="col-md-3"><?php echo form_input('data_nascimento','Data Nascimento','','data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><?php echo form_input('email','E-mail','','','<i class="fa fa-at"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('telefone','Telefone','','telefone text-right','<i class="fa fa-phone"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('celular','Celular','','celular text-right','<i class="fa fa-mobile-phone"></i>'); ?></div>
                        </div>
                    </div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-2"><?php echo form_input('cep','CEP','','cep'); ?></div>
                            <div class="col-md-4"><?php echo form_input('logradouro','Logradouro','','logradouro'); ?></div>
                            <div class="col-md-3"><?php echo form_input('numero','Número'); ?></div>
                            <div class="col-md-3"><?php echo form_input('complemento','Complemento'); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><?php echo form_input('bairro','Bairro','','bairro'); ?></div>
                            <div class="col-md-5"><?php echo form_input('cidade','Cidade','','cidade'); ?></div>
                            <div class="col-md-2"><?php echo form_input('uf','UF','','uf'); ?></div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'alterar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados do Representante</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3"><?php echo form_select('consultor_tipo','Tipo Remun.',array('1'=>'PSV','2'=>'Franquia'),$representante->consultor_tipo); ?></div>
                            <div class="col-md-3"><?php echo form_input('psv','PSV %',$representante->psv,'text-right','R$') ?></div>
                            <div class="col-md-3"><?php echo form_input('consultor_custo','Valor p/ Cliente',$representante->consultor_custo,'text-right','R$') ?></div>
                        </div>
                    </div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-6"><?php echo form_input('nome_ou_fantasia','Nome ou Nome Fantasia',$representante->nome_ou_fantasia); ?></div>
                            <!--div class="col-md-3"><?php //echo form_select('tipo_pessoa','Tipo de Pessoa',array('1'=>'Física','2'=>'Jurídica')); ?></div-->
                            <div class="col-md-3"><?php echo form_input('cpf_cnpj','CPF/CNPJ',$representante->cpf_cnpj) ?></div>
                            <div class="col-md-3"><?php echo form_input('data_nascimento','Data Nascimento',data_pt($representante->data_nascimento,false),'data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><?php echo form_input('email','E-mail',$representante->email,'','<i class="fa fa-at"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('telefone','Telefone',$representante->telefone,'telefone text-right','<i class="fa fa-phone"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('celular','Celular',$representante->celular,'celular text-right','<i class="fa fa-mobile-phone"></i>'); ?></div>
                        </div>
                    </div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-2"><?php echo form_input('cep','CEP',$representante->cep,'cep'); ?></div>
                            <div class="col-md-4"><?php echo form_input('logradouro','Logradouro',$representante->logradouro,'logradouro'); ?></div>
                            <div class="col-md-3"><?php echo form_input('numero','Número',$representante->numero); ?></div>
                            <div class="col-md-3"><?php echo form_input('complemento','Complemento',$representante->complemento); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><?php echo form_input('bairro','Bairro',$representante->bairro,'bairro'); ?></div>
                            <div class="col-md-5"><?php echo form_input('cidade','Cidade',$representante->cidade,'cidade'); ?></div>
                            <div class="col-md-2"><?php echo form_input('uf','UF',$representante->uf,'uf'); ?></div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('representante','Voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
                <?php echo form_close(); ?>
        <?php break;
    case 'gerenciar': ?>
            <div class="text-right">
                <?php echo anchor('representante/cadastrar','Cadastrar',array('class'=>'btn btn-default no-border-radius')); ?>
                <?php echo anchor('representante/valores','Editar Valores',array('class'=>'btn btn-default no-border-radius')); ?>
            </div>
            <br>
            <div class="panel panel-google">
                <div class="panel-heading">Gerenciamento de Representantes</div>
                <div class="table-responsive">
                    <table class="panel-table table-striped table-hover tabela_pesquisavel">
                        <thead>
                        <tr>
                            <th class="w-min350">Nome</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Mens.</th>
                            <th class="text-center">Vcto</th>
                            <th class="text-center">Criação</th>
                            <th class="text-center">Opções</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($representantes as $index => $r): ?>
                            <tr class="<?php echo class_status($r->status); ?>">
                                <td><?php echo anchor('cliente/perfil/'.$r->id_cliente,strtoupper($r->nome_ou_fantasia)); ?></td>
                                <td class="text-center"><?php if($r->status!=1) echo retornar_status($r->status); ?></td>
                                <td class="text-right"><?php echo dinheiro($r->mensalidade); ?></td>
                                <td class="text-center"><?php echo $r->dia_vencimento; ?></td>
                                <td class="text-center"><?php echo data_pt($r->criado_em,false); ?></td>
                                <td class="text-center w-min150">
                                    <?php echo anchor('representante/perfil/'.$r->id_cliente,'Perfil'); ?> |
                                    <?php //echo anchor('representante/produtos_valores/'.$r->id_cliente,'<i class="fa fa-money"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('representante/alterar/'.$r->id_cliente,'Alterar'); ?> |
                                    <?php echo anchor('representante/clientes/'.$r->id_cliente,'Clientes'); ?> |
                                    <?php echo anchor('representante/financeiro/'.$r->id_cliente,'Financeiro'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'clientes': ?>
                <div class="text-right">
                    <?php echo anchor('representante','Voltar',array('class'=>'btn btn-default')); ?>
                </div>
                <br>
                <div class="form-group">
                    <input type="text" class="form-control input-lg" placeholder="Pesquisa de Cliente" id="pesquisa_tabela">
                </div>
                <div class="panel panel-google">
                    <div class="panel-heading">Clientes de <?php echo $representante->nome_ou_fantasia; ?></div>
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
    case 'valores': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Tabela de Valores</div>
                    <table class="table table-panel table-condensed no-margin">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Nome</th>
                            <th class="text-center w-100">Custo</th>
                            <th class="text-center w-100">Fixo</th>
                            <th class="text-center w-100">Venda</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($consultas as $index => $consulta): ?>
                            <?php $check = false; if(array_key_exists($consulta->id_consulta,$cr)) $check = true; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="id_consulta[]" value="<?php echo $consulta->id_consulta; ?>" class="check_line" <?php if($check) echo 'checked'; ?>>
                                </td>
                                <td><?php echo $consulta->nome; ?></td>
                                <td><input type="text" name="custo[]" <?php if(!$check) echo 'disabled'; ?> class="form-control dinheiro text-right input-xs" value="<?php if(!$check) echo dinheiro($consulta->custo); else echo dinheiro($cr[$consulta->id_consulta]->custo); ?>"></td>
                                <td><input type="text" name="fixo[]" <?php if(!$check) echo 'disabled'; ?> class="form-control dinheiro text-right input-xs" value="<?php if(!$check) echo dinheiro($consulta->custo); else echo dinheiro($cr[$consulta->id_consulta]->fixo); ?>"></td>
                                <td><input type="text" name="valor[]" <?php if(!$check) echo 'disabled'; ?> class="form-control dinheiro text-right input-xs" value="<?php echo dinheiro($consulta->venda); ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="panel-footer text-right">
                        <?php echo form_hidden('teste','Teste'); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'financeiro': ?>
            <div class="row">
                <div class="col-md-6">
                    <div style="margin-bottom: 10px">
                        <?php echo anchor('representante/adicionar_psv/'.$id_representante,'Novo PSV',array('class'=>'btn btn-success')); ?>
                    </div>
                    <div class="panel panel-google">
                        <div class="panel-heading">Dados de PSV</div>
                        <div class="table-responsive">
                            <table class="panel-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-right" style="margin-bottom: 10px">
                        <?php echo anchor('cliente/gerar_faturamento_prorata/'.$id_representante,'Gerar Fat. Pró-rata',array('class'=>'btn btn-success')); ?>
                        <?php echo anchor('cliente/gerar_faturamento/'.$id_representante,'Gerar Fat.',array('class'=>'btn btn-success')); ?>
                    </div>
                    <div class="panel panel-google">
                        <div class="panel-heading">Dados de Boletos</div>
                        <div class="table-responsive">
                            <table class="panel-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Descrição</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php break;
    case 'adicionar_psv':  ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados Para Geração De Boleto</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mes">Mês</label>
                                    <input type="text" class="form-control" name="mes" value="<?php echo date('m'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mes">Ano</label>
                                    <input type="text" class="form-control" name="ano" value="<?php echo date('Y'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mes">Tipo Fat.</label>
                                    <select class="form-control" name="tipo_fat">
                                        <option value="psv">PSV</option>
                                        <option value="franquia">Franquia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mes">Valor por Cliente</label>
                                    <input type="text" class="form-control text-right" name="valor_p_cliente" value="<?php echo dinheiro($representante->consultor_custo); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php  echo anchor('representante/financeiro'.$id_representante,'Voltar',array('class'=>'btn btn-default'));?>
                        <?php echo form_submit('submit','Emitir',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'adicionar_boleto': ?>
            
        <?php break;
endswitch; ?>
