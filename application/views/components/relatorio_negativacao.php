<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body>
        <h1 style="text-align: center">Relatório Rede Mais Cre´dito</h1>
        <hr>
        <p><?php echo date('d/m/Y H:i:s'); ?></p>
        <br>
        <?php foreach($negativacoes as $i => $n): ?>
            <table style="width: 100%;overflow-wrap: anywhere">
                <tbody>
                    <tr><td><b>ID:</b> <?php echo $n->id_negativacao; ?></td></tr>
                    <tr><td><b>CPF/CNPJ:</b> <?php echo $n->cpf_cnpj; ?></td></tr>
                    <tr><td><b>Requisição:</b> <?php echo $n->requisicao; ?></td></tr>
                    <tr><td><b>Retorno:</b> <?php echo $n->retorno; ?></td></tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    </body>
</html>