<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'email_geral': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados Para Envio</div>
                    <div class="panel-body border-top">
                        <div class="form-group">
                            <label for="titulo">Titulo</label>
                            <input type="text" class="form-control" name="titulo">
                        </div>
                        <div class="form-group">
                            <label for="mensagem">Mensagem</label>
                            <textarea name="mensagem" rows="8" class="form-control" id="edittext"></textarea>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo form_submit('submit','Enviar Agora',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>

            <div class="panel panel-google">
                <div class="panel-heading">E-mails</div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nome</th>
                            <th class="text-right">E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clientes as $i => $c): ?>
                            <tr>
                                <td class="text-right"><?php echo $i+1; ?></td>
                                <td><?php echo $c->nome_ou_fantasia; ?></td>
                                <td class="text-right"><?php echo $c->email; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'gestao_negativacao': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Lista de Negativações</div>
                <div class="table-resposive">
                    <table class="panel-table table-hover table-striped no-margin">
                        <thead>
                            <tr>
                                <th class="text-right">#</th>
                                <th class="text-right">ID</th>
                                <th>Slug</th>
                                <th>CPF/CNPJ</th>
                                <th class="text-right">Contrato</th>
                                <th class="text-right">Parcelas</th>
                                <th class="text-right">Data</th>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($negativacoes as $index => $n): ?>
                                <?php if(intval($n->parametros->PARCELAS)>1): ?>
                                    <tr class="text-danger">
                                <?php else: ?>
                                    <tr>
                                <?php endif; ?>
                                    <td class="text-right"><?php echo $index+1; ?></td>
                                    <td class="text-right"><?php echo $n->id_negativacao; ?></td>
                                    <td><?php echo $n->slug; ?></td>
                                    <td><?php echo $n->cpf_cnpj; ?></td>
                                    <td class="text-right"><?php echo $n->contrato; ?></td>
                                    <td class="text-right"><?php echo $n->parametros->PARCELAS; ?></td>
                                    <td class="text-right"><?php echo data_pt($n->criado_em); ?></td>
                                    <td class="text-center">
                                        <?php echo anchor('cliente/negativacao_baixar/'.$n->id_negativacao,'Baixar'); ?> |
                                        <?php echo anchor('cliente/negativacao_refazer/'.$n->id_negativacao,'Refazer'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'validacao': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Validação de Clientes</div>
                <div class="panel-body">
                    <table class="table table-bordered table-condensed table-consulta table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Vcto</th>
                                <th>CPF/CNPJ</th>
                                <th>CEP</th>
                                <th>UF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $index => $cliente): ?>
                                <tr>
                                    <td><?php echo $index+1; ?></td>
                                    <td><?php echo anchor('cliente/alterar/'.$cliente->id_cliente,strtoupper($cliente->nome_ou_fantasia)).'<br>'.$cliente->validacao; ?></td>
                                    <td><?php echo $cliente->dia_vencimento; ?></td>
                                    <td><?php echo $cliente->cpf_cnpj; ?></td>
                                    <td><?php echo $cliente->cep; ?></td>
                                    <td><?php echo $cliente->uf; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'perfil': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Perfil de <?php echo $cliente->nome_ou_fantasia; ?></div>
                <div class="panel-body border-top">
                    <div class="row">
                        <div class="col-md-4">
                            <dl>
                                <dt>Nome/Fantasia</dt><dd><?php echo $cliente->nome_ou_fantasia; ?></dd>
                                <dt>Razão Social</dt><dd><?php echo $cliente->razao_social; ?></dd>
                                <dt>CPF/CNPJ</dt><dd><?php echo $cliente->cpf_cnpj; ?></dd>
                                <br>
                                <dt>Endereço</dt><dd><?php echo $cliente->logradouro.' '.$cliente->numero.', '.$cliente->complemento; ?></dd>
                                <dt>Bairro</dt><dd><?php echo $cliente->bairro; ?></dd>
                                <dt>Cidade</dt><dd><?php echo $cliente->cidade; ?></dd>
                                <dt>UF</dt><dd><?php echo $cliente->uf; ?></dd>
                                <dt>CEP</dt><dd><?php echo $cliente->cep; ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Telefone</dt><dd><?php echo $cliente->telefone; ?></dd>
                                <dt>Celular</dt><dd><?php echo $cliente->celular; ?></dd>
                                <dt>E-mail</dt><dd><?php echo $cliente->email; ?></dd>
                                <br>
                                <dt>Proprietário</dt><dd><?php echo $cliente->nome_proprietario; ?></dd>
                                <dt>CPF</dt><dd><?php echo $cliente->cpf_proprietario; ?></dd>
                                <dt>Data Nasc.</dt><dd><?php echo data_pt($cliente->data_nascimento_proprietario,false); ?></dd>
                                <dt>Criado em</dt><dd><?php echo data_pt($cliente->criado_em,true); ?></dd>
                                <dt>Aceite em</dt><dd><?php echo data_pt($cliente->aceite_em,true); ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Mensalidade</dt><dd><?php echo dinheiro($cliente->mensalidade); ?></dd>
                                <dt>Franquia</dt><dd><?php echo dinheiro($cliente->franquia); ?></dd>
                                <dt>Dia Vencimento</dt><dd><?php echo $cliente->dia_vencimento; ?></dd>
                                <br>
                                <dt>Limite Cons.</dt><dd><?php echo $cliente->limite_consulta_qtd; ?></dd>
                                <dt>Limite Valor.</dt><dd><?php echo dinheiro($cliente->limite_consulta_valor); ?></dd>
                            </dl>
                        </div>
                    </div>

                </div>
            </div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#consumo" aria-controls="consumo" role="tab" data-toggle="tab">Consumo</a></li>
                <!--li role="presentation"><a href="#valores_produtos" aria-controls="valores_produtos" role="tab" data-toggle="tab">Valores e Produtos</a></li-->
                <li role="presentation"><a href="#usuarios" aria-controls="usuarios" role="tab" data-toggle="tab">Usuários</a></li>
                <li role="presentation"><a href="#boletos" aria-controls="boletos" role="tab" data-toggle="tab">Boletos</a></li>
                <li role="presentation"><a href="#faturas" aria-controls="faturas" role="tab" data-toggle="tab">Faturas</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="consumo">
                    <ul class="nav nav-tabs circle" role="tablist">
                        <li role="presentation" class="active"><a href="#consultas" aria-controls="consultas" role="tab" data-toggle="tab">Consul<span class="hide_xs">tas</span></a></li>
                        <li role="presentation"><a href="#veiculares" aria-controls="veiculares" role="tab" data-toggle="tab">Veic<span class="hide_xs">ulares</span></a></li>
                        <li role="presentation"><a href="#cartas" aria-controls="cartas" role="tab" data-toggle="tab">Cartas</a></li>
                        <li role="presentation"><a href="#negativacoes" aria-controls="negativacoes" role="tab" data-toggle="tab">Neg<span class="hide_xs">ativações</span></a></li>
                        <li role="presentation"><a href="#baixas" aria-controls="baixas" role="tab" data-toggle="tab">Baixas</a></li>
                    </ul>
                    <div class="tab-content clear">
                        <div class="tab-pane active" id="consultas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-consulta table-hover">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center"><i class="fa fa-eye"></i></th>
                                        <th class="text-right">#</th>
                                        <th>Nome</th>
                                        <th>Consulta</th>
                                        <th class="text-right">Custo</th>
                                        <th class="text-right">Venda</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_custo = 0; $total_valor = 0; ?>
                                    <?php foreach($consultas as $index => $consulta): ?>
                                        <tr>
                                            <td class="text-center" style="font-size:17px; padding: 0 5px;">
                                                <?php if($consulta->restricao!=null):
                                                    if($consulta->restricao==0) echo '<i class="fa fa-check-circle-o" style="color: #00923f"></i>';
                                                    else echo '<i class="fa fa-times-circle-o" style="color: #da251d"></i>';
                                                endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(($consulta->pesquisa_auxiliar!=null)||($consulta->pesquisa_mutipla!=null)): ?>
                                                    <i class="fa fa-eye-slash"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right"><?php echo $index+1; ?></td>
                                            <td><?php echo str_replace('+ Crédito ','',$consulta->nome); ?></td>
                                            <td><?php echo $consulta->pesquisa; ?></td>
                                            <td class="text-right"><?php echo dinheiro($consulta->custo); ?></td>
                                            <td class="text-right"><?php echo dinheiro($consulta->valor); ?></td>
                                            <td class="text-center"><?php echo data_pt($consulta->criado_em,true); ?></td>
                                            <td class="text-center">
                                                <?php echo anchor('cliente/consulta_ver/'.$consulta->id_consulta_efetuada,'Ver'); ?> |
                                                <?php echo anchor('cliente/consulta_mover/'.$consulta->id_consulta_efetuada,'Mover'); ?>
                                            </td>
                                        </tr>
                                        <?php $total_custo += $consulta->custo; $total_valor += $consulta->valor; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="5">Total</th>
                                        <th class="text-right"><?php echo dinheiro($total_custo); ?></th>
                                        <th class="text-right"><?php echo dinheiro($total_valor); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="veiculares" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-consulta table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th>Consulta</th>
                                        <th class="text-right">Custo</th>
                                        <th class="text-right">Venda</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_custo = 0; $total_valor = 0; ?>
                                    <?php foreach($veiculares as $index => $consulta): ?>
                                        <tr>
                                            <td class="text-right"><?php echo $index+1; ?></td>
                                            <td><?php echo $consulta->pesquisa; ?></td>
                                            <td class="text-right"><?php echo dinheiro($consulta->custo); ?></td>
                                            <td class="text-right"><?php echo dinheiro($consulta->valor); ?></td>
                                            <td class="text-center"><?php echo data_pt($consulta->criado_em,true); ?></td>
                                            <td class="text-center">
                                                <?php echo anchor('cliente/ver_consulta/'.$consulta->id_consulta_veicular,'Ver Consulta'); ?>
                                            </td>
                                        </tr>
                                        <?php $total_custo += $consulta->custo; $total_valor += $consulta->valor; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-right"><?php echo dinheiro($total_custo); ?></th>
                                        <th class="text-right"><?php echo dinheiro($total_valor); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="cartas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-consulta table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th>Nome</th>
                                        <th class="text-right">Custo</th>
                                        <th class="text-right">Venda</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_custo = 0; $total_valor = 0; ?>
                                    <?php foreach($cartas as $index => $carta): ?>
                                        <tr>
                                            <td class="text-right"><?php echo $index+1; ?></td>
                                            <td><?php echo $carta->nome; ?></td>
                                            <td class="text-right">0,00</td>
                                            <td class="text-right"><?php echo dinheiro($carta->valor_carta); ?></td>
                                            <td class="text-center"><?php echo data_pt($carta->criado_em,true); ?></td>
                                            <td class="text-center">
                                                <?php echo anchor('cliente/ver_consulta/'.$carta->id_carta,'Ver Carta'); ?>
                                            </td>
                                        </tr>
                                        <?php $total_valor += $carta->valor_carta; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-right">0,00</th>
                                        <th class="text-right"><?php echo dinheiro($total_valor); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="negativacoes" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-consulta table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th class="text-center">Tipo</th>
                                        <th></th>
                                        <th>CPF/CNPJ</th>
                                        <th class="text-right">Custo</th>
                                        <th class="text-right">Venda</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_custo = 0; $total_valor = 0; ?>
                                    <?php foreach($negativacoes as $index => $negativacao): ?>
                                        <tr>
                                            <td class="text-right"><?php echo $index+1; ?></td>
                                            <td class="text-center"><?php echo $negativacao->tipo; ?></td>
                                            <td class="text-center">
                                                <?php echo $negativacao->status; ?>
                                            </td>
                                            <td><?php echo $negativacao->cpf_cnpj; ?></td>
                                            <td class="text-right"><?php echo dinheiro($negativacao->custo); ?></td>
                                            <td class="text-right"><?php echo dinheiro($negativacao->valor); ?></td>
                                            <td class="text-center"><?php echo data_pt($negativacao->criado_em,true); ?></td>
                                            <td class="text-center">
                                                <?php echo anchor('cliente/negativacao_visualizar/'.$negativacao->id_negativacao,'Ver'); ?> |
                                                <?php echo anchor('cliente/negativacao_refazer/'.$negativacao->id_negativacao,'Refazer'); ?> |
                                                <?php echo anchor('cliente/negativacao_baixar/'.$negativacao->id_negativacao,'Baixar'); ?>
                                                <?php if(isset($retorno_negativacao->inclusao->erro)) echo ' | '.anchor('cliente/negativacao_mover/'.$negativacao->id_negativacao,'Mover'); ?>
                                            </td>
                                        </tr>
                                        <?php $total_custo += $negativacao->custo; $total_valor += $negativacao->valor; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-right"><?php echo dinheiro($total_custo); ?></th>
                                        <th class="text-right"><?php echo dinheiro($total_valor); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="baixas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-consulta table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th>CPF/CNPJ</th>
                                        <th class="text-right">Custo</th>
                                        <th class="text-right">Venda</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_custo = 0; $total_valor = 0; ?>
                                    <?php foreach($baixas as $index => $baixa): ?>
                                        <tr>
                                            <td class="text-right"><?php echo $index+1; ?></td>
                                            <td><?php echo $baixa->cpf_cnpj; ?></td>
                                            <td class="text-right"><?php echo dinheiro($baixa->custo); ?></td>
                                            <td class="text-right"><?php echo dinheiro($baixa->valor); ?></td>
                                            <td class="text-center"><?php echo data_pt($baixa->criado_em,true); ?></td>
                                            <td class="text-center">
                                                <?php echo anchor('cliente/ver_consulta/'.$baixa->id_negativacao_baixa,'Ver Baixa'); ?>
                                            </td>
                                        </tr>
                                        <?php $total_custo += $baixa->custo; $total_valor += $baixa->valor; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-right"><?php echo dinheiro($total_custo); ?></th>
                                        <th class="text-right"><?php echo dinheiro($total_valor); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--div role="tabpanel" class="tab-pane" id="valores_produtos"></div-->
                <div role="tabpanel" class="tab-pane" id="usuarios">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-consulta table-hover">
                            <thead>
                                <tr>
                                    <th class="text-right">#</th>
                                    <th>Id</th>
                                    <th class="text-center">Status</th>
                                    <th>Nome</th>
                                    <th class="text-center">Usuário</th>
                                    <th class="text-center">Senha</th>
                                    <th class="text-center">Criação</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($usuarios as $index => $usuario): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $index+1; ?></td>
                                        <td><?php echo $usuario->id_usuario; ?></td>
                                        <td class="text-center"><?php echo $usuario->status; ?></td>
                                        <td><?php echo $usuario->nome; ?></td>
                                        <td class="text-center"><?php echo $usuario->usuario; ?></td>
                                        <td class="text-center"><?php echo $usuario->senha; ?></td>
                                        <td class="text-center"><?php echo data_pt($usuario->criado_em,true); ?></td>
                                        <td class="text-center"><?php echo anchor('usuario/redefinir_senha/'.$usuario->id_usuario,'Redefenir Senha',array('class'=>'btn btn-info btn-xs')); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="boletos">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-consulta table-hover">
                            <thead>
                                <tr>
                                    <th class="text-right">#</th>
                                    <th class="text-right">NN</th>
                                    <th class="text-center">Geração</th>
                                    <th class="text-center">Vencimento</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Pagamento</th>
                                    <th class="text-right">Valor</th>
                                    <th class="text-center">Opções</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($boletos as $index => $boleto): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $index+1; ?></td>
                                        <td class="text-right"><?php echo $boleto->nosso_numero; ?></td>
                                        <td class="text-center"><?php echo data_pt($boleto->criado_em,false); ?></td>
                                        <td class="text-center"><?php echo data_pt($boleto->data_vencimento,false); ?></td>
                                        <td><?php echo $boleto->descricao_boleto; ?></td>
                                        <td class="text-center"><?php if($boleto->data_pagamento!="0000-00-00") echo data_pt($boleto->data_pagamento,false); ?></td>
                                        <td class="text-right"><?php echo dinheiro($boleto->valor_boleto); ?></td>
                                        <td class="text-center">
                                            <?php echo anchor('boleto/visualizar/'.$boleto->hash,'Visualizar'); ?> |
                                            <?php echo anchor('boleto/baixar/'.$boleto->hash,'Baixar'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="faturas">
                    <div class="text-right" style="margin-bottom: 10px;">
                        <?php echo anchor('cliente/gerar_faturamento_prorata/'.$cliente->id_cliente,'Gerar Fatura Pró-Rata', array('class'=>'btn btn-info')); ?>
                        <?php echo anchor('cliente/gerar_faturamento/'.$cliente->id_cliente,'Gerar Fatura', array('class'=>'btn btn-info')); ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-consulta table-hover">
                            <thead>
                                <tr>
                                    <th class="text-right">#</th>
                                    <th class="text-center">Vencimento</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Inicio</th>
                                    <th class="text-center">Fim</th>
                                    <th class="text-right">Valor</th>
                                    <th class="text-center">Opções</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($faturas as $index => $fatura): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $index+1; ?></td>
                                        <td class="text-center"><?php echo data_pt($fatura->vencimento,false); ?></td>
                                        <td><?php echo $fatura->nome; ?></td>
                                        <td class="text-center"><?php echo data_pt($fatura->inicio,false); ?></td>
                                        <td class="text-center"><?php echo data_pt($fatura->fim,false); ?></td>
                                        <td class="text-right"><?php echo dinheiro($fatura->valor); ?></td>
                                        <td class="text-center">
                                            <?php echo anchor('fatura/visualizar/'.$fatura->id_fatura,'<i class="fa fa-eye"></i>',array('class'=>'btn btn-us btn-default')); ?>
                                            <?php echo anchor('fatura/alterar/'.$fatura->id_fatura,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-us btn-default')); ?>
                                            <?php echo anchor('fatura/pdf/'.$fatura->id_fatura,'<i class="fa fa-file-pdf-o"></i>',array('class'=>'btn btn-us btn-default')); ?>
                                            <?php echo anchor('fatura/pdf_resumo/'.$fatura->id_fatura,'<i class="fa fa-file-pdf-o"></i>',array('class'=>'btn btn-us btn-info')); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php break;
    case 'gerar_faturamento': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados da Geração</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vencimento">Vencimento</label>
                                    <input type="text" name="vencimento" class="form-control text-center" value="<?php echo $cliente->dia_vencimento; ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vencimento">Vencimento</label>
                                    <select name="mes" class="form-control">
                                        <?php foreach(meses_array() as $index => $i): ?>
                                            <option value="<?php echo $index; ?>" <?php if($index==date('m')) echo 'SELECTED'; ?>><?php echo $i; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ano">Ano</label>
                                    <input type="text" name="ano" class="form-control text-center" value="<?php echo date('Y'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php echo form_submit('submit','Gerar',array('class'=>'btn btn-info btn-block', 'style'=>'margin-top: 23px')) ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    </div>
                </div>
            <?php form_close(); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados da Geração</div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th class="text-right">#</th>
                            <th class="text-center">Vencimento</th>
                            <th>Descrição</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fim</th>
                            <th class="text-right">Valor</th>
                            <th class="text-center">Boleto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($faturas as $i => $fatura): ?>
                            <tr>
                                <td class="text-right"><?php echo $i+1; ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->vencimento,false); ?></td>
                                <td><?php echo $fatura->nome; ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->inicio,false); ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->fim,false); ?></td>
                                <td class="text-right""><?php echo dinheiro($fatura->valor); ?></td>
                                <td class="text-center">
                                    <?php if($fatura->hash_boleto!=null): echo anchor('boleto/visualizar/'.$fatura->hash_boleto,'Ver'); ?>
                                    <?php else: echo anchor('cliente/gerar_boleto_via_fatura/'.$fatura->id_fatura,'Gerar'); endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'gerar_faturamento_prorata': ?>
            <?php echo form_open(current_url()); ?>
                <?php $now = date('Y-m-d').' 00:00:00' ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados da Geração</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="inicio">Início</label>
                                    <input type="text" name="inicio" class="form-control text-center" value="<?php echo date('d/m/Y H:i:s', strtotime($now.' -1 month')) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fim">Fim</label>
                                    <input type="text" name="fim" class="form-control text-center" value="<?php echo date('d/m/Y', strtotime($now)).' 23:59:59'; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fim">Vencimento</label>
                                    <input type="text" name="vencimento" class="form-control text-center" value="<?php echo date('d/m/Y'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php echo form_submit('submit','Gerar',array('class'=>'btn btn-info btn-block', 'style'=>'margin-top: 23px')) ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    </div>
                </div>
            <?php form_close(); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados da Geração</div>
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th class="text-right">#</th>
                            <th class="text-center">Vencimento</th>
                            <th>Descrição</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fim</th>
                            <th class="text-right">Valor</th>
                            <th class="text-center">Boleto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($faturas as $i => $fatura): ?>
                            <tr>
                                <td class="text-right"><?php echo $i+1; ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->vencimento,false); ?></td>
                                <td><?php echo anchor('fatura/pdf/'.$fatura->id_fatura,$fatura->nome); ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->inicio,false); ?></td>
                                <td class="text-center"><?php echo data_pt($fatura->fim,false); ?></td>
                                <td class="text-right""><?php echo dinheiro($fatura->valor); ?></td>
                                <td class="text-center">
                                    <?php if($fatura->hash_boleto!=null): echo anchor('boleto/visualizar/'.$fatura->hash_boleto,'Ver'); ?>
                                    <?php else: echo anchor('cliente/gerar_boleto_via_fatura/'.$fatura->id_fatura,'Gerar'); endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'gerenciar_novo': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Resumo</div>
                <table class="panel-table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td><b>Faturamento</b></td>
                            <td class="text-right"><?php echo dinheiro($resumo['faturamento']); ?></td>
                        </tr>
                        <tr>
                            <td><b>Vencimentos</b></td>
                            <td class="text-right">
                                <?php foreach($resumo['dias'] as $index => $item): ?>
                                    <span style="margin-left: 10px"><?php echo $item; ?> clientes (dia <?php echo $index; ?>)</span>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Status</b></td>
                            <td class="text-right">
                                <?php foreach($resumo['status'] as $index => $item): ?>
                                    <span style="margin-left: 10px"><b><?php echo $status[$index]; ?>:</b> <?php echo $item; ?> clientes</span>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-lg" placeholder="Pesquisa de Cliente" id="pesquisa_tabela">
            </div>
            <!--div class="row">
                <div class="col-md-4">
                    <?php echo anchor('cliente?area=matriz','Clientes da Matriz',array('class'=>'btg btg-block text-center')); ?>
                </div>
                <div class="col-md-4">
                    <?php echo anchor('cliente?area=franquia','Clientes de Franquia',array('class'=>'btg btg-block text-center')); ?>
                </div>
                <div class="col-md-4">
                    <?php echo anchor('cliente?area=representantes','Clientes de Representantes',array('class'=>'btg btg-block text-center')); ?>
                </div>
            </div-->
            <div class="panel panel-google">
                <div class="panel-heading">Gerenciamento de Clientes</div>
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
                            <?php foreach($clientes as $index => $cliente): ?>
                                <tr class="<?php echo class_status($cliente->status); ?>">
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
    case 'gerenciar': ?>
            <!--div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"> Matriz</div>
                        <div class="card-content">
                            <p class="text-right">130 Clientes</p>
                            <h3>R$ <span class="pull-right val">145,00</span></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"> Franquias</div>
                        <div class="card-content">
                            <p class="text-right">130 Clientes</p>
                            <h3>R$ <span class="pull-right val">145,00</span></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"> Representantes</div>
                        <div class="card-content">
                            <p class="text-right">130 Clientes</p>
                            <h3>R$ <span class="pull-right val">145,00</span></h3>
                        </div>
                    </div>
                </div>
            </div-->
            <div class="row">
                <div class="col-md-4"><?php echo anchor('cliente/gerenciar?area=matriz','Clientes Matriz',array('class'=>'btn btn-info btn-block')); ?></div>
                <div class="col-md-4"><?php echo anchor('cliente/gerenciar?area=franquias','Clientes Franquias',array('class'=>'btn btn-info btn-block')); ?></div>
                <div class="col-md-4"><?php echo anchor('cliente/gerenciar?area=representantes','Clientes Representantes',array('class'=>'btn btn-info btn-block')); ?></div>
            </div>
            <br>
            <div class="panel panel-google">
                <div class="panel-heading">Clientes de <?php echo ucwords($area); ?></div>
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
                            <?php foreach($clientes as $index => $cliente): ?>
                                <tr class="<?php echo class_status($cliente->status); ?>">
                                    <td><?php echo $index+1; ?>. <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,strtoupper($cliente->nome_ou_fantasia)); ?></td>
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
    case 'gerenciar_por_area': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados por Cidade</div>
                <div class="table-responsive">
                    <table class="panel-table table-condensed table-bordered table-hover no-margin">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Cidade</th>
                                <th class="text-right">Clientes</th>
                                <th class="text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $index => $cliente): ?>
                                <tr>
                                    <td class="text-right"><?php echo ($index+1); ?></td>
                                    <td><?php echo anchor('cliente/clientes_por_area/'.strtolower(str_replace(' ','_',$cliente->cidade)),$cliente->cidade); ?></td>
                                    <td class="text-right"><?php echo $cliente->qtd; ?></td>
                                    <td class="text-right"><?php echo dinheiro($cliente->valor); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'clientes_por_area': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Clientes na Cidade de <?php echo $cidade; ?></div>
                <div class="table-responsive">
                    <table class="panel-table table-condensed table-bordered table-hover no-margin">
                        <thead>
                        <tr>
                            <th class="text-right">#</th>
                            <th>Id</th>
                            <th>Nome</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Mens.</th>
                            <th class="text-center">Vcto</th>
                            <th class="text-center">Opções</th>
                            <th class="text-center">Criação</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($clientes as $index => $cliente): ?>
                            <tr>
                                <td class="text-right"><?php echo $index+1 ?></td>
                                <td><?php echo $cliente->id_cliente; ?></td>
                                <td><?php echo strtoupper($cliente->nome_ou_fantasia); ?></td>
                                <td class="text-center"><?php echo retornar_status($cliente->status); ?></td>
                                <td class="text-right"><?php echo dinheiro($cliente->mensalidade); ?></td>
                                <td class="text-center"><?php echo $cliente->dia_vencimento; ?></td>
                                <td class="text-center">
                                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'<i class="fa fa-user"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('cliente/produtos_valores/'.$cliente->id_cliente,'<i class="fa fa-money"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                    <?php echo anchor('cliente/alterar/'.$cliente->id_cliente,'<i class="fa fa-pencil"></i>',array('class'=>'btn btn-warning btn-xs')); ?>
                                    <?php echo anchor('cliente/inativar/'.$cliente->id_cliente,'<i class="fa fa-close"></i>',array('class'=>'btn btn-danger btn-xs')); ?>
                                </td>
                                <td class="text-center"><?php echo data_pt($cliente->criado_em,false); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
    case 'cadastrar': ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p style="margin: 0">É necessário consultar os dados do cliente no site da receita, <a href="https://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/cnpjreva_solicitacao2.asp" target="_blank">Clique Aqui</a>.</p>
                    <p style="margin: 0">* Para que o cliente possa ser negativado todos os dados devem ser iguais aos da receita.</p>
                </div>
            </div>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Cadastrar Cliente</div>
				<div class="panel-body">
					<div class="col-md-6"></div>
					<div class="col-md-3"><?php echo form_select('bloqueavel','Cliente Bloqueavel ?',array('0'=>'Não','1'=>'Sim')); ?></div>
					<div class="col-md-3"><?php echo form_input('limite_bloqueio','Limite do Bloqueio','300,00','dinheiro text-right'); ?></div>
				</div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_select('consultor','É Consultor ?',array('0'=>'Cliente','1'=>'Consultor')); ?></div>
                        <div class="col-md-3"><?php echo form_select('id_consultor_fk','Consultor',$consultores); ?></div>
                        <div class="col-md-3"><?php echo form_select('status','Situação',array('1'=>'Ativo','0'=>'Bloqueado')); ?></div>
                        <div class="col-md-3"><?php echo form_select('id_plano_fk','Plano',$planos); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('mensalidade','Mensalidade','65,00','dinheiro text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('franquia','Franquia','33,00','dinheiro text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('limite_consulta_qtd','Limite de Consultas (qtd)','50','text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('limite_consulta_valor','Limite de Consultas (valor)','300,00','dinheiro text-right'); ?></div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_select('tipo_pessoa','Tipo de Pessoa',array('1'=>'Física','2'=>'Jurídica')); ?></div>
                        <div class="col-md-3"><?php echo form_input('cpf_cnpj','CPF/CNPJ','') ?></div>
                        <div class="col-md-3"><?php echo form_input('data_nascimento','Data de Abertura','','data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        <!--div class="col-md-3"><?php //echo form_input('documento','Doc. Identidade','','text-center') ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('nome_ou_fantasia','Nome ou Nome Fantasia',''); ?></div>
                        <div class="col-md-6"><?php echo form_input('razao_social','Razão Social',''); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><?php echo form_input('carta_nome1','Nome em Carta 1'); ?></div>
                        <div class="col-md-4"><?php echo form_input('carta_nome2','Nome em Carta 2'); ?></div>
                        <div class="col-md-4"><?php echo form_input('carta_nome3','Nome em Carta 3'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('email','E-mail','','','<i class="fa fa-at"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('telefone','Telefone','','telefone text-right','<i class="fa fa-phone"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('celular','Celular','','celular text-right','<i class="fa fa-mobile-phone"></i>'); ?></div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
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
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('nome_proprietario','Nome Proprietário',''); ?></div>
                        <div class="col-md-3"><?php echo form_input('cpf_proprietario','CPF Proprietário','') ?></div>
                        <div class="col-md-3"><?php echo form_input('data_nascimento_proprietario','Nasc. Proprietário','','data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        <div class="col-md-3">
                            <?php echo form_select('dia_vencimento','Dia de Vencimento',array('15'=>'15','30'=>'30')); ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'alterar': ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p style="margin: 0">É necessário consultar os dados do cliente no site da receita, <a href="https://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/cnpjreva_solicitacao2.asp" target="_blank">Clique Aqui</a>.</p>
                    <p style="margin: 0">* Para que o cliente possa ser negativado todos os dados devem ser iguais aos da receita.</p>
                </div>
            </div>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Alterar Cliente</div>
				<div class="panel-body">
					<div class="col-md-6"></div>
					<div class="col-md-3"><?php echo form_select('bloqueavel','Cliente Bloqueavel ?',array('0'=>'Não','1'=>'Sim'),'',$cliente->bloqueavel); ?></div>
					<div class="col-md-3"><?php echo form_input('limite_bloqueio','Limite do Bloqueio',$cliente->limite_bloqueio,'dinheiro text-right'); ?></div>
				</div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_select('consultor','É Consultor ?',array('0'=>'Cliente','1'=>'Consultor'),'',$cliente->consultor); ?></div>
                        <div class="col-md-3"><?php echo form_select('id_consultor_fk','Consultor',$consultores,'',$cliente->id_consultor_fk); ?></div>
                        <div class="col-md-3"><?php echo form_select('status','Situação',array('1'=>'Ativo','0'=>'Bloqueado'),'',$cliente->status); ?></div>
                        <div class="col-md-3"><?php echo form_select('id_plano_fk','Plano',$planos,'',$cliente->id_plano_fk); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('mensalidade','Mensalidade',dinheiro($cliente->mensalidade),'dinheiro text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('franquia','Franquia',dinheiro($cliente->franquia),'dinheiro text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('limite_consulta_qtd','Limite de Consultas (qtd)',dinheiro($cliente->limite_consulta_qtd),'text-right'); ?></div>
                        <div class="col-md-3"><?php echo form_input('limite_consulta_valor','Limite de Consultas (valor)',dinheiro($cliente->limite_consulta_valor),'dinheiro text-right'); ?></div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_select('tipo_pessoa','Tipo de Pessoa',array('1'=>'Física','2'=>'Jurídica'),'',$cliente->tipo_pessoa); ?></div>
                        <div class="col-md-3"><?php echo form_input('cpf_cnpj','CPF/CNPJ',$cliente->cpf_cnpj) ?></div>
                        <div class="col-md-3"><?php echo form_input('data_nascimento','Data de Abertura',data_pt($cliente->data_nascimento,false),'data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        <div class="col-md-3"><?php echo form_select('limite_status','Ativar Limite',array('1'=>'Sim','0'=>'Não'),'',$cliente->limite_status); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('nome_ou_fantasia','Nome ou Nome Fantasia',$cliente->nome_ou_fantasia); ?></div>
                        <div class="col-md-6"><?php echo form_input('razao_social','Razão Social',$cliente->razao_social); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><?php echo form_input('carta_nome1','Nome em Carta 1',$cliente->carta_nome1); ?></div>
                        <div class="col-md-4"><?php echo form_input('carta_nome2','Nome em Carta 2',$cliente->carta_nome2); ?></div>
                        <div class="col-md-4"><?php echo form_input('carta_nome3','Nome em Carta 3',$cliente->carta_nome3); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('email','E-mail',$cliente->email,'','<i class="fa fa-at"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('telefone','Telefone',$cliente->telefone,'telefone text-right','<i class="fa fa-phone"></i>'); ?></div>
                        <div class="col-md-3"><?php echo form_input('celular','Celular',$cliente->celular,'celular text-right','<i class="fa fa-mobile-phone"></i>'); ?></div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-2"><?php echo form_input('cep','CEP',$cliente->cep,'cep'); ?></div>
                        <div class="col-md-4"><?php echo form_input('logradouro','Logradouro',$cliente->logradouro,'logradouro'); ?></div>
                        <div class="col-md-3"><?php echo form_input('numero','Número',$cliente->numero); ?></div>
                        <div class="col-md-3"><?php echo form_input('complemento','Complemento',$cliente->complemento); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-5"><?php echo form_input('bairro','Bairro',$cliente->bairro,'bairro'); ?></div>
                        <div class="col-md-5"><?php echo form_input('cidade','Cidade',$cliente->cidade,'cidade'); ?></div>
                        <div class="col-md-2"><?php echo form_input('uf','UF',$cliente->uf,'uf'); ?></div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: 1px solid #AAA">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('nome_proprietario','Nome Proprietário',$cliente->nome_proprietario); ?></div>
                        <div class="col-md-3"><?php echo form_input('cpf_proprietario','CPF Proprietário',$cliente->cpf_proprietario) ?></div>
                        <div class="col-md-3"><?php echo form_input('data_nascimento_proprietario','Nasc. Proprietário',data_pt($cliente->data_nascimento_proprietario,false),'data text-center','<i class="fa fa-calendar"></i>') ?></div>
                        <div class="col-md-3">
                            <?php echo form_select('dia_vencimento','Dia de Vencimento',array('15'=>'15','30'=>'30'),'',$cliente->dia_vencimento); ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'inativar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-blue">
                    <div class="panel-heading">Cliente - Inativar</div>
                    <div class="panel-body">
                        <p class="text-center text-danger">Você está pedindo a inativação do cliente <b><?php echo $cliente->nome_ou_fantasia; ?></b>.</p>
                        <div class="form-group">
                            <label for="status">Motivo da Inativação</label>
                            <select name="status" id="" class="form-control">
                                <option value="0">Cancelamento</option>
                                <option value="2">Bloqueio por Inadimplencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Cancelar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')) ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'ver_consulta': ?>

        <?php break;
    case 'produtos_e_valores': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-gray">
                <div class="panel-heading">Produtos e Valores Habilitados para: <?php echo $cliente->nome_ou_fantasia; ?></div>
                <div class="panel-body no-padding">
                    <table class="table table-condensed no-margin">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th class="w-100">Valor</th>
                                <th class="w-100">Qtd GE</th>
                                <th class="w-100">Valor GE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($consultas as $index => $consulta): ?>
                                <?php $check = false; if(array_key_exists($consulta->id_consulta,$consultas_cliente)) $check = true; ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="id_consulta[]" value="<?php echo $consulta->id_consulta; ?>" class="check_line" <?php if($check) echo 'checked'; ?>>
                                    </td>
                                    <td><?php echo $consulta->nome; ?></td>
                                    <td><input type="text" <?php if(!$check) echo 'disabled'; ?> name="valor[]" class="form-control dinheiro text-right input-xs" value="<?php if(!$check) echo dinheiro($consulta->venda); else echo dinheiro($consultas_cliente[$consulta->id_consulta]->valor); ?>"></td>
                                    <td><input type="text" <?php if(!$check) echo 'disabled'; ?> name="qtd_ge[]" class="form-control text-right input-xs" value="<?php if(!$check){ if($consulta->qtd_ge!=null&&$consulta->qtd_ge!=0) echo $consulta->qtd_ge; else echo 0; } else { echo $consultas_cliente[$consulta->id_consulta]->qtd_ge; } ?>"></td>
                                    <td><input type="text" <?php if(!$check) echo 'disabled'; ?> name="valor_ge[]" class="form-control dinheiro text-right input-xs" value="<?php if(!$check){ if($consulta->venda_ge!=0&&$consulta->venda_ge!=null) echo dinheiro($consulta->venda_ge); else echo dinheiro($consulta->venda);} else { echo dinheiro($consultas_cliente[$consulta->id_consulta]->valor_ge); } ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer text-right">
                    <?php echo form_hidden('id_cliente',$cliente->id_cliente); ?>
                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'refazer_negativacaopefinpf': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Recriação de Negativação PF - PEFIN</div>
                <div class="panel-body">
                    
                </div>
            </div>
        <?php break;
    case 'refazer_negativacaoscpcpf': ?>
            <?php echo form_open(current_url().(isset($_GET['debug_url']) && $_GET['debug_url'] == '1' ? '?debug_url=1' : '')); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Recriação de Negativação PF - Varejo</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>CNPJ</label><input type="text" class="form-control" value="<?php echo $cliente->cpf_cnpj; ?>" disabled></div></div>
                        <div class="col-md-9"><div class="form-group"><label>Razão Social</label><input type="text" class="form-control" value="<?php echo $cliente->razao_social; ?>" disabled></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-8"><div class="form-group"><label>Endereço</label><input type="text" class="form-control" value="<?php echo $cliente->logradouro; ?>" disabled></div></div>
                        <div class="col-md-1"><div class="form-group"><label>DDD</label><input type="text" class="form-control text-center" value="<?php echo substr($cliente->telefone,0,2); ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Telefone</label><input type="text" class="form-control text-center" value="<?php echo substr($cliente->telefone,2); ?>" disabled></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Bairro</label><input type="text" class="form-control" value="<?php echo $cliente->bairro; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Cidade</label><input type="text" class="form-control" value="<?php echo $cliente->cidade; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>UF</label><input type="text" class="form-control" value="<?php echo $cliente->uf; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Cep</label><input type="text" class="form-control" value="<?php echo $cliente->cep; ?>" disabled></div></div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('cpf','CPF <span style="color: #F00">*</span>',$devedor->DEVEDOR_CPF,'text-right cpf'); ?></div>
                        <div class="col-md-4"><?php echo form_input('nome','Nome <span style="color: #F00">*</span>',$devedor->DEVEDOR_NOME); ?></div>
                        <div class="col-md-5"><?php echo form_select('natureza','Natureza da Negativação <span style="color: #F00">*</span>',cod_natureza_scpc(),'',$devedor->NATUREZA_OPERACAO); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('vencimento_inicio','Venc. Inicial<span style="color: #F00">*</span>',$devedor->DATA_ATRASO,'text-right data data5anos_validate obrigatorio'); ?></div>
                        <div class="col-md-3"><?php echo form_input('vencimento_fim','Venc. Final<span style="color: #F00">*</span>',$devedor->DATA_TERMINO,'text-right data'); ?></div>
                        <div class="col-md-2"><?php echo form_input('parcelas','Parcelas <span style="color: #F00">*</span>',$devedor->PARCELAS,'text-right'); ?></div>
                        <div class="col-md-2"><?php echo form_input('valor','Valor <span style="color: #F00">*</span>',intval($devedor->VALOR),'text-right dinheiro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('contrato','Contrato <span style="color: #F00">*</span>','000001','text-right'); ?></div>
                        <!--div class="col-md-3"><?php //echo form_input('nosso_numero','Nosso Número','','text-right'); ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('data_nascimento','Data de Nascimento <span style="color: #F00">*</span>',$devedor->DEVEDOR_NASCIMENTO,'data text-center'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"><?php echo form_input('cep','CEP <span style="color: #F00">*</span>',$devedor->DEVEDOR_CEP,'cep cep_validate obrigatorio'); ?></div>
                        <div class="col-md-6"><?php echo form_input('logradouro','Endereço <span style="color: #F00">*</span>',trim($devedor->DEVEDOR_ENDERECO),'logradouro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('numero','Número <span style="color: #F00">*</span>'); ?></div>
                        <div class="col-md-2"><?php echo form_input('complemento','Complemento <span style="color: #F00">*</span>'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('bairro','Bairro <span style="color: #F00">*</span>',trim($devedor->DEVEDOR_BAIRRO),'bairro'); ?></div>
                        <div class="col-md-3"><?php echo form_input('cidade','Cidade <span style="color: #F00">*</span>',trim($devedor->DEVEDOR_CIDADE),'cidade'); ?></div>
                        <div class="col-md-3"><?php echo form_input('uf','UF <span style="color: #F00">*</span>',$devedor->DEVEDOR_UF,'uf'); ?></div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Negativar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'refazer_negativacaoscpcpj': ?>
            <?php echo form_open(current_url().(isset($_GET['debug_url']) && $_GET['debug_url'] == '1' ? '?debug_url=1' : '')); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Recriação de Negativação PF - Varejo</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>CNPJ</label><input type="text" class="form-control" value="<?php echo $cliente->cpf_cnpj; ?>" disabled></div></div>
                        <div class="col-md-9"><div class="form-group"><label>Razão Social</label><input type="text" class="form-control" value="<?php echo $cliente->razao_social; ?>" disabled></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-8"><div class="form-group"><label>Endereço</label><input type="text" class="form-control" value="<?php echo $cliente->logradouro; ?>" disabled></div></div>
                        <div class="col-md-1"><div class="form-group"><label>DDD</label><input type="text" class="form-control text-center" value="<?php echo substr($cliente->telefone,0,2); ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Telefone</label><input type="text" class="form-control text-center" value="<?php echo substr($cliente->telefone,2); ?>" disabled></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Bairro</label><input type="text" class="form-control" value="<?php echo $cliente->bairro; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Cidade</label><input type="text" class="form-control" value="<?php echo $cliente->cidade; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>UF</label><input type="text" class="form-control" value="<?php echo $cliente->uf; ?>" disabled></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Cep</label><input type="text" class="form-control" value="<?php echo $cliente->cep; ?>" disabled></div></div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('cnpj','CNPJ <span style="color: #F00">*</span>',$devedor->DEVEDOR_CNPJ,'text-right cnpj'); ?></div>
                        <div class="col-md-4"><?php echo form_input('razao_social','Razão Social <span style="color: #F00">*</span>',$devedor->DEVEDOR_RAZAO_SOCIAL); ?></div>
                        <div class="col-md-5"><?php echo form_select('natureza','Natureza da Negativação <span style="color: #F00">*</span>',cod_natureza_scpc(),'',$devedor->NATUREZA_OPERACAO); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('vencimento_inicio','Venc. Inicial<span style="color: #F00">*</span>',$devedor->DATA_ATRASO,'text-right data data5anos_validate obrigatorio'); ?></div>
                        <div class="col-md-3"><?php echo form_input('vencimento_fim','Venc. Final<span style="color: #F00">*</span>',$devedor->DATA_TERMINO,'text-right data'); ?></div>
                        <div class="col-md-2"><?php echo form_input('parcelas','Parcelas <span style="color: #F00">*</span>',$devedor->PARCELAS,'text-right'); ?></div>
                        <div class="col-md-2"><?php echo form_input('valor','Valor <span style="color: #F00">*</span>',intval($devedor->VALOR),'text-right dinheiro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('contrato','Contrato <span style="color: #F00">*</span>','000001','text-right'); ?></div>
                        <!--div class="col-md-3"><?php //echo form_input('nosso_numero','Nosso Número','','text-right'); ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-2"><?php echo form_input('cep','CEP <span style="color: #F00">*</span>',$devedor->DEVEDOR_CEP,'cep cep_validate obrigatorio'); ?></div>
                        <div class="col-md-6"><?php echo form_input('logradouro','Endereço <span style="color: #F00">*</span>',$devedor->DEVEDOR_ENDERECO,'logradouro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('numero','Número <span style="color: #F00">*</span>'); ?></div>
                        <div class="col-md-2"><?php echo form_input('complemento','Complemento'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('bairro','Bairro <span style="color: #F00">*</span>',$devedor->DEVEDOR_BAIRRO,'bairro'); ?></div>
                        <div class="col-md-3"><?php echo form_input('cidade','Cidade <span style="color: #F00">*</span>',$devedor->DEVEDOR_CIDADE,'cidade'); ?></div>
                        <div class="col-md-3"><?php echo form_input('uf','UF <span style="color: #F00">*</span>',$devedor->DEVEDOR_UF,'uf'); ?></div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Negativar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'visualizar_negativacaoscpcpf': ?>
            <div class="text-right">
                <?php echo anchor('cliente/perfil/'.$negativacao->id_cliente_fk,'Voltar',array('class'=>'btn btn-default')); ?>
                <?php echo anchor('cliente/negativacao_refazer/'.$negativacao->id_negativacao,'Refazer Negativação',array('class'=>'btn btn-default')); ?>
            </div>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">Visualizar Negativação</div>
                <div class="panel-body">
                    <p><b>Retorno</b></p>
                    <p style="word-break: break-all"><?php echo $negativacao->retorno; ?></p>
                    <br>
                    <div class="table-responsive" style="margin: 0">
                        <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                            <thead>
                                <tr>
                                    <th>Natureza</th>
                                    <th class="text-right">Cod Natureza</th>
                                    <th class="text-right">Contrato</th>
                                    <th class="text-right">Nosso Número</th>
                                    <th class="text-center">Vencimento</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo cod_natureza_scpc($parametros->NATUREZA_OPERACAO); ?></td>
                                    <td class="text-right"><?php echo $parametros->NATUREZA_OPERACAO; ?></td>
                                    <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                                    <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                                    <td class="text-center"><?php echo $parametros->DATA_ATRASO; ?></td>
                                    <td class="text-right"><?php echo dinheiro(intval($parametros->VALOR)/100); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive" style="margin: 0">
                                <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                    <tr><td><b>CNPJ</b></td><td><?php echo $parametros->CNPJ_CREDOR; ?></td></tr>
                                    <tr><td><b>Nome Fantasia</b></td><td><?php echo $parametros->FANTASIA_CREDOR; ?></td></tr>
                                    <tr><td><b>Razão Social</b></td><td><?php echo $parametros->RAZAO_CREDOR; ?></td></tr>
                                    <tr><td><b>Telefone</b></td><td><?php echo $parametros->TELEFONE_CREDOR; ?></td></tr>
                                    <tr><td><b>E-mail</b></td><td><?php echo $parametros->EMAIL_CREDOR; ?></td></tr>
                                    <tr><td><b>Endereço</b></td><td><?php echo $parametros->ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Numero</b></td><td><?php echo $parametros->NUMERO_ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Complemento</b></td><td><?php echo $parametros->COMPLEMENTO_ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Bairro</b></td><td><?php echo $parametros->BAIRRO_CREDOR; ?></td></tr>
                                    <tr><td><b>Cidade</b></td><td><?php echo $parametros->CIDADE_CREDOR; ?></td></tr>
                                    <tr><td><b>UF</b></td><td><?php echo $parametros->UF_CREDOR; ?></td></tr>
                                    <tr><td><b>CEP</b></td><td><?php echo $parametros->CEP_CREDOR; ?></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive" style="margin: 0">
                                <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                    <tr><td><b>CPF/CNPJ</b></td><td><?php echo $parametros->DEVEDOR_CPF; ?></td></tr>
                                    <tr><td><b>Nome</b></td><td><?php echo $parametros->DEVEDOR_NOME; ?></td></tr>
                                    <tr><td><b>Endereço</b></td><td><?php echo $parametros->DEVEDOR_ENDERECO; ?></td></tr>
                                    <tr><td><b>Bairro</b></td><td><?php echo $parametros->DEVEDOR_BAIRRO; ?></td></tr>
                                    <tr><td><b>Cidade</b></td><td><?php echo $parametros->DEVEDOR_CIDADE; ?></td></tr>
                                    <tr><td><b>UF</b></td><td><?php echo $parametros->DEVEDOR_UF; ?></td></tr>
                                    <tr><td><b>CEP</b></td><td><?php echo $parametros->DEVEDOR_CEP; ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php break;
    case 'visualizar_negativacaoscpcpj': ?>
        <div class="text-right">
            <?php echo anchor('cliente/perfil/'.$negativacao->id_cliente_fk,'Voltar',array('class'=>'btn btn-default')); ?>
            <?php echo anchor('cliente/negativacao_refazer/'.$negativacao->id_negativacao,'Refazer Negativação',array('class'=>'btn btn-default')); ?>
        </div>
        <br>
        <div class="panel panel-default">
            <div class="panel-heading">Visualizar Negativação</div>
            <div class="panel-body">
                <div class="table-responsive" style="margin: 0">
                    <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                        <thead><tr><th>Retorno</th></tr></thead>
                        <tbody style="word-break: break-all"><tr><td><?php echo $negativacao->retorno; ?></td></tr></tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive" style="margin: 0">
                    <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                        <thead>
                        <tr>
                            <th>Natureza</th>
                            <th class="text-right">Cod Natureza</th>
                            <th class="text-right">Contrato</th>
                            <th class="text-right">Nosso Número</th>
                            <th class="text-center">Vencimento</th>
                            <th class="text-right">Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo cod_natureza_scpc($parametros->NATUREZA_OPERACAO); ?></td>
                            <td class="text-right"><?php echo $parametros->NATUREZA_OPERACAO; ?></td>
                            <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                            <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                            <td class="text-center"><?php echo $parametros->DATA_ATRASO; ?></td>
                            <td class="text-right"><?php echo dinheiro(intval($parametros->VALOR)/100); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive" style="margin: 0">
                            <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                <tr><td><b>CNPJ</b></td><td><?php echo $parametros->CNPJ_CREDOR; ?></td></tr>
                                <tr><td><b>Nome Fantasia</b></td><td><?php echo $parametros->FANTASIA_CREDOR; ?></td></tr>
                                <tr><td><b>Razão Social</b></td><td><?php echo $parametros->RAZAO_CREDOR; ?></td></tr>
                                <tr><td><b>Telefone</b></td><td><?php echo $parametros->TELEFONE_CREDOR; ?></td></tr>
                                <tr><td><b>E-mail</b></td><td><?php echo $parametros->EMAIL_CREDOR; ?></td></tr>
                                <tr><td><b>Endereço</b></td><td><?php echo $parametros->ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Numero</b></td><td><?php echo $parametros->NUMERO_ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Complemento</b></td><td><?php echo $parametros->COMPLEMENTO_ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Bairro</b></td><td><?php echo $parametros->BAIRRO_CREDOR; ?></td></tr>
                                <tr><td><b>Cidade</b></td><td><?php echo $parametros->CIDADE_CREDOR; ?></td></tr>
                                <tr><td><b>UF</b></td><td><?php echo $parametros->UF_CREDOR; ?></td></tr>
                                <tr><td><b>CEP</b></td><td><?php echo $parametros->CEP_CREDOR; ?></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="margin: 0">
                            <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                <tr><td><b>CPF/CNPJ</b></td><td><?php echo $parametros->DEVEDOR_CNPJ; ?></td></tr>
                                <tr><td><b>Nome</b></td><td><?php echo $parametros->DEVEDOR_RAZAO_SOCIAL; ?></td></tr>
                                <tr><td><b>Endereço</b></td><td><?php echo $parametros->DEVEDOR_ENDERECO; ?></td></tr>
                                <tr><td><b>Bairro</b></td><td><?php echo $parametros->DEVEDOR_BAIRRO; ?></td></tr>
                                <tr><td><b>Cidade</b></td><td><?php echo $parametros->DEVEDOR_CIDADE; ?></td></tr>
                                <tr><td><b>UF</b></td><td><?php echo $parametros->DEVEDOR_UF; ?></td></tr>
                                <tr><td><b>CEP</b></td><td><?php echo $parametros->DEVEDOR_CEP; ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php break;
    case 'baixar_negativacaoscpcpf': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-danger">
                <div class="panel-heading">Baixa de Negativação</div>
                <div class="panel-body">
                    <p><b>Deseja realmente efetuar a baixa desta negativação ? (este processo não pode ser desfeito).</b></p>
                </div>
                <div class="panel-footer text-right">
                    <?php echo form_submit('submit','Quero Baixar',array('class'=>'btn btn-danger')); ?>
                    <?php echo anchor('cliente/perfil/'.$negativacao->id_cliente_fk,'Voltar',array('class'=>'btn btn-default')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">Baixa de Negativação</div>
                <div class="panel-body">
                    <div class="table-responsive" style="margin: 0">
                        <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                            <thead>
                            <tr>
                                <th>Natureza</th>
                                <th class="text-right">Cod Natureza</th>
                                <th class="text-right">Contrato</th>
                                <th class="text-right">Nosso Número</th>
                                <th class="text-center">Vencimento</th>
                                <th class="text-right">Valor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php echo cod_natureza_scpc($parametros->NATUREZA_OPERACAO); ?></td>
                                <td class="text-right"><?php echo $parametros->NATUREZA_OPERACAO; ?></td>
                                <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                                <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                                <td class="text-center"><?php echo $parametros->DATA_ATRASO; ?></td>
                                <td class="text-right"><?php echo dinheiro(intval($parametros->VALOR)/100); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive" style="margin: 0">
                                <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                    <tr><td><b>CNPJ</b></td><td><?php echo $parametros->CNPJ_CREDOR; ?></td></tr>
                                    <tr><td><b>Nome Fantasia</b></td><td><?php echo $parametros->FANTASIA_CREDOR; ?></td></tr>
                                    <tr><td><b>Razão Social</b></td><td><?php echo $parametros->RAZAO_CREDOR; ?></td></tr>
                                    <tr><td><b>Telefone</b></td><td><?php echo $parametros->TELEFONE_CREDOR; ?></td></tr>
                                    <tr><td><b>E-mail</b></td><td><?php echo $parametros->EMAIL_CREDOR; ?></td></tr>
                                    <tr><td><b>Endereço</b></td><td><?php echo $parametros->ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Numero</b></td><td><?php echo $parametros->NUMERO_ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Complemento</b></td><td><?php echo $parametros->COMPLEMENTO_ENDERECO_CREDOR; ?></td></tr>
                                    <tr><td><b>Bairro</b></td><td><?php echo $parametros->BAIRRO_CREDOR; ?></td></tr>
                                    <tr><td><b>Cidade</b></td><td><?php echo $parametros->CIDADE_CREDOR; ?></td></tr>
                                    <tr><td><b>UF</b></td><td><?php echo $parametros->UF_CREDOR; ?></td></tr>
                                    <tr><td><b>CEP</b></td><td><?php echo $parametros->CEP_CREDOR; ?></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive" style="margin: 0">
                                <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                    <tr><td><b>CPF/CNPJ</b></td><td><?php echo $parametros->DEVEDOR_CPF; ?></td></tr>
                                    <tr><td><b>Nome</b></td><td><?php echo $parametros->DEVEDOR_NOME; ?></td></tr>
                                    <tr><td><b>Endereço</b></td><td><?php echo $parametros->DEVEDOR_ENDERECO; ?></td></tr>
                                    <tr><td><b>Bairro</b></td><td><?php echo $parametros->DEVEDOR_BAIRRO; ?></td></tr>
                                    <tr><td><b>Cidade</b></td><td><?php echo $parametros->DEVEDOR_CIDADE; ?></td></tr>
                                    <tr><td><b>UF</b></td><td><?php echo $parametros->DEVEDOR_UF; ?></td></tr>
                                    <tr><td><b>CEP</b></td><td><?php echo $parametros->DEVEDOR_CEP; ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php break;
    case 'baixar_negativacaoscpcpj': ?>
        <?php echo form_open(current_url()); ?>
            <div class="panel panel-danger">
                <div class="panel-heading">Baixa de Negativação</div>
                <div class="panel-body">
                    <p><b>Deseja realmente efetuar a baixa desta negativação ? (este processo não pode ser desfeito).</b></p>
                </div>
                <div class="panel-footer text-right">
                    <?php echo form_submit('submit','Quero Baixar',array('class'=>'btn btn-danger')); ?>
                    <?php echo anchor('cliente/perfil/'.$negativacao->id_cliente_fk,'Voltar',array('class'=>'btn btn-default')); ?>
                </div>
            </div>
        <?php echo form_close(); ?>
        <br>
        <div class="panel panel-default">
            <div class="panel-heading">Baixa de Negativação</div>
            <div class="panel-body">
                <div class="table-responsive" style="margin: 0">
                    <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                        <thead>
                        <tr>
                            <th>Natureza</th>
                            <th class="text-right">Cod Natureza</th>
                            <th class="text-right">Contrato</th>
                            <th class="text-right">Nosso Número</th>
                            <th class="text-center">Vencimento</th>
                            <th class="text-right">Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo cod_natureza_scpc($parametros->NATUREZA_OPERACAO); ?></td>
                            <td class="text-right"><?php echo $parametros->NATUREZA_OPERACAO; ?></td>
                            <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                            <td class="text-right"><?php echo $parametros->CONTRATO; ?></td>
                            <td class="text-center"><?php echo $parametros->DATA_ATRASO; ?></td>
                            <td class="text-right"><?php echo dinheiro(intval($parametros->VALOR)/100); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive" style="margin: 0">
                            <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                <tr><td><b>CNPJ</b></td><td><?php echo $parametros->CNPJ_CREDOR; ?></td></tr>
                                <tr><td><b>Nome Fantasia</b></td><td><?php echo $parametros->FANTASIA_CREDOR; ?></td></tr>
                                <tr><td><b>Razão Social</b></td><td><?php echo $parametros->RAZAO_CREDOR; ?></td></tr>
                                <tr><td><b>Telefone</b></td><td><?php echo $parametros->TELEFONE_CREDOR; ?></td></tr>
                                <tr><td><b>E-mail</b></td><td><?php echo $parametros->EMAIL_CREDOR; ?></td></tr>
                                <tr><td><b>Endereço</b></td><td><?php echo $parametros->ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Numero</b></td><td><?php echo $parametros->NUMERO_ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Complemento</b></td><td><?php echo $parametros->COMPLEMENTO_ENDERECO_CREDOR; ?></td></tr>
                                <tr><td><b>Bairro</b></td><td><?php echo $parametros->BAIRRO_CREDOR; ?></td></tr>
                                <tr><td><b>Cidade</b></td><td><?php echo $parametros->CIDADE_CREDOR; ?></td></tr>
                                <tr><td><b>UF</b></td><td><?php echo $parametros->UF_CREDOR; ?></td></tr>
                                <tr><td><b>CEP</b></td><td><?php echo $parametros->CEP_CREDOR; ?></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="margin: 0">
                            <table class="table table-hover table-condensed table-bordered" style="margin: 0">
                                <tr><td><b>CPF/CNPJ</b></td><td><?php echo $parametros->DEVEDOR_CNPJ; ?></td></tr>
                                <tr><td><b>Nome</b></td><td><?php echo $parametros->DEVEDOR_RAZAO_SOCIAL; ?></td></tr>
                                <tr><td><b>Endereço</b></td><td><?php echo $parametros->DEVEDOR_ENDERECO; ?></td></tr>
                                <tr><td><b>Bairro</b></td><td><?php echo $parametros->DEVEDOR_BAIRRO; ?></td></tr>
                                <tr><td><b>Cidade</b></td><td><?php echo $parametros->DEVEDOR_CIDADE; ?></td></tr>
                                <tr><td><b>UF</b></td><td><?php echo $parametros->DEVEDOR_UF; ?></td></tr>
                                <tr><td><b>CEP</b></td><td><?php echo $parametros->DEVEDOR_CEP; ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php break;
    case 'pastas': ?>
            <div class="text-right">
                <?php echo anchor('cliente/pasta_cadastrar','Cadastrar',array('class'=>'btn btn-success')); ?>
            </div>
            <br>
            <div class="panel panel-google">
                <div class="panel-heading">Pastas Cadastradas</div>
                <table class="panel-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th class="text-right">Criação</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pastas as $index => $p): ?>
                            <tr>
                                <td><?php echo $p->nome; ?></td>
                                <td><?php echo $p->descricao; ?></td>
                                <td class="text-right"><?php echo data_pt($p->criado_em); ?></td>
                                <td class="text-right">
                                    <?php echo anchor('cliente/pasta_subs/'.$p->id_cliente_pasta,'Visualizar'); ?> | 
                                    <?php echo anchor('cliente/pasta_relatorio_valores/'.$p->id_cliente_pasta,'Relatório'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'pasta_cadastrar': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados do Cadastro</div>
                    <div class="panel-body border-top">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" name="nome">
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" class="form-control" name="descricao">
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente_pastas','Voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Salvar',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'pasta_subs': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Adição de Cliente</div>
                    <div class="panel-body border-top">
                        <div class="form-group">
                            <label for="cliente">Cliente</label>
                            <select name="cliente" id="" class="form-control">
                                <?php foreach($clientes as $index => $c): ?>
                                    <option value="<?php echo $c->id_cliente; ?>"><?php echo strtoupper($c->nome_ou_fantasia); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente/pastas','voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Inscrever',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Clientes Cadastrados na Pasta</div>
                <table class="panel-table">
                    <thead>
                        <th class="text-right" style="width: 80px"></th>
                        <th>Nome</th>
                        <th class="text-right">Data</th>
                    </thead>
                    <tbody>
                        <?php foreach($subs as $i => $c): ?>
                            <tr>
                                <td class="text-right"><?php echo $i+1; ?></td>
                                <td title="<?php echo $c->razao_social; ?>"><?php echo $c->nome; ?></td>
                                <td class="text-right"><?php echo data_pt($c->criado_em,true); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
    case 'pasta_relatorio_valores': ?>
            <?php echo form_open(current_url()); ?>
                <div class="panel panel-google">
                    <div class="panel-heading">Dados p/ Geração do Relatório</div>
                    <div class="panel-body border-top">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="data">Data</label>
                                    <input type="text" class="form-control text-center" name="data" value="<?php echo date('m/Y'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php echo anchor('cliente/pastas','Voltar',array('class'=>'btn btn-default')); ?>
                        <?php echo form_submit('submit','Emitir',array('class'=>'btn btn-success')); ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php break;
    case 'ultimas_aberturas': ?>
            <?php $status = array(0=>'Cancelado',1=>'Ativo',2=>'Bloqueado'); ?>
            <div class="panel panel-google">
                <div class="panel-heading">Dados de Clientes</div>
                <div class="table-responsive">
                    <table class="panel-table table-striped table-hover tabela_pesquisavel">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Franquia</th>
                                <th>Consultor</th>
                                <th class="w-min350">Nome</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Mens.</th>
                                <th class="text-right">Vcto</th>
                                <th class="text-center">Criação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $i => $c): ?>
                                <tr>
                                    <td class="text-right"><?php echo $i+1; ?></td>
                                    <td class="text-center">
                                        <?php if($c->consultor==1){ echo '<i class="fa fa-suitcase"></i>'; } ?>
                                    </td>
                                    <td><small><?php
                                        if($c->id_franquia_fk!=0){
                                            $n_arr = explode(" ",$c->franquia);
                                            echo $n_arr[0];
                                        }
                                    ?></small></td>
                                    <td><small><?php
                                        if($c->id_consultor_fk>3){
                                            $n_arr = explode(" ",$c->consultor_nome);
                                            echo $n_arr[0];
                                        }
                                    ?></small></td>
                                    <td><?php echo anchor('cliente/perfil/'.$c->id_cliente,strtoupper($c->nome_ou_fantasia)); ?></td>
                                    <td class="text-center"><?php echo $status[$c->status]; ?></td>
                                    <td class="text-right"><?php echo dinheiro($c->mensalidade); ?></td>
                                    <td class="text-right"><?php echo $c->dia_vencimento; ?></td>
                                    <td class="text-center"><?php echo data_pt($c->criado_em); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php break;
endswitch;



