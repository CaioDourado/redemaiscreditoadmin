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
        <?php echo form_open(current_url(),array('id'=>'form_login')); ?>
        <div class="panel panel-default" style="width: 300px;position: fixed; top: 50%; left: 50%; margin-top: -100px; margin-left: -150px">
            <div class="panel-heading text-center">Rede Mais Crédito</div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" name="usuario" placeholder="Informe seu Usuário">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" name="senha" placeholder="Informe sua Senha">
                    </div>
                </div>
                <?php echo form_hidden('lat','0'); ?>
                <?php echo form_hidden('lng','0'); ?>
                <?php echo form_hidden('timestamp','0'); ?>
                <div class="btn btn-success btn-block btn-login">Entrar</div>
                <?php //echo form_submit('submit','Entrar',array('class'=>'btn btn-success btn-block btn-login')); ?>
            </div>
        </div>
        <?php echo form_close(); ?>

        <script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/script.js"></script>
        <script>
            $(document).ready(function(){3
                $(".btn-login").click(function(){
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(posicao){
                            $("input[name='lat']").val(posicao.coords.latitude);
                            $("input[name='lng']").val(posicao.coords.longitude);
                            $("input[name='timestamp']").val(posicao.timestamp);
                            $("#form_login").submit();
                        });
                    }else{
                        alert('Ative sua localização para entrar no sistema.');
                    }
                });
            });
        </script>
    </body>
</html>
