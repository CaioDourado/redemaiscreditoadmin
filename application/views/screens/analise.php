<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'negativacoes': ?>
            <h3>Resumo</h3>
            <table class="table table-condensed table-striped">
                <tbody>
                    <tr><td>Sucesso:</td><td class="text-right"><?php echo count($sucessos); ?></td></tr>
                    <tr><td>Erros:</td><td class="text-right"><?php echo count($erros); ?></td></tr>
                </tbody>
            </table>
            <h3>Negativações com erro</h3>
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>Credor</th>
                        <th>Devedor</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach($erros as $index => $erro): ?>
                        <?php $dados_param = json_decode($erro->parametros); ?>
                        <?php $dados_retorno = json_decode($erro->retorno_json); ?>
                    
                        <tr>
                            <td><?php echo $dados_param->CNPJ_CREDOR; ?></td>
                            <td><?php echo $dados_param->RAZAO_CREDOR; ?></td>
                            <td><?php echo $dados_param->NOME_DEVEDOR; ?></td>
                            <td><?php echo $dados_param->COD_NATUREZA_DIVIDA; ?></td>
                            <td><?php echo $dados_retorno->inclusao->erro; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php break;
endswitch;
