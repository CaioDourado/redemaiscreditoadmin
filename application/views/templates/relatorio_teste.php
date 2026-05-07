<?php for($l=0; $l<count($dados->processos_judiciais->processos->registro);$l++): ?>
    <h3>Partes do Processo.</h3>
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Nome</th>
                <th>Polaridade</th>
                <th>Tipo</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($dados->processos_judiciais->processos->registro[$l]->partes->registro as $index => $i): ?>
                <tr>
                    <td><?php if(is_string($i->documento)) echo $i->documento; ?></td>
                    <td><?php echo $i->nome; ?></td>
                    <td><?php echo $i->polaridade; ?></td>
                    <td><?php echo $i->tipo; ?></td>
                    <td><?php echo $i->detalhamento; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Atualizações do Processo.</h3>
    <table>
        <thead>
        <tr>
            <th>Data Publicacao</th>
            <th>Data Captura</th>
            <th>Conteudo</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($dados->processos_judiciais->processos->registro[$l]->atualizacao->registro as $index => $i): ?>
            <tr>
                <td><?php echo $i->data_publicacao; ?></td>
                <td><?php echo $i->data_captura; ?></td>
                <td><?php echo $i->conteudo; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endfor; ?>