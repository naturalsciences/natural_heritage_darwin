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
  <div class="page blue_line">
    <table>
      <tr>
        <td class="blue_line_left">
          <ul class="lang_picker">
            <li><?php echo link_to('En','board/lang?lang=en');?></li>
            <li class="sep">|</li>
            <li><?php echo link_to('Fr','board/lang?lang=fr');?></li>
            <li class="sep">|</li>
            <li><?php echo link_to('Nl','board/lang?lang=nl');?></li>
            <li class="sep">|</li>
            <li><?php echo link_to('Es','board/lang?lang=es_ES');?></li>
          </ul>
        </td>
        <td class="blue_line_right">
          <table class="register_form">
            <tr>
              <!--<td class="menu_button">
                <?php echo link_to(__('Login'),$sf_context->getConfiguration()->generateBackendUrl('homepage', array(), $sf_request),array('target'=>'blank')) ;?>
              </td>-->
              <td class="menu_button">
                <?php echo link_to(__('DaRWIN search'),'search/search',array('target'=>'blank')) ;?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>

    <?php echo $sf_content ?>
  </body>
</html>
