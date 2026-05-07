<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php if(isset($title)) echo $title; else echo "CONSULTAS"; ?></title>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url().'assets/css/style.css'; ?>">
    </head>
    <body>
        <div class="header">
            <div class="container">
                <div class="logo">REDE<i class="fa fa-plus-circle"></i>CREDITO</div>
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
        <br>
        <div class="container-fluid">
            <?php get_msgs(); ?>
            <?php form_validation(); ?>
            <ol class="breadcrumb">
                <?php foreach($breadcrumb as $index => $item): ?>
                    <li><?php echo anchor($item[0],$item[1]); ?></li>
                <?php endforeach; ?>
            </ol>
            <div class="row">
                <div class="col-md-2">
                    <div class="menu">
                        <?php if(isset($menu)) echo $menu; ?>
                    </div>
                </div>
                <div class="col-md-10">
                    <?php if(isset($content)) echo $content; ?>
                </div>
            </div>
        </div>
        <div class="tela-loading">
            <div class="tela-loading-container">
                <div class="tela-loading-container-cell">
                    <img src="<?php echo base_url(); ?>/assets/imgs/loadin_consult.gif" alt="">
                    <div class="descricao">A REDE + CREDITO ESTÁ PESQUISANDO</div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/script.js"></script>
    </body>
</html>
