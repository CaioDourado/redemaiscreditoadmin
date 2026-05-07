<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'gerenciar': ?>
        <div class="ui segment">
            <h4> <i class="user icon "></i> Lista de Clientes</h4>
        </div>
        <p style="text-align: right;"><?php echo anchor('user/create','<i class="plus icon"></i> Cliente',array('class'=>'ui button green')); ?></p>
        <table class="ui definition selectable table small">
            <thead>
            <tr>
                <th></th>
                <th>Nome</th>
                <th>Documento</th>
                <th>Tipo</th>
                <th style="text-align: center;">Consultor</th>
                <th style="text-align: center;">Plano</th>
                <th style="text-align: center;">Taxa Extra</th>
                <th style="text-align: center;">Ativo</th>
                <th style="text-align: center;">Negativar</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($users as $index => $user){
                echo '<tr>';
                echo '<td style="text-align: right;">'.$user->id.'</td>';
                echo '<td>'.anchor('user/read/'.$user->id,$user->name).'</td>';
                echo '<td>'.$user->document.'</td>';
                echo '<td>'.get_client_type($user->consultant).'</td>';
                echo '<td>'. ( $user->salesConsultant ? $user->salesConsultant .' - '. $user->nameconsultant : '' ) .'</td>';
                echo '<td style="text-align: center;">'. ($user->idplan ? $user->idplan .' - ' : '') . $user->nameplan .'</td>';
                echo '<td style="text-align: center;">'.get_month($user->taxmonth).'</td>';
                echo '<td style="text-align: center;">'.get_icon($user->enabled,true).'</td>';
                echo '<td style="text-align: center;">'.get_icon($user->syncId > 0,true).'</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <?php break;
    case 'cadastrar': ?>
                <?php echo form_open(current_url()); ?>
                <div class="row">
                    <div class="col-md-3"><?php echo form_select('consultant','Tipo',array('0'=>'Cliente','1'=>'Consultor')); ?></div>
                    <div class="col-md-3"><?php echo form_select('salesConsultant','Consultor',$consultores); ?></div>
                    <div class="col-md-3"><?php echo form_select('enabled','Situação',array('1'=>'Ativo','0'=>'Bloqueado')); ?></div>
                    <div class="col-md-3"><?php echo form_select('admin','Administrador',array('0'=>'Não','1'=>'Sim')); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><?php echo form_select('legalPerson','Tipo de Pessoa',array('1'=>'Física','2'=>'Jurídica')); ?></div>
                    <div class="col-md-3"><?php echo form_input('document','CPF/CNPJ','') ?></div>
                    <div class="col-md-3"><?php echo form_input('birthdate','Data Nascimento','','data text-center','<i class="fa fa-calendar"></i>') ?></div>
                    <div class="col-md-3"><?php echo form_input('rg','Doc. Identidade','','text-center') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><?php echo form_input('name','Nome do Cliente',''); ?></div>
                    <div class="col-md-6"><?php echo form_input('tradingName','Razão Social',''); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><?php echo form_input('email','E-mail','','','<i class="fa fa-at"></i>'); ?></div>
                    <div class="col-md-3"><?php echo form_input('telephone','Telefone','','telefone text-right','<i class="fa fa-phone"></i>'); ?></div>
                    <div class="col-md-3"><?php echo form_input('mobilePhone','Celular','','celular text-right','<i class="fa fa-mobile-phone"></i>'); ?></div>
                </div>
                <hr>

                <?php
                /*
                echo '<div class="four fields">';
                echo form_field_dropdown('Situação','enabled',array('1'=>'Ativo','0'=>'Bloqueado'),1);
                echo form_field_dropdown('Administrador','admin',array('1'=>'Sim','0'=>'Não'),0);
                echo form_field_dropdown('Tipo de Pessoa','legalPerson',array('1'=>'Física','2'=>'Jurídica'),2);
                echo '</div>';
                echo '<div class="two fields">';
                echo form_field_input_required('Nome do Cliente','name','Nome do Cliente');
                echo form_field_input('Razão Social','tradingName','Razão Social');
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input('CPF/CNPJ','document','CPF/CNPJ');
                echo form_field_input('Data de Nascimento','birthdate','Data de Nascimento');
                echo form_field_input('Doc. de Identidade','rg','Documento de Identidade');
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input_required('E-mail','email','E-mail');
                echo form_field_input('Telefone','telephone','Telefone');
                echo form_field_input('Celular','mobilePhone','Celular');
                echo '</div>';
                $mes_escolhido = rand(1,12);
                $array_aux = get_month_in_array();
                $array_aux[0] = 'Sem Taxa';
                echo '<div class="two fields">';
                echo form_field_dropdown('Taxa Extra','taxMonth',$array_aux,$mes_escolhido);
                echo form_field_dropdown('Planos','userplan',$this->plan->get_all_inarray_enable(),2);
                echo '</div>';
                echo form_field_textarea('Observações','observation',5);
                echo '<h4 class="ui dividing header">ENDEREÇO</h4>';
                echo '<div class="fields">';
                echo form_field_input('CEP','addressCep','CEP');
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input('Logradouro','addressStreet','Logradouro');
                echo form_field_input('Número','addressNumber','Número');
                echo form_field_input('Complemento','addressComplement','Complemento');
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input('Bairro','addressDistrict','Bairro');
                echo form_field_input('Cidade','addressCity','Cidade');
                echo form_field_dropdown('Estado','addressState',get_status_in_array(),'Estado');
                echo '</div>';
                */
                ?>
                <p class="text-right">
                    <?php echo anchor('usuario/gerenciar','Cancelar',array('class'=>'btn btn-default')); ?>
                    <?php echo form_submit('submit','Enviar',array('class'=>'btn btn-success')); ?>
                </p>
                <?php echo form_close(); ?>
        <?php break;
    case 'alterar': ?>
        <div class="ui segments">
            <div class="ui segment">
                <h4> <i class="user icon"></i> Cadastrar Cliente</h4>
            </div>
            <div class="ui segment">
                <?php echo form_open(current_url(),array('class'=>'ui form')); ?>
                <?php
                echo '<div class="four fields">';
                echo form_field_dropdown('Situação','enabled',array('0'=>'Bloqueado','1'=>'Ativo'),$user->enabled);
                echo form_field_dropdown('Administrador','admin',array('0'=>'Não','1'=>'Sim'),$user->admin);
                echo form_field_dropdown('Tipo de Cliente','consultant',array('0'=>'Cliente','1'=>'Consultor'),$user->consultant);
                echo form_field_dropdown('Tipo de Pessoa','legalPerson',array('1'=>'Física','2'=>'Jurídica'),$user->legalPerson);
                echo '</div>';
                echo '<div class="two fields">';
                echo form_field_input_required('Nome do Cliente','name','Nome do Cliente',$user->name);
                echo form_field_dropdown('Consultor','salesConsultant',$this->user->get_consultants_array(),$user->salesConsultant);
                echo '</div>';
                echo form_field_input('Razão Social','tradingName','Razão Social',$user->tradingName);
                echo '<div class="three fields">';
                echo form_field_input('CPF/CNPJ','document','CPF/CNPJ',$user->document);
                echo form_field_input('Data de Nascimento','birthdate','Data de Nascimento',date_pt($user->birthdate));
                echo form_field_input('Doc. de Identidade','rg','Documento de Identidade',$user->rg);
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input_required('E-mail','email','E-mail',$user->email);
                echo form_field_input('Telefone','telephone','Telefone',$user->telephone);
                echo form_field_input('Celular','mobilePhone','Celular',$user->mobilePhone);
                echo '</div>';
                $array_aux = get_month_in_array();
                $array_aux[0] = 'Sem Taxa';
                echo '<div class="two fields">';
                echo form_field_dropdown('Taxa Extra','taxMonth',$array_aux,$plan ? $plan->taxMonth : '');
                echo form_field_dropdown('Planos','userplan',$this->plan->get_all_inarray_enable(), $plan ? $plan->plan : '');
                echo '</div>';
                echo form_field_textarea('Observações','observation',5,$user->observation);
                echo '<h4 class="ui dividing header">ENDEREÇO</h4>';
                echo '<div class="fields">';
                echo form_field_input('CEP','addressCep','CEP',$user->addressCep);
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input('Logradouro','addressStreet','Logradouro',$user->addressStreet);
                echo form_field_input('Número','addressNumber','Número',$user->addressNumber);
                echo form_field_input('Complemento','addressComplement','Complemento',$user->addressComplement);
                echo '</div>';
                echo '<div class="three fields">';
                echo form_field_input('Bairro','addressDistrict','Bairro',$user->addressDistrict);
                echo form_field_input('Cidade','addressCity','Cidade',$user->addressCity);
                echo form_field_dropdown('Estado','addressState',get_status_in_array(),$user->addressState);
                echo '</div>';
                ?>
                <h4 class="ui horizontal divider header" style="margin-bottom: 0">
                    <i class="arrow down icon"></i>
                    Finalização
                </h4>
                <p style="margin: 0;text-align: right;">
                    <?php echo anchor('user/read/'.$user->id,'Cancelar',array('class'=>'ui button white')); ?>
                    <?php echo form_submit('submit','Enviar',array('class'=>'ui button blue')); ?>
                    <?php echo form_hidden('id',$user->id); ?>
                    <?php echo form_hidden('id_userplan',$plan ? $plan->id : ''); ?>
                </p>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php break;
    case 'perfil': ?>
        <p class="text-right">
            <?php echo anchor('user/negative/'.$user->id,'<i class="minus icon"></i> Negativar',array('class'=>'ui button orange','style'=>'float: left')); ?>
            <?php echo anchor('user/consultations/'.$user->id,'<i class="file text outline icon"></i> Consultas',array('class'=>'ui button blue','style'=>'float: left')); ?>
            <?php echo anchor('user/manage','<i class="arrow left icon"></i> Cancelar',array('class'=>'ui button white')); ?>
            <?php echo anchor('user/update/'.$user->id,'<i class="pencil icon"></i> Alterar',array('class'=>'ui button yellow')); ?>
            <?php
            if($user->enabled==0)
                echo anchor('user/awake/'.$user->id,'<i class="close icon"></i> Reativar',array('class'=>'ui button green'));
            else
                echo anchor('user/delete/'.$user->id,'<i class="close icon"></i> Desativar',array('class'=>'ui button red'));
            ?>
        </p>
        <h3 class="ui header dividing">
            <?php echo $user->id.' - '.$user->name; ?>
            <small style="float: right">
                <?php if($user->enabled==1) echo '<span style="color: #21ba45">Ativo</span> | '; else echo '<span style="color: #db2828">Inativo</span> | '; ?>
                <?php if($user->admin==1) echo 'Administrador | '; ?>
                <?php if($user->consultant==1) echo 'Consultor | '; else echo 'Cliente | '; ?>
                <?php if($user->legalPerson==2) echo 'Pessoa Jurídica'; else echo 'Pessoa Física'; ?>
            </small>
        </h3>
        <p style="text-align: right;"><i>Criado em <?php echo date_pt($user->inserted,true); ?></i></p>
        <p><b>Obs.: </b><?php echo $user->observation; ?></p>
        <div class="ui grid">
            <div class="eight wide column">
                <table class="ui single line selectable table table">
                    <thead>
                    <tr>
                        <th colspan="2" style="text-align: center;">Dados Pessoais</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td><b>Nome</b></td><td class="text-right"><?php echo $user->name; ?></td></tr>
                    <tr><td><b>Razão Social</b></td><td class="text-right"><?php echo $user->tradingName; ?></td></tr>
                    <tr><td><b>Tipo usuário</b></td><td class="text-right"><?php echo $user->admin ? '<b>admin</b>' : ($user->consultant ? '<b>consultor</b>' : 'cliente'); ?></td></tr>
                    <tr><td><b>Consultor</b></td><td class="text-right"><?php echo $user->salesConsultant ? ($user->salesConsultant .' - '. $salesConsultant->name) : '-'; ?></td></tr>
                    <tr><td><b>CPF/CNPJ</b></td><td class="text-right"><?php echo $user->document; ?></td></tr>
                    <tr><td><b>Doc. Identidade</b></td><td class="text-right"><?php echo $user->rg; ?></td></tr>
                    <tr><td><b>Data Nascimento</b></td><td class="text-right"><?php echo date_pt($user->birthdate); ?></td></tr>
                    <tr><td><b>E-mail</b></td><td class="text-right"><?php echo $user->email; ?></td></tr>
                    <tr><td><b>Telefone</b></td><td class="text-right"><?php echo $user->telephone; ?></td></tr>
                    <tr><td><b>Celular</b></td><td class="text-right"><?php echo $user->mobilePhone; ?></td></tr>
                    </tbody>
                </table>
                <table class="ui single line selectable table table">
                    <thead>
                    <tr>
                        <th colspan="2" style="text-align: center;">Endereço</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td><b>Logradouro</b></td><td class="text-right"><?php echo $user->addressStreet; ?></td></tr>
                    <tr><td><b>Número</b></td><td class="text-right"><?php echo $user->addressNumber; ?></td></tr>
                    <tr><td><b>Complemento</b></td><td class="text-right"><?php echo $user->addressComplement; ?></td></tr>
                    <tr><td><b>Bairro</b></td><td class="text-right"><?php echo $user->addressDistrict; ?></td></tr>
                    <tr><td><b>Cidade</b></td><td class="text-right"><?php echo $user->addressCity; ?></td></tr>
                    <tr><td><b>Estado</b></td><td class="text-right"><?php echo $user->addressState; ?></td></tr>
                    <tr><td><b>CEP</b></td><td class="text-right"><?php echo $user->addressCep; ?></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="eight wide column">
                <?php if($plan!=NULL){ ?>
                    <table class="ui single line selectable table table">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="2">Dados do Plano</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td><b>Id</b></td><td class="text-right"><?php echo $plan->plan; ?></td></tr>
                        <tr><td><b>Nome</b></td><td class="text-right"><?php echo $plan->nome; ?></td></tr>
                        <tr><td><b>Ativo</b></td><td class="text-right"><?php echo $plan->enabled ? 'sim' : '<b>não</b>'; ?></td></tr>
                        <tr><td><b>Taxa Anual</b></td><td class="text-right"><?php echo $plan->taxMonth ? get_month_in_array()[$plan->taxMonth] : '-'; ?></td></tr>
                        <tr><td><b>Data de Inserção</b></td><td class="text-right"><?php echo date_pt($plan->inserted, true); ?></td></tr>
                        <tr><td><b>Data de Alteração</b></td><td class="text-right"><?php echo date_pt($plan->updated, true); ?></td></tr>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if($session!=NULL){ ?>
                    <table class="ui single line selectable table table">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="2">Dados de Sessão</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td><b>UUID</b></td><td class="text-right"><?php echo $session->uuid; ?></td></tr>
                        <tr><td><b>Data de Expiração</b></td><td class="text-right"><?php echo date_pt($session->expire, true); ?></td></tr>
                        <tr><td><b>Data de Inserção</b></td><td class="text-right"><?php echo date_pt($session->inserted, true); ?></td></tr>
                        <tr><td><b>Data de Alteração</b></td><td class="text-right"><?php echo date_pt($session->updated, true); ?></td></tr>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if($negative!=NULL){ ?>
                    <table class="ui single line selectable table table">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="2">Dados de Negativação</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td><b>Código</b></td><td class="text-right"><?php echo $negative->sicId; ?></td></tr>
                        <tr><td><b>Chave</b></td><td class="text-right"><?php echo $negative->hash; ?></td></tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
        <div class="ui grid centered">
            <div class="fifteen wide column">
                <a id="annotation"></a>
                <p style="text-align: right;"><?php echo anchor('user/create_annotation/'.$user->id,'<i class="plus icon"></i> Anotação',array('class'=>'ui button green')); ?></p>
                <?php if($annotations!=NULL){ ?>
                    <table class="ui single line selectable table table">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="2">Anotações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($annotations as $index => $annotation): ?>
                            <tr>
                                <td>
                                    <?php echo '<strong>'. date_pt($annotation->inserted,true) .' &nbsp;-&nbsp; '. $annotation->userAnnotationName .'</strong>'; ?> <br>
                                    <pre><?php echo $annotation->annotation; ?></pre>
                                </td>
                            </tr>
                        <?php endforeach;
                        ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
        <?php break;
    case 'consultas': ?>
        <p class="text-right">
            <?php echo anchor('user/read/'.$user->id,'<i class="arrow left icon"></i> Cancelar',array('class'=>'ui button white')); ?>
            <?php
            if($user->enabled==0)
                echo anchor('user/awake/'.$user->id,'<i class="close icon"></i> Reativar',array('class'=>'ui button green'));
            else
                echo anchor('user/delete/'.$user->id,'<i class="close icon"></i> Desativar',array('class'=>'ui button red'));
            ?>
        </p>
        <h3 class="ui header dividing">
            <?php echo $user->id.' - '.$user->name; ?>
            <small style="float: right">
                <?php if($user->enabled==1) echo '<span style="color: #21ba45">Ativo</span> | '; else echo '<span style="color: #db2828">Inativo</span> | '; ?>
                <?php if($user->admin==1) echo 'Administrador | '; ?>
                <?php if($user->consultant==1) echo 'Consultor | '; else echo 'Cliente | '; ?>
                <?php if($user->legalPerson==2) echo 'Pessoa Jurídica'; else echo 'Pessoa Física'; ?>
            </small>
        </h3>
        <p style="text-align: right;"><i>Criado em <?php echo date_pt($user->inserted,true); ?></i></p>
        <p><b>Obs.: </b><?php echo $user->observation; ?></p>
        <h2 class="ui header dividing" style="color: #000099">Consultas</h2>
        <table class="ui definition striped table small">
            <thead>
            <tr>
                <th></th>
                <th>Consulta</th>
                <th>Data</th>
                <th>IP</th>
                <th>Dados</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($consultations as $index => $consultation){
                echo '<tr>';
                echo '<td valign="top" style="text-align: right;">' . $consultation->id . '</td>';
                echo '<td valign="top">' . $consultation->consultationName . '</td>';
                echo '<td valign="top">' . date_pt($consultation->date, true) . '</td>';
                echo '<td valign="top">' . $consultation->ip . '</td>';
//				echo '<td>' . $consultation->params . '</td>';

                echo '<td>';
                $arr = json_decode($consultation->params, true); //i prefer associative array in this context
                echo "<table class=\"ui table\">";
                foreach($arr as $k=>$v)
                    echo "<tr><td>$k</td><td>$v</td></tr>";
                echo "</table>";
                echo '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <?php break;
    case 'excluir': ?>
        <div class="ui segment inverted orange">
            <h4><i class="close icon"></i> Desativação de Cliente </h4>

            <p>Atenção, você pediu a desativação do cliente:</p>
            <h2 style="text-align: center;"><b><?php echo $user->name ?> - <?php echo $user->id; ?></b></h2>
            <p>A partir do momento que o cliente for desativado, o mesmo não conseguirá mais:</p>
            <ul>
                <li>Acessar o Sistema</li>
                <li>Efetuar Consultas</li>
                <li>Negativar Pessoas</li>
            </ul>
            <p>Deseja realmente continuar com o procedimento ?</p>
            <p style="text-align: right;">
                <?php echo anchor('user/read/'.$user->id,'cancelar',array('class'=>'ui button white')) ?>
                <?php echo anchor('user/delete_confirm/'.$user->id,'Confirmar Desativação',array('class'=>'ui button red')) ?>
            </p>
        </div>
        <?php break;
    case 'reativar': ?>
        <div class="ui segment inverted teal">
            <h4><i class="close icon"></i> Reativação de Cliente </h4>

            <p>Atenção, você pediu a reativação do cliente:</p>
            <h2 style="text-align: center;"><b><?php echo $user->name ?> - <?php echo $user->id; ?></b></h2>
            <p>A partir do momento que o cliente for reativado, o mesmo poderá:</p>
            <ul>
                <li>Acessar o Sistema</li>
                <li>Efetuar Consultas</li>
                <li>Negativar Pessoas</li>
            </ul>
            <p>Deseja realmente continuar com o procedimento ?</p>
            <p style="text-align: right;">
                <?php echo anchor('user/read/'.$user->id,'cancelar',array('class'=>'ui button white')) ?>
                <?php echo anchor('user/awake_confirm/'.$user->id,'Confirmar Reativação',array('class'=>'ui button green')) ?>
            </p>
        </div>
        <?php break;
    case 'negativar': ?>
        <h4><i class="minus icon"></i> Negativações </h4>
        <br>
        <table class="ui single line selectable table table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Cod.</th>
                <th>Chave</th>
                <th>Data</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($negatives as $index => $negative): ?>
                <tr>
                    <td><?php echo $negative->id; ?></td>
                    <td><?php echo $negative->user; ?></td>
                    <td><?php echo $negative->sicId; ?></td>
                    <td><?php echo $negative->hash; ?></td>
                    <td><?php echo $negative->inserted; ?></td>
                </tr>
            <?php endforeach;
            ?>
            </tbody>
        </table>

        <?php break;
    case 'criar_anotacao': ?>
        <div class="ui segments">
            <div class="ui segment">
                <h4> <i class="unordered list icon"></i> Cadastrar anotação</h4>
            </div>
            <div class="ui segment">
                <?php echo form_open(current_url(),array('class'=>'ui form')); ?>
                <?php
                echo '<div class="field">';
                echo form_field_textarea('Anotações','annotation',25);
                echo '</div>';
                ?>
                <h4 class="ui horizontal divider header" style="margin-bottom: 0">
                    <i class="arrow down icon"></i>
                    Finalização
                </h4>
                <p style="margin: 0;text-align: right;">
                    <?php echo anchor('user/read/'. $user->id .'#annotation','Cancelar',array('class'=>'ui button white')); ?>
                    <?php echo form_submit('submit','Enviar',array('class'=>'ui button blue')); ?>
                </p>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php break;
    default:
        break;
endswitch;
