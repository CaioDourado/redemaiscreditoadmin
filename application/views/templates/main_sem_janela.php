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
<br>
<div class="container">
    <?php get_msgs(); ?>
    <?php form_validation(); ?>
    <ol class="breadcrumb">
        <?php foreach($breadcrumb as $index => $item): ?>
            <li><?php echo anchor($item[0],$item[1]); ?></li>
        <?php endforeach; ?>
    </ol>
    <div class="row">
        <div class="col-md-3">
            <div class="menu">
                <?php if(isset($menu)) echo $menu; ?>
            </div>
        </div>
        <div class="col-md-9">
            <?php if(isset($content)) echo $content; ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/script.js"></script>
</body>
</html>
