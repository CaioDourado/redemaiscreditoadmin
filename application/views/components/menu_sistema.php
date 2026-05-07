<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($menu):
    case 'padrao': ?>
        <div class="topo">Início</div>
            <?php echo anchor('sistema/contrato','Meu Contrato',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consultas','Consultas',array('class'=>'item')); ?>
            <?php echo anchor('sistema/negativacao','Negativação',array('class'=>'item')); ?>
            <?php echo anchor('sistema/boletos','Boletos',array('class'=>'item')); ?>
            <?php echo anchor('sistema/relatorios','Relatórios',array('class'=>'item')); ?>
        <?php break;
    case 'contrato': ?>
            <div class="topo">Consultas</div>
            <?php echo anchor('sistema/contrato/visualizar','Meu Contrato',array('class'=>'item')); ?>
            <?php echo anchor('sistema/contrato/produtos_e_valores','Produtos e Valores',array('class'=>'item')); ?>
            <?php echo anchor('sistema/contrato/imprimir_aceite','Imprimir Aceite Contrato',array('class'=>'item')); ?>
            <?php echo anchor('sistema','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'consultas': ?>
            <div class="topo">Consultas</div>
            <?php echo anchor('sistema/consultas/crediticias','Creditícias',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consultas/cadastrais','Cadastrais',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consultas/veiculos','Veículos',array('class'=>'item')); ?>
            <?php echo anchor('sistema','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'ver_consulta': ?>
            <div class="topo">Ver Consulta</div>
            <?php echo anchor('sistema/consulta/ver_consulta/'.$id_consulta.'/pdf','Exportar Para PDF',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consulta/ver_consulta/'.$id_consulta.'/excel','Exportar Para Excel',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consulta/ver_consulta/'.$id_consulta.'/email','Enviar por E-mail',array('class'=>'item')); ?>
            <?php echo anchor('sistema/consultas','Voltar',array('class'=>'back')); ?>
        <?php break;
    case 'negativacao': ?>
            <div class="topo">Ver Consulta</div>
            <?php echo anchor('sistema/negativacao/carta','Carta Aviso Extra Judicial',array('class'=>'item')); ?>
        <?php break;
    case 'boletos':
        break;
    case '':
        break;
endswitch;