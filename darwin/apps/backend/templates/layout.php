<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $sf_user->getCulture() ?>" lang="<?php echo $sf_user->getCulture() ?>">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <title><?php include_slot('title') ?></title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <!--[if IE]>
    <?php echo stylesheet_tag('ie.css') ?>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper">
    <?php include_partial('global/head_menu') ?>
    <?php echo $sf_content ?>
    <script type="text/javascript">
    $(document).ready(function () {
      attachHelpQtip('body');
    });
    </script>
    <div class="clear"></div>
    <div id="load_indicator"><?php echo image_tag('loader.gif');?> <?php echo __('Loading');?></div>
    <?php if(sfConfig::get('dw_broadcast_enabled', false)):?>
      <div id="broadcast_bottom_padding"></div>
      <div id="broadcast_bottom"><?php echo __(sfConfig::get('dw_broadcast_message', ''));?>
      </div>
    <?php endif;?>
    <?php if(sfConfig::get('dw_broadcast_enabled', false)):?>
      <div id="broadcast_bottom_padding"></div>
      <div id="broadcast_bottom"><?php echo __(sfConfig::get('dw_broadcast_message', ''));?>
      </div>
    <?php endif;?>
    <?php if($sf_context->has('is_outdated') && $sf_context->get('is_outdated')):?>
			<div id="broadcast_bottom_padding"></div>
      <div id="broadcast_bottom">There is a problem with the Database, please contact your administator and look in the logs of symfony</div>
    <?php endif;?>
    </div>
  </body>
</html>
