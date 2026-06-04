<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'lista':
        $paginas = max(1, (int) ceil($total / $limite));
        $url_base = site_url('negativacao').'?limite='.$limite.'&busca='.urlencode($busca).'&pagina=';
        ?>
        <div class="panel panel-google">
            <div class="panel-heading">
                <b>Negativacoes</b>
                <span class="pull-right"><?php echo number_format($total, 0, ',', '.'); ?> registro(s)</span>
            </div>
            <div class="panel-body">
                <?php echo form_open('negativacao', array('method' => 'get', 'class' => 'form-inline')); ?>
                    <div class="form-group">
                        <input type="text" name="busca" class="form-control" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Cliente, documento, consulta ou fornecedor" style="min-width: 320px">
                    </div>
                    <div class="form-group">
                        <select name="limite" class="form-control">
                            <?php foreach(array(50,100,200) as $opcao): ?>
                                <option value="<?php echo $opcao; ?>" <?php echo $limite == $opcao ? 'selected' : ''; ?>><?php echo $opcao; ?> por pagina</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Filtrar</button>
                    <?php echo anchor('negativacao', 'Limpar', array('class' => 'btn btn-default')); ?>
                <?php echo form_close(); ?>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-striped">
                    <thead>
                    <tr>
                        <th style="width:80px">#</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Consulta</th>
                        <th>Fornecedor</th>
                        <th class="text-right">Valor</th>
                        <th>Data</th>
                        <th>Negativacao</th>
                        <th>Baixa</th>
                        <th style="width:90px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($negativacoes)): ?>
                        <tr><td colspan="10" class="text-center text-muted">Nenhuma negativacao encontrada.</td></tr>
                    <?php endif; ?>
                    <?php foreach($negativacoes as $negativacao): ?>
                        <tr>
                            <td><?php echo $negativacao->id_negativacao; ?></td>
                            <td><?php echo htmlspecialchars($negativacao->cliente_nome ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($negativacao->cpf_cnpj ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($negativacao->slug ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($negativacao->fornecedor ?: '-'); ?></td>
                            <td class="text-right">R$ <?php echo number_format((float) $negativacao->valor, 2, ',', '.'); ?></td>
                            <td><?php echo isset($negativacao->criado_em) ? date('d/m/Y H:i', strtotime($negativacao->criado_em)) : '-'; ?></td>
                            <td><span class="label label-<?php echo $negativacao->_status_negativacao['classe']; ?>"><i class="fa fa-<?php echo $negativacao->_status_negativacao['icone']; ?>"></i> <?php echo $negativacao->_status_negativacao['texto']; ?></span></td>
                            <td><span class="label label-<?php echo $negativacao->_status_baixa['classe']; ?>"><i class="fa fa-<?php echo $negativacao->_status_baixa['icone']; ?>"></i> <?php echo $negativacao->_status_baixa['texto']; ?></span></td>
                            <td><?php echo anchor('negativacao/dossie/'.$negativacao->id_negativacao, '<i class="fa fa-folder-open"></i> Abrir', array('class' => 'btn btn-info btn-xs')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer clearfix">
                <span>Pagina <?php echo $pagina; ?> de <?php echo $paginas; ?></span>
                <div class="pull-right">
                    <?php if($pagina > 1): ?>
                        <a class="btn btn-default btn-sm" href="<?php echo $url_base.($pagina - 1); ?>">Anterior</a>
                    <?php endif; ?>
                    <?php if($pagina < $paginas): ?>
                        <a class="btn btn-default btn-sm" href="<?php echo $url_base.($pagina + 1); ?>">Proxima</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'dossie':
        if(!function_exists('neg_pretty')){
            function neg_pretty($value){
                if($value === null || $value === '') return '-';
                $decoded = json_decode($value);
                if(json_last_error() === JSON_ERROR_NONE){
                    return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                $xml = @simplexml_load_string($value);
                if($xml !== false){
                    $dom = new DOMDocument('1.0');
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;
                    @$dom->loadXML($value);
                    return $dom->saveXML();
                }
                return $value;
            }
        }
        if(!function_exists('neg_val')){
            function neg_val($obj, $field){
                return isset($obj->$field) && $obj->$field !== '' ? htmlspecialchars($obj->$field) : '-';
            }
        }        if(!function_exists('neg_retorno_box')){
            function neg_retorno_box($value, $maxHeight = 320){
                $pretty = neg_pretty($value);
                if(is_string($pretty) && strlen($pretty) > 160 && !preg_match('/\s/', $pretty)){
                    $pretty = chunk_split($pretty, 120, "\n");
                }
                $html = htmlspecialchars($pretty);
                return '<pre class="neg-retorno-box" style="max-height: '.$maxHeight.'px; overflow:auto; white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-width: 100%; font-size: 12px; line-height: 1.45;">'.$html.'</pre>';
            }
        }
        ?>
        <div class="clearfix" style="margin-bottom: 15px">
            <?php echo anchor('negativacao', '<i class="fa fa-arrow-left"></i> Voltar', array('class' => 'btn btn-default')); ?>
        </div>

        <div class="panel panel-google">
            <div class="panel-heading"><b>Dados da negativacao #<?php echo $negativacao->id_negativacao; ?></b></div>
            <table class="table table-bordered table-condensed">
                <tbody>
                <tr><th>Cliente</th><td><?php echo neg_val($negativacao, 'cliente_nome'); ?></td><th>Usuario</th><td><?php echo neg_val($negativacao, 'usuario_nome'); ?></td></tr>
                <tr><th>Documento</th><td><?php echo neg_val($negativacao, 'cpf_cnpj'); ?></td><th>Consulta</th><td><?php echo neg_val($negativacao, 'slug'); ?></td></tr>
                <tr><th>Slug</th><td><?php echo neg_val($negativacao, 'slug'); ?></td><th>Fornecedor</th><td><?php echo neg_val($negativacao, 'fornecedor'); ?></td></tr>
                <tr><th>Valor</th><td>R$ <?php echo number_format((float) $negativacao->valor, 2, ',', '.'); ?></td><th>Custo</th><td>R$ <?php echo number_format((float) $negativacao->custo, 2, ',', '.'); ?></td></tr>
                <tr><th>Criado em</th><td><?php echo isset($negativacao->criado_em) ? date('d/m/Y H:i:s', strtotime($negativacao->criado_em)) : '-'; ?></td><th>Status</th><td><span class="label label-<?php echo $negativacao->_status_negativacao['classe']; ?>"><?php echo $negativacao->_status_negativacao['texto']; ?></span> <span class="label label-<?php echo $negativacao->_status_baixa['classe']; ?>"><?php echo $negativacao->_status_baixa['texto']; ?></span></td></tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Parametros enviados</b></div>
                    <div class="panel-body"><?php echo neg_retorno_box(isset($negativacao->parametros) ? $negativacao->parametros : '', 420); ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Retorno do fornecedor</b></div>
                    <div class="panel-body"><?php echo neg_retorno_box(isset($negativacao->retorno_json) && $negativacao->retorno_json ? $negativacao->retorno_json : $negativacao->retorno, 420); ?></div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><b>Auditorias da negativacao</b></div>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead><tr><th>Data</th><th>Area</th><th>Acao</th><th>Status</th><th>Mensagem</th><th>Erro</th></tr></thead>
                    <tbody>
                    <?php if(empty($auditorias)): ?><tr><td colspan="6" class="text-center text-muted">Nenhuma auditoria vinculada.</td></tr><?php endif; ?>
                    <?php foreach($auditorias as $auditoria): ?>
                        <tr><td><?php echo neg_val($auditoria, 'criado_em'); ?></td><td><?php echo neg_val($auditoria, 'area'); ?></td><td><?php echo neg_val($auditoria, 'acao'); ?></td><td><?php echo neg_val($auditoria, 'status'); ?></td><td><?php echo neg_val($auditoria, 'mensagem'); ?></td><td><?php echo neg_val($auditoria, 'erro'); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-google">
            <div class="panel-heading"><b>Baixas vinculadas</b></div>
            <div class="panel-body">
                <?php if(empty($baixas)): ?>
                    <div class="alert alert-info">Nenhuma baixa encontrada para este documento/cliente.</div>
                <?php endif; ?>
                <?php foreach($baixas as $baixa): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Baixa #<?php echo $baixa->id_negativacao_baixa; ?></b> <span class="label label-<?php echo $baixa->_status_baixa['classe']; ?>"><?php echo $baixa->_status_baixa['texto']; ?></span></div>
                        <table class="table table-bordered table-condensed">
                            <tbody>
                            <tr><th>Fornecedor</th><td><?php echo neg_val($baixa, 'fornecedor'); ?></td><th>Slug</th><td><?php echo neg_val($baixa, 'slug'); ?></td></tr>
                            <tr><th>Documento</th><td><?php echo neg_val($baixa, 'cpf_cnpj'); ?></td><th>Criado em</th><td><?php echo neg_val($baixa, 'criado_em'); ?></td></tr>
                            </tbody>
                        </table>
                        <div class="panel-body"><?php echo neg_retorno_box(isset($baixa->retorno_json) && $baixa->retorno_json ? $baixa->retorno_json : $baixa->retorno, 260); ?></div>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead><tr><th>Data</th><th>Area</th><th>Acao</th><th>Status</th><th>Mensagem</th></tr></thead>
                                <tbody>
                                <?php if(empty($baixa->_auditorias)): ?><tr><td colspan="5" class="text-center text-muted">Nenhuma auditoria vinculada a baixa.</td></tr><?php endif; ?>
                                <?php foreach($baixa->_auditorias as $auditoria): ?>
                                    <tr><td><?php echo neg_val($auditoria, 'criado_em'); ?></td><td><?php echo neg_val($auditoria, 'area'); ?></td><td><?php echo neg_val($auditoria, 'acao'); ?></td><td><?php echo neg_val($auditoria, 'status'); ?></td><td><?php echo neg_val($auditoria, 'mensagem'); ?></td></tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        break;
    case 'index': ?>
            <div class="panel panel-blue">
                <div class="panel-heading">NegativaÃ§Ãµes PEFIN</div>
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
                <div class="panel-heading">ConversÃ£o de NegativaÃ§Ã£o Pefin => Varejo</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>CNPJ</label><input type="text" class="form-control" value="<?php echo $cliente->cpf_cnpj; ?>" disabled></div></div>
                        <div class="col-md-9"><div class="form-group"><label>RazÃ£o Social</label><input type="text" class="form-control" value="<?php echo $cliente->razao_social; ?>" disabled></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-8"><div class="form-group"><label>EndereÃ§o</label><input type="text" class="form-control" value="<?php echo $cliente->logradouro; ?>" disabled></div></div>
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
                        <div class="col-md-5"><?php echo form_select('natureza','Natureza da NegativaÃ§Ã£o <span style="color: #F00">*</span>',cod_natureza_scpc(),'',13); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('vencimento_inicio','Venc. Inicial<span style="color: #F00">*</span>',$devedor->VENCIMENTO_DIVIDA,'text-right data data5anos_validate obrigatorio'); ?></div>
                        <div class="col-md-3"><?php echo form_input('vencimento_fim','Venc. Final<span style="color: #F00">*</span>',$devedor->VENCIMENTO_DIVIDA,'text-right data'); ?></div>
                        <div class="col-md-2"><?php echo form_input('parcelas','Parcelas <span style="color: #F00">*</span>',1,'text-right'); ?></div>
                        <div class="col-md-2"><?php echo form_input('valor','Valor <span style="color: #F00">*</span>',$devedor->VALOR_DIVIDA,'text-right dinheiro'); ?></div>
                        <div class="col-md-2"><?php echo form_input('contrato','Contrato <span style="color: #F00">*</span>','000001','text-right'); ?></div>
                        <!--div class="col-md-3"><?php //echo form_input('nosso_numero','Nosso NÃºmero','','text-right'); ?></div-->
                    </div>
                    <div class="row">
                        <div class="col-md-3"><?php echo form_input('data_nascimento','Data de Nascimento <span style="color: #F00">*</span>',$devedor->DATA_NASC,'data text-center'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"><?php echo form_input('cep','CEP <span style="color: #F00">*</span>',$devedor->CEP_CREDOR,'cep cep_validate obrigatorio'); ?></div>
                        <div class="col-md-10"><?php echo form_input('logradouro','EndereÃ§o <span style="color: #F00">*</span>',$devedor->ENDERECO_CREDOR.' '.$devedor->NUMERO_ENDERECO_CREDOR,'logradouro'); ?></div>
                        <!--div class="col-md-2"><?php echo form_input('numero','NÃºmero <span style="color: #F00">*</span>'); ?></div>
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
