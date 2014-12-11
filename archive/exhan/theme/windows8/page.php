<!DOCTYPE html>
<html>
    
    <head>
        <meta charset="utf-8"/>
        <title><?php echo page::$title;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="<?php echo page::url("theme/windows8/css/bootstrap.min.css")?>" rel="stylesheet">
        <link href="<?php echo page::url("theme/windows8/css/custom.css")?>" rel="stylesheet">
        <link href="<?php echo page::url("theme/windows8/css/bootstrap-wysihtml5.css")?>" rel="stylesheet">


        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="../../assets/js/html5shiv.js"></script>
          <script src="../../assets/js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo page::url("");?>">Home</a>
                </div>
                <div class="collapse navbar-collapse">
                    <?php echo theme::theme_menu();?>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div class="container">
            <?php /*
              <ol class="breadcrumb">
              <li><a href="<?php echo page::url("");?>">Home</a></li>
              <?php $path = page::url("");?>
              <?php
              foreach((is_array(page::arg()) ? page::arg() : array())as $arg):
              ?>
              <?php $path .="$arg/";?>
              <li><a href="<?php echo $path;?>"><?php echo $arg;?></a></li>
              <?php endforeach;?>
              </ol>
             * 
             */?>
            <div class="">
                <?php if(page::title() != NULL):?>
                    <h1 class="page_title"><?php echo page::title();?></h1>
                <?php endif;?>
                <?php echo page::content();?>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo page::url("theme/windows8/js/jquery.js");?>"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo page::url("theme/windows8/js/bootstrap.min.js")?>"></script>
    </body>

</html>