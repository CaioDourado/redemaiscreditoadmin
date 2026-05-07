<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
        <div class="panel panel-google">
            <div class="panel-heading">Fornecedores</div>
            <div class="table-responsive">
                <table class="panel-table table-hover table-striped no-margin">
                    <thead>
                        <tr>
                            <th class="text-right">#</th>
                            <th>Nome</th>
                            <th class="text-center">Telefone</th>
                            <th class="text-center">Celular</th>
                            <th>E-mail</th>
                            <th class="text-center">Criação</th>
                            <th class="text-center">Opções</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($fornecedores as $index => $fornecedor): ?>
                            <tr>
                                <td class="text-right"><?php echo $index+1 ?></td>
                                <td><?php echo $fornecedor->nome; ?></td>
                                <td class="text-center"><?php echo $fornecedor->telefone; ?></td>
                                <td class="text-center"><?php echo $fornecedor->celular; ?></td>
                                <td><?php echo $fornecedor->email; ?></td>
                                <td class="text-center"><?php echo data_pt($fornecedor->criado_em,false) ; ?></td>
                                <td class="text-center">
                                    <?php echo anchor('fornecedor/perfil/'.$fornecedor->id_fornecedor,'<i class="fa fa-user"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('fornecedor/alterar/'.$fornecedor->id_fornecedor,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                    <?php echo anchor('fornecedor/excluir/'.$fornecedor->id_fornecedor,'<i class="fa fa-close"></i>',array('class'=>'btn btn-danger btn-xs')); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php break;
    case 'perfil': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Perfil de <?php echo $fornecedor->nome; ?></div>
                <div class="table-responsive">
                    <table class="panel-table table-hover table-striped no-margin">
                        <tbody>
                            <tr><td style="width: 200px;font-weight: bold" class="text-right">Nome</td><td><?php echo $fornecedor->nome; ?></td></tr>
                            <tr><td style="width: 200px;font-weight: bold" class="text-right">E-mail</td><td><?php echo $fornecedor->email; ?></td></tr>
                            <tr><td style="width: 200px;font-weight: bold" class="text-right">Telefone</td><td><?php echo $fornecedor->telefone; ?></td></tr>
                            <tr><td style="width: 200px;font-weight: bold" class="text-right">Celular</td><td><?php echo $fornecedor->celular; ?></td></tr>
                            <tr><td style="width: 200px;font-weight: bold" class="text-right">Observação</td><td><?php echo $fornecedor->observacao; ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-google">
                <div class="panel-heading">Consultas de <?php echo $fornecedor->nome; ?></div>
                <div class="table-responsive">
                    <table class="panel-table table-hover table-striped no-margin">
                        <thead>
                            <tr>
                                <th class="text-right">#</th>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th class="text-right">Custo</th>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($consultas as $index => $consulta): ?>
                                <tr>
                                    <td class="text-right"><?php echo $index+1; ?></td>
                                    <td><?php echo $consulta->nome; ?></td>
                                    <td><?php echo $consulta->slug; ?></td>
                                    <td class="text-right"><?php echo dinheiro($consulta->custo); ?></td>
                                    <td class="text-center">
                                        <?php echo anchor('fornecedor/alterar_consulta/'.$consulta->id_fornecedor_consulta,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-xs btn-warning')); ?>
                                        <?php echo anchor('fornecedor/teste/'.$consulta->id_fornecedor_consulta,'<i class="fa fa-arrow-right"></i>',array('class'=>'btn btn-xs btn-info')); ?>
                                        <?php echo anchor('fornecedor/teste_bateria/'.$consulta->id_fornecedor_consulta,'<i class="fa fa-gavel"></i>',array('class'=>'btn btn-xs btn-info')); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'adicionar_consulta': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Adicionar Consulta para <?php echo $fornecedor->nome; ?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3"><?php echo form_input('nome','Nome'); ?></div>
                            <div class="col-md-3"><?php echo form_input('slug','Slug'); ?></div>
                            <div class="col-md-3"><?php echo form_input('custo','Custo','0,00','dinheiro text-right','<i class="fa fa-money"></i>'); ?></div>
                            <div class="col-md-3">
                            <div class="form-group">
                                <label for="formato">Formato</label>
                                <select name="formato" class="form-control">
                                    <option value="xml">XML</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="requisicao">Requisição</label>
                                    <textarea name="requisicao" id="" rows="3" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <?php echo form_input('header','Header'); ?>
                        <?php echo form_input('body','Body'); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea name="descricao" id="" rows="3" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Cancelar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'alterar_consulta': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Adicionar Consulta para <?php echo $fornecedor->nome; ?></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('nome','Nome',$consulta->nome); ?></div>
                        <div class="col-md-3"><?php echo form_input('slug','Slug',$consulta->slug); ?></div>
                        <div class="col-md-3"><?php echo form_input('custo','Custo',dinheiro($consulta->custo),'dinheiro text-right','<i class="fa fa-money"></i>'); ?></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="formato">Formato</label>
                                <select name="formato" class="form-control">
                                    <option value="xml" <?php if($consulta->formato=='xml') echo 'SELECTED'; ?>>XML</option>
                                    <option value="json" <?php if($consulta->formato=='json') echo 'SELECTED'; ?>>JSON</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="requisicao">Requisição</label>
                                <textarea name="requisicao" id="" rows="3" class="form-control"><?php echo $consulta->requisicao; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="header">Header</label>
                        <input type="text" class="form-control" name="header" value='<?php echo $consulta->header; ?>'>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <input type="text" class="form-control" name="body" value='<?php echo $consulta->body; ?>'>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <textarea name="descricao" id="" rows="3" class="form-control"><?php echo $consulta->descricao; ?></textarea>
                            </div>
                        </div>
                    </div>
					<div class="form-group">
						<label for="body">Arquivo (caminho)</label>
						<input type="text" class="form-control" name="file" value='<?php echo $consulta->file; ?>'>
					</div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'cadastrar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Cadastrar Fornecedor</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6"><?php echo form_input('nome','Nome'); ?></div>
                            <div class="col-md-6"><?php echo form_input('email','E-mail'); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><?php echo form_input('usuario','Usuario','','','<i class="fa fa-user"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('senha','Senha','','','<i class="fa fa-lock"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('telefone','Telefone','','telefone text-center','<i class="fa fa-phone"></i>'); ?></div>
                            <div class="col-md-3"><?php echo form_input('celular','Celular','','celular text-center','<i class="fa fa-mobile-phone"></i>'); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo form_input('chave','Chave','','','<i class="fa fa-key"></i>'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observacao">Observação</label>
                                    <textarea name="observacao" id="" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('fornecedor','Cancelar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case "alterar": ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Alterar Fornecedor</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('nome','Nome',$fornecedor->nome); ?></div>
                        <div class="col-md-6"><?php echo form_input('email','E-mail',$fornecedor->email); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('usuario','Usuario',$fornecedor->usuario,'','<i class="fa fa-user"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('senha','Senha',$fornecedor->senha,'','<i class="fa fa-lock"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('telefone','Telefone',$fornecedor->telefone,'telefone text-center','<i class="fa fa-phone"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('celular','Celular',$fornecedor->celular,'celular text-center','<i class="fa fa-mobile-phone"></i>'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo form_input('chave','Chave',$fornecedor->chave,'','<i class="fa fa-key"></i>'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observacao">Observação</label>
                                <textarea name="observacao" id="" rows="4" class="form-control"><?php echo $fornecedor->observacao; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="checkbox"> <label> <input type="checkbox" name="isauth"> Ativar Autenticação </label> </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo form_input('authurl','Autenticação Url',$fornecedor->authurl,''); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="authbody">Autenticação Body</label>
                                <textarea name="authbody" id="" rows="4" class="form-control"><?php echo $fornecedor->authbody; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo form_input('header','Header',$fornecedor->header,''); ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('fornecedor','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'teste': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-google">
                <div class="panel-heading border-top">Efetuar Requisição</div>
                <div class="panel-body border-top">
                    <div class="form-group">
                        <label for="requisicao">Requisição ( substitua os campos entre colchetes. )</label>
                        <textarea name="requisicao" rows="3" class="form-control"><?php echo $consulta->requisicao; ?></textarea>
                    </div>
                    <?php if($fornecedor->isauth==1): ?>
                        <div class="form-group">
                            <label for="header">Header</label>
                            <input type="text" class="form-control" name="header" value='<?php echo $header; ?>'>
                        </div>
                        <div class="form-group">
                            <label for="body">Body</label>
                            <input type="text" class="form-control" name="body" value='<?php echo $body; ?>'>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="panel panel-google">
                <div class="panel-heading border-top">Histórico de Teste</div><div class="panel-body border-top">
                    <?php foreach($historico as $index => $ch): ?>
                        <?php if($index > 0) echo '<hr>'; ?>
                        <p class="text-right"><small><?php echo data_pt($ch->criado_em); ?></small></p>
                        <p class="text-center"><small><b><u><i><?php echo $ch->requisicao; ?></i></u></b></small></p>
                        <p style="word-break: break-word">
                            <?php //echo nl2br(json_encode(json_decode($ch->retorno_json),JSON_PRETTY_PRINT)); ?>
                            <?php echo $ch->retorno_json; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php break;
    case 'teste_bateria': ?>
        <?php echo form_open(current_url()); ?>
            <div class="panel panel-google">
                <div class="panel-heading border-top">Efetuar Requisição</div>
                <div class="panel-body border-top">
                    <div class="form-group">
                        <label for="requisicao">Requisição ( substitua os campos entre colchetes. )</label>
                        <textarea name="requisicao" rows="3" class="form-control"><?php echo $consulta->requisicao; ?></textarea>
                    </div>
                    <?php if($fornecedor->isauth==1): ?>
                        <div class="form-group">
                            <label for="header">Header</label>
                            <input type="text" class="form-control" name="header" value='<?php echo $header; ?>'>
                        </div>
                        <div class="form-group">
                            <label for="body">Body</label>
                            <input type="text" class="form-control" name="body" value='<?php echo $body; ?>'>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo">Tipo de Consulta</label>
                                <select name="tipo" id="" class="form-control">
                                    <option value="cnpj" SELECTED>CNPJ</option>
                                    <option value="cpf">CPF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="formato_i">Formato Inicial</label>
                                <select name="formato_i" id="" class="form-control">
                                    <option value="xml" SELECTED>XML</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="formato_f">Formato Final</label>
                                <select name="formato_f" id="" class="form-control">
                                    <option value="json" SELECTED>JSON</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('fornecedor/perfil/'.$fornecedor->id_fornecedor,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="panel panel-google">
                <div class="panel-heading border-top">Histórico de Teste <?php echo anchor('fornecedor/teste_bateria_analise/'.$consulta->id_fornecedor_consulta,'Analisar',array('class'=>'btn btn-default pull-right')); ?></div>
                <table class="table panel-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Pesquisa</th>
                            <th>Conversão</th>
                            <th class="text-right">Tempo</th>
                            <th class="text-right">Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historico as $index => $ch): ?>
                            <tr>
                                <td class="text-right"><?php echo $index+1; ?></td>
                                <td><?php echo $ch->pesquisa; ?></td>
                                <td><?php if($ch->retorno_json!="false") echo 'OK'; else echo 'ERRO'; ?></td>
                                <td class="text-right"><?php echo $ch->tempo_retorno; ?></td>
                                <td class="text-right"><?php echo data_pt($ch->criado_em,true); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'teste_bateria_analise': ?>
            <div class="panel panel-google">
                <div class="panel-heading border-top">Histórico de Teste 
                    <?php echo anchor('fornecedor/teste_bateria_analise/'.$consulta->id_fornecedor_consulta,'Reanalisar',array('class'=>'btn btn-default pull-right')); ?>
                    <?php echo anchor('fornecedor/teste_bateria/'.$consulta->id_fornecedor_consulta,'Voltar',array('class'=>'btn btn-default pull-right','style'=>'margin-right: 5px')); ?> 
                </div>
                <table class="table panel-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Pesquisa</th>
                            <th>Conversão</th>
                            <th class="text-right">Tempo</th>
                            <th class="text-right">Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historico as $index => $ch): ?>
                            <tr>
                                <td class="text-right"><?php echo $index+1; ?></td>
                                <td><?php echo $ch->pesquisa; ?></td>
                                <td><?php if($ch->retorno_json!="false") echo 'OK'; else echo 'ERRO'; ?></td>
                                <td class="text-right"><?php echo $ch->tempo_retorno; ?></td>
                                <td class="text-right"><?php echo data_pt($ch->criado_em,true); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
endswitch;
