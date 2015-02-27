<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $sf_user->getCulture() ?>" lang="<?php echo $sf_user->getCulture() ?>">
  <head>
    <?php include_http_metas() ?>  
    <?php include_metas() ?>
    <?php include_javascripts() ?>
    <?php include_stylesheets() ?>
    <title><?php include_slot('title') ?></title>
    <!--[if IE]>
    <?php echo stylesheet_tag('ie.css') ?>
    <![endif]-->
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
    <?php echo $sf_content ?>
  </body>
</html>
