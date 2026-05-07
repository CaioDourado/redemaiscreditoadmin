<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php if(isset($title)) echo $title; else echo "CONSULTAS"; ?></title>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/font-awesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url().'assets/css/styleg.css'; ?>">
    </head>
    <body>
        <div class="grid_principal">
            <div class="left">
                <div class="menu_lateral">
                    <div class="titulo_superior">REDE MAIS CRÉDITO</div>
                    <?php echo $menu; ?>
                </div>
            </div>
            <div class="right">
                <?php get_msgs(); ?>
                <?php form_validation(); ?>
                <?php if(isset($pg_title)): ?>
                    <div class="page-header pb">
                        <div class="ph-titulo"> <?php echo $pg_title; ?> </div>
                        <?php if($pg_subtitle!=null): ?> <div class="ph-subtitulo"> <?php echo $pg_subtitle; ?> </div> <?php endif; ?>
                        <?php if(isset($pg_bts)): ?>
                            <div class="bts">
                                <?php foreach($pg_bts as $index => $bt): echo $bt; endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="p15">
                    <?php if(isset($content)) echo $content; ?>
                </div>
            </div>
        </div>
        <div class="bt_menu"><i class="fa fa-bars"></i></div>

        <script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/script.js"></script>
        <script>
            var menu_open = false;
            $(function(){
                $(".bt_menu").click(function(){
                    if(menu_open){
                        $(".grid_principal .left").removeClass("active");
                        $(".bt_menu i").removeClass("fa-close").addClass("fa-bars");
                        menu_open = false;
                    }else{
                        $(".grid_principal .left").addClass("active");
                        $(".bt_menu i").removeClass("fa-bars").addClass("fa-close");
                        menu_open = true;
                    }
                });

                $("#pesquisa_tabela").keyup(function(){
                    let pesquisa = String($(this).val()).toUpperCase();
                    filtrar_tabelas_pesquisaveis(pesquisa);
                });

                $('#edittext').summernote();
            });

            function filtrar_tabelas_pesquisaveis(pesquisa){
                $(".tabela_pesquisavel tbody tr").each(function(indice,linha) {
                    let conteudo = String($(linha).text()).toUpperCase();
                    if(conteudo.indexOf(pesquisa) == -1){
                        $(linha).hide();
                    }else{
                        $(linha).show();
                    }
                });
            }
        </script>
    </body>
</html>
