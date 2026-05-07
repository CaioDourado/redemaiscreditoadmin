<div class="titulo">Dados do Pesquisado</div>
<div class="table-responsive">
    <table class="table table-list">
        <tbody>
        <tr><td>CPF</td><td><?php echo $relatorio->cpf; ?></td></tr>
        <tr><td>Nome</td><td><?php echo $relatorio->nome; ?></td></tr>
        <tr><td>Data Nascimento</td><td><?php echo $relatorio->data_nascimento; ?></td></tr>
        <tr><td>Nome da Mãe</td><td><?php echo $relatorio->nome_mae; ?></td></tr>
        <tr>
            <td>Telefones</td>
            <td>
                <?php foreach($relatorio->telefones as $index => $telefone): ?>
                    <kbd><?php echo $telefone; ?></kbd>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td>E-mails</td>
            <td>
                <?php foreach($relatorio->emails as $index => $email): ?>
                    <kbd><?php echo $email; ?></kbd>
                <?php endforeach; ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="titulo">Endereços do Pesquisado</div>
<?php if(count($relatorio->enderecos)>0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th>Endereço</th>
                <th>Bairro</th>
                <th>Cidade</th>
                <th>CEP</th>
                <th>UF</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($relatorio->enderecos as $index => $endereco): ?>
                <tr>
                    <td><?php echo $endereco->endereco; ?></td>
                    <td><?php echo $endereco->bairro; ?></td>
                    <td><?php echo $endereco->cidade; ?></td>
                    <td><?php echo $endereco->cep; ?></td>
                    <td><?php echo $endereco->uf; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-condensed table-consulta"><tbody><tr><td><b>SEM ENDEREÇOS ENCONTRADOS</b></td></tr></tbody></table></div>
<?php endif; ?>


<div class="titulo">Parentes do Pesquisado</div>
<?php if(count($relatorio->parentes)>0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Data Nascimento</th>
                <th>Telefone</th>
                <th class="hidden-print">Endereço</th>
                <th class="hidden-print">Bairro</th>
                <th>Cidade</th>
                <th>CEP</th>
                <th>UF</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($relatorio->parentes as $index => $parente): ?>
                <tr>
                    <td><?php echo $parente->nome; ?></td>
                    <td><?php echo $parente->cpf; ?></td>
                    <td><?php echo $parente->data_nascimento; ?></td>
                    <td><?php echo $parente->telefone; ?></td>
                    <td class="hidden-print"><?php echo $parente->endereco; ?></td>
                    <td class="hidden-print"><?php echo $parente->bairro; ?></td>
                    <td><?php echo $parente->cidade; ?></td>
                    <td><?php echo $parente->cep; ?></td>
                    <td><?php echo $parente->uf; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-condensed table-consulta"><tbody><tr><td><b>SEM PARENTES ENCONTRADOS</b></td></tr></tbody></table></div>
<?php endif; ?>


<div class="titulo">Vizinhos do Pesquisado</div>
<?php if(count($relatorio->vizinhos)>0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Data Nascimento</th>
                <th>Telefone</th>
                <th class="hidden-print">Endereço</th>
                <th class="hidden-print">Bairro</th>
                <th>Cidade</th>
                <th>CEP</th>
                <th>UF</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($relatorio->vizinhos as $index => $vizinho): ?>
                <tr>
                    <td><?php echo $vizinho->nome; ?></td>
                    <td><?php echo $vizinho->cpf; ?></td>
                    <td><?php echo $vizinho->data_nascimento; ?></td>
                    <td><?php echo $vizinho->telefone; ?></td>
                    <td class="hidden-print"><?php echo $vizinho->endereco; ?></td>
                    <td class="hidden-print"><?php echo $vizinho->bairro; ?></td>
                    <td><?php echo $vizinho->cidade; ?></td>
                    <td><?php echo $vizinho->cep; ?></td>
                    <td><?php echo $vizinho->uf; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-condensed table-consulta"><tbody><tr><td><b>SEM VIZINHOS ENCONTRADOS</b></td></tr></tbody></table></div>
<?php endif; ?>

<div class="titulo">Trabalha/Trabalhou nas seguintes empresas</div>
<?php if(count($relatorio->trabalhos)>0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-consulta">
            <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th class="hidden-print">Endereço</th>
                <th class="hidden-print">Bairro</th>
                <th>Cidade</th>
                <th>CEP</th>
                <th>UF</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($relatorio->trabalhos as $index => $trabalho): ?>
                <tr>
                    <td><?php echo $trabalho->nome; ?></td>
                    <td><?php echo $trabalho->cnpj; ?></td>
                    <td><?php echo $trabalho->telefone; ?></td>
                    <td class="hidden-print"><?php echo $trabalho->endereco; ?></td>
                    <td class="hidden-print"><?php echo $trabalho->bairro; ?></td>
                    <td><?php echo $trabalho->cidade; ?></td>
                    <td><?php echo $trabalho->cep; ?></td>
                    <td><?php echo $trabalho->uf; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-condensed table-consulta"><tbody><tr><td><b>SEM TRABALHOS ENCONTRADOS</b></td></tr></tbody></table></div>
<?php endif; ?>