<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php if(isset($title)) echo $title; else echo "CONSULTAS"; ?></title>
        <link rel="stylesheet" href="<?php echo base_url().'assets/css/style.css'; ?>">
    </head>
    <body>
    <div class="header">
        <div class="container">
            <div class="logo">CONSULTAS</div>
            <img src="" alt="" class="logo">
            <div class="bloco">
                <p><a href="">Consultas</a></p>
                <p><a href="">Administrativo</a></p>
                <p><a href="">Sair</a></p>
            </div>
            <div class="bloco">
                <p><b>Usuário Logado </b></p>
                <p>Cód. Contrato | 1242</p>
                <p>5001242 | 1139</p>
            </div>
            <div class="bloco">
                <p><b>Central de Atendimento</b></p>
                <p>(14) 33139400</p>
                <p>cac@dossietotal.com.br</p>
            </div>

        </div>
    </div>
    <div class="menu">
        <div class="topo">Administração</div>
        <div class="item">Contratos</div>
        <div class="item">Usuários</div>
        <a class="item" href="boletos.html">Boletos</a>
        <div class="item">Relatórios</div>
        <div class="item">Negativação</div>
        <div class="item">Tabela de Vendas</div>
    </div>
    <div class="corpo">
        <div class="janela">
            <div class="titulo"><?php if(isset($title_window)) echo $title_window; ?></div>
            <div class="conteudo">
                <?php if(isset($content)) echo $content; ?>
            </div>
        </div>
    </div>

    </body>
</html>
