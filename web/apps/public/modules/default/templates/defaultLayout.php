<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $sf_user->getCulture() ?>" lang="<?php echo $sf_user->getCulture() ?>">
<head>

<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<link rel="stylesheet" type="text/css" media="screen" href="/css/reset.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/main.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/menu_pub.css" />

<link rel="shortcut icon" href="/favicon.ico" />

</head>
<body>
<div class="dw_logo">
  <a href="/"><?php echo image_tag('photo_bckg.jpg', array('alt' => 'Darwin2')) ?></a>
</div>
<div class="lyt_content">
  <?php echo $sf_content ?>
</div>
</body>
</html>
