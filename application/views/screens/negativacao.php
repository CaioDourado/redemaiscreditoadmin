<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Negativações PEFIN</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Negativado</th>
                                    <th>Data</th>
                                    <th class="text-center">Valor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($negativacoes as $index => $negativacao): ?>
                                    <?php $dt = json_decode($negativacao->parametros); ?>
                                    <tr>
                                        <td><?php echo truncate($negativacao->cliente,20); ?></td>
                                        <td>
                                            <?php if(isset($dt->CPF_DEVEDOR)):
                                                echo $dt->CPF_DEVEDOR;
                                            else:
                                                echo $dt->CNPJ_DEVEDOR;
                                            endif; ?>
                                        </td>
                                        <td><?php echo $dt->NOME_DEVEDOR; ?></td>
                                        <td><?php echo $dt->VENCIMENTO_DIVIDA; ?></td>
                                        <td class="text-right"><?php echo $dt->VALOR_DIVIDA; ?></td>
                                        <td>
                                            <?php echo anchor('negativacao/conversao/'.$negativacao->id_negativacao,'<i class="fa fa-repeat"></i>',array('class'=>'btn btn-info btn-xs')); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php break;
    case 'conversao': ?>
            <?php echo form_open(current_url()); ?>
            <div class="panel panel-blue">
                <div class="panel-heading">Conversão de Negativação Pefin => Varejo</div>
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
                        <div class="col-md-3"><?php echo form_input('cpf','CPF <span style="color: #F00">*</span>',$devedor->CPF_DEVEDOR,'text-right cpf'); ?></div>
                        <div class="col-md-4"><?php echo form_input('nome','Nome <span style="color: #F00">*</span>',$devedor->NOME_DEVEDOR); ?></div>
                        <div class="col-md-5"><?php echo form_select('natureza','Natureza da Negativação <span style="color: #F00">*</span>',cod_natureza_scpc(),'',13); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('vencimento_inicio','Venc. Inicial<span style="color: #F00">*</span>',$devedor->VENCIMENTO_DIVIDA,'text-right data data5anos_validate obrigatorio'); ?></div>
                        <div class="col-md-3"><?php echo form_input('vencimento_fim','Venc. Final<span style="color: #F00">*</span>',$devedor->VENCIMENTO_DIVIDA,'text-right data'); ?></div>
                        <div class="col-md-2"><?php echo form_input('parcelas','Parcelas <span style="color: #F00">*</span>',1,'text-right'); ?></div>
                        <div class="col-md-2"><?php echo form_input('valor','Valor <span style="color: #F00">*</span>',$devedor->VALOR_DIVIDA,'text-right dinheiro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('contrato','Contrato <span style="color: #F00">*</span>','000001','text-right'); ?></div>
                        <!--div class="col-md-3"><?php //echo form_input('nosso_numero','Nosso Número','','text-right'); ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('data_nascimento','Data de Nascimento <span style="color: #F00">*</span>',$devedor->DATA_NASC,'data text-center'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"><?php echo form_input('cep','CEP <span style="color: #F00">*</span>',$devedor->CEP_CREDOR,'cep cep_validate obrigatorio'); ?></div>
                        <div class="col-md-10"><?php echo form_input('logradouro','Endereço <span style="color: #F00">*</span>',$devedor->ENDERECO_CREDOR.' '.$devedor->NUMERO_ENDERECO_CREDOR,'logradouro'); ?></div>
                        <!--div class="col-md-2"><?php echo form_input('numero','Número <span style="color: #F00">*</span>'); ?></div>
                        <div class="col-md-2"><?php echo form_input('complemento','Complemento <span style="color: #F00">*</span>'); ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?php echo form_input('bairro','Bairro <span style="color: #F00">*</span>',$devedor->BAIRRO_CREDOR,'bairro'); ?></div>
                        <div class="col-md-3"><?php echo form_input('cidade','Cidade <span style="color: #F00">*</span>',$devedor->CIDADE_CREDOR,'cidade'); ?></div>
                        <div class="col-md-3"><?php echo form_input('uf','UF <span style="color: #F00">*</span>',strtoupper($devedor->UF_CREDOR),'uf'); ?></div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <?php echo anchor('cliente/perfil/'.$cliente->id_cliente,'Voltar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Negativar',array('class'=>'btn btn-success')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        <?php break;
endswitch;
