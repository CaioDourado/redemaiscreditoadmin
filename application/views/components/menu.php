<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($menu):
    case 'padrao_novo': ?>
            <?php //echo anchor('inicio','<i class="fa fa-home"></i> Início',array('class'=>'item_solto')); ?>
            <?php echo anchor('portifolio','<i class="fa fa-list"></i> Portifolio',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente','<i class="fa fa-user-circle"></i> Clientes',array('class'=>'item_solto')); ?>
            <?php echo anchor('representante','<i class="fa fa-suitcase"></i> Representante',array('class'=>'item_solto')); ?>
            <?php echo anchor('fornecedor','<i class="fa fa-truck"></i> Fornecedor',array('class'=>'item_solto')); ?>
            <?php echo anchor('usuario','<i class="fa fa-users"></i> Usuários',array('class'=>'item_solto')); ?>
            <?php echo anchor('consulta','<i class="fa fa-search"></i> Consultas',array('class'=>'item_solto')); ?>
            <?php echo anchor('boleto','<i class="fa fa-barcode"></i> Boletos',array('class'=>'item_solto')); ?>
            <?php echo anchor('fatura','<i class="fa fa-file-text"></i> Faturas',array('class'=>'item_solto')); ?>
            <?php echo anchor('franquia','<i class="fa fa-industry"></i> Franquias',array('class'=>'item_solto')); ?>
            <?php echo anchor('arquivo','<i class="fa fa-file"></i> Arquivos',array('class'=>'item_solto')); ?>
            <?php echo anchor('relatorio','<i class="fa fa-bar-chart"></i> Relatorios',array('class'=>'item_solto')); ?>
            <?php echo anchor('operacao','<i class="fa fa-heartbeat"></i> Operacao',array('class'=>'item_solto')); ?>
            <?php echo anchor('operacao/top3','<i class="fa fa-list-ol"></i> Top 3',array('class'=>'item_solto')); ?>
        <?php break;
    case 'padrao': ?>
            <?php switch($this->session->userdata('adm_nivel')){
                case '1': ?>
                    <div class="topo">Administração</div>
                    <?php echo anchor('diario','Diario',array('class'=>'item')); ?>
                    <?php echo anchor('cliente','Clientes',array('class'=>'item')); ?>
                    <?php echo anchor('usuario','Usuários',array('class'=>'item')); ?>
                    <?php echo anchor('boleto','Boletos',array('class'=>'item')); ?>
                <?php break;
                case '2': ?>
                    <div class="topo">Administração</div>
                    <?php echo anchor('diario','Diario',array('class'=>'item')); ?>
                    <?php echo anchor('contasapagar','Contas a Pagar',array('class'=>'item')); ?>
                    <?php echo anchor('cliente','Clientes',array('class'=>'item')); ?>
                    <?php echo anchor('fornecedor','Fornecedor',array('class'=>'item')); ?>
                    <?php echo anchor('usuario','Usuários',array('class'=>'item')); ?>
                    <?php echo anchor('consulta','Consultas',array('class'=>'item')); ?>
                    <?php echo anchor('boleto','Boletos',array('class'=>'item')); ?>
                    <?php echo anchor('fatura','Fatura',array('class'=>'item')); ?>
                    <?php echo anchor('faturamento','Faturamento',array('class'=>'item')); ?>
                    <?php echo anchor('negativacao','Negativação',array('class'=>'item')); ?>
                    <?php echo anchor('plano','Planos',array('class'=>'item')); ?>
                    <?php echo anchor('relatorio','Relatórios',array('class'=>'item')); ?>
                    <?php echo anchor('arquivo','Arquivos',array('class'=>'item')); ?>
                <?php break;
                case '3': ?>
                    <div class="topo">Administração</div>
                    <?php echo anchor('diario','Diario',array('class'=>'item')); ?>
                    <?php echo anchor('contasapagar','Contas a Pagar',array('class'=>'item')); ?>
                    <?php echo anchor('cliente','Clientes',array('class'=>'item')); ?>
                    <?php echo anchor('fornecedor','Fornecedor',array('class'=>'item')); ?>
                    <?php echo anchor('usuario','Usuários',array('class'=>'item')); ?>
                    <?php echo anchor('consulta','Consultas',array('class'=>'item')); ?>
                    <?php echo anchor('boleto','Boletos',array('class'=>'item')); ?>
                    <?php echo anchor('fatura','Fatura',array('class'=>'item')); ?>
                    <?php echo anchor('faturamento','Faturamento',array('class'=>'item')); ?>
                    <?php echo anchor('negativacao','Negativação',array('class'=>'item')); ?>
                    <?php echo anchor('plano','Planos',array('class'=>'item')); ?>
                    <?php echo anchor('relatorio','Relatórios',array('class'=>'item')); ?>
                    <?php echo anchor('arquivo','Arquivos',array('class'=>'item')); ?>
                <?php break; ?>
            <?php } ?>
        <?php break;
    case 'franquia': ?>
            <!--div class="titulo">Opções de Franquia</div-->
            <?php //echo anchor('inicio','<i class="fa fa-home"></i> Início',array('class'=>'item_solto')); ?>
            <?php echo anchor('franquia','<i class="fa fa-industry"></i> Franquias',array('class'=>'item_solto')); ?>
            <?php echo anchor('franquia/cadastrar','<i class="fa fa-plus"></i> Cadastrar Franquia',array('class'=>'item_solto')); ?>
        <?php break;
    case 'fornecedores_novo': ?>
            <?php echo anchor('fornecedor','Gerenciar',array('class'=>'item_solto')); ?>
            <?php echo anchor('fornecedor/cadastrar','Cadastrar',array('class'=>'item_solto')); ?>
            <?php echo anchor('inicio','Voltar',array('class'=>'item_solto')); ?>
        <?php break;
    case 'fornecedores': ?>
            <div class="topo">Fornecedor</div>
            <?php echo anchor('fornecedor','Gerenciar',array('class'=>'item')); ?>
            <?php echo anchor('fornecedor/cadastrar','Cadastrar',array('class'=>'item')); ?>
            <?php echo anchor('inicio','Voltar',array('class'=>'item')); ?>
        <?php break;
    case 'fornecedor_perfil': ?>
            <div class="titulo"><?php echo $fornecedor->nome; ?></div>
            <?php echo anchor('fornecedor/adicionar_consulta/'.$fornecedor->id_fornecedor,'<i class="fa fa-plus-circle"></i> Adicionar Consulta',array('class'=>'item')); ?>
            <?php echo anchor('fornecedor/alterar/'.$fornecedor->id_fornecedor,'<i class="fa fa-pencil"></i> Alterar',array('class'=>'item')); ?>
            <?php echo anchor('fornecedor/excluir/'.$fornecedor->id_fornecedor,'<i class="fa fa-close"></i> Excluir',array('class'=>'item')); ?>
            <?php //echo anchor('fornecedor','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'clientes': ?>
            <?php echo anchor('cliente','Gerenciar',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/gerenciar_por_area','Gerenciar por Área',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/cadastrar','Cadastrar',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/email_geral','E-mail Geral',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/pastas','Pastas',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/ultimas_aberturas','Últimas Aberturas',array('class'=>'item_solto')); ?>
        <?php break;
    case 'cliente_perfil': ?>
            <?php echo anchor('cliente/alterar/'.$cliente->id_cliente,'Alterar',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/produtos_valores/'.$cliente->id_cliente,'Produtos e Valores',array('class'=>'item_solto')); ?>
            <?php echo anchor('cliente/gestao_negativacao/'.$cliente->id_cliente,'Gestão de Negativação',array('class'=>'item_solto')); ?>
            <?php //echo anchor('cliente','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'cliente_alterar': ?>
            <div class="topo"><?php echo $cliente->nome_ou_fantasia; ?></div>
            <?php echo anchor('cliente','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'consultas': ?>
            <div class="topo">Consultas</div>
            <?php echo anchor('consulta','Gerenciar',array('class'=>'item')); ?>
            <?php echo anchor('consulta/cadastrar','Cadastrar',array('class'=>'item')); ?>
            <?php echo anchor('consulta/grupo','Grupo',array('class'=>'item')); ?>
            <?php echo anchor('inicio','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'usuarios': ?>
            <div class="topo">Usuários</div>
            <?php echo anchor('usuario','Gerenciar',array('class'=>'item')); ?>
            <?php echo anchor('usuario/cadastrar','Cadastrar',array('class'=>'item')); ?>
            <?php echo anchor('inicio','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'boletos': ?>
            <?php switch($this->session->userdata('adm_nivel')) {
                case '1': ?>
                        <?php echo anchor('boleto', 'Gerenciar', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/cadastrar', 'Cadastrar', array('class' => 'item_solto')); ?>
                    <?php break;
                case '2': ?>
                        <?php echo anchor('boleto', 'Gerenciar', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/pagos', 'Pagos', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/cadastrar', 'Cadastrar', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/retorno', 'Retorno', array('class' => 'item')); ?>
                        <?php echo anchor('boleto/baixar_remessa_hoje', 'Baixar Remessa', array('class' => 'item_solto')); ?>
                        <?php echo anchor('faturamento/gerar_faturamento', 'Geração Faturamento', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/geracao_em_massa', 'Geração em Massa', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/envio_por_email', 'Envio por E-mail', array('class' => 'item_solto')) ?>
                    <?php break;
                case '3': ?>
                        <?php echo anchor('boleto', 'Gerenciar', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/pagos', 'Pagos', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/cadastrar', 'Cadastrar', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/retorno', 'Retorno', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/baixar_remessa_hoje', 'Baixar Remessa', array('class' => 'item_solto')); ?>
                        <?php echo anchor('faturamento/gerar_faturamento', 'Geração Faturamento', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/geracao_em_massa', 'Geração em Massa', array('class' => 'item_solto')); ?>
                        <?php echo anchor('boleto/envio_por_email', 'Envio por E-mail', array('class' => 'item_solto')) ?>
                    <?php break;
            } ?>
        <?php break;
    case 'faturamento': ?>
            <div class="topo">Faturamento</div>
            <?php echo anchor('faturamento','Faturamento',array('class'=>'item')); ?>
        <?php break;
    case 'faturas':
        break;
    case 'relatorios':
        break;
    case 'arquivo': ?>
            <!--div class="topo">Arquivos</div-->
            <?php echo anchor('arquivo','Gerenciar',array('class'=>'item_solto')); ?>
            <?php //echo anchor('inicio','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'planos': ?>
            <div class="topo">Planos</div>
            <?php echo anchor('plano','Gerenciar',array('class'=>'item')); ?>
            <?php echo anchor('plano/cadastrar','Cadastrar',array('class'=>'item')); ?>
            <?php echo anchor('inicio','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'mais_opcoes': ?>
            <div class="titulo">Mais Opções</div>
            <?php echo anchor('inicio','<i class="fa fa-home"></i> Início',array('class'=>'item')); ?>
            <?php echo anchor('diario','<i class="fa fa-book"></i> Diário',array('class'=>'item')); ?>
            <?php echo anchor('vault','<i class="fa fa-exclamation-triangle"></i> Vault',array('class'=>'item')); ?>
            <?php echo anchor('inicio/sair','<i class="fa fa-sign-out"></i> Sair',array('class'=>'item')); ?>
        <?php break;
endswitch;

?>
<br>
