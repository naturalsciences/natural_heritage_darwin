<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $sf_user->getCulture() ?>" lang="<?php echo $sf_user->getCulture() ?>">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body id="login_page">
    <table class="all_content">
      <tr>
        <?php include_partial('global/head_login') ?>
      </tr>
      <tr>
        <td class="content">
          <?php echo $sf_content ?>
        </td>
      </tr>
      <tr>
        <td class="menu_bottom">
          <div class="page">
            <table>
              <tr>
                <td class="browser_img"><ul>
                  <li><?php echo __('Recommended browser for DaRWIN :') ; ?></li>
                  <li><?php echo image_tag('chrome.png',array('title' =>'Google Chrome','alt'=>'Google Chrome'));?></li>
                  <li><?php echo image_tag('firefox.png',array('title' =>'Firefox >= 3.6','alt'=>'Firefox'));?></li>
                  <li><?php echo image_tag('Safari.png',array('title' =>'Safari','alt'=>'Safari'));?></li>
                </ul></td>
              </tr>
            </table>    
          </div>
        </td>     
      </tr>
    </table>
    <script type="text/javascript">
    $(document).ready(function () {
      attachHelpQtip('body');
      if($('.menu_top .house').length)
        location.reload();
    });
    </script>
    <?php if(sfConfig::get('dw_broadcast_enabled', false)):?>
      <div id="broadcast_bottom_padding"></div>
      <div id="broadcast_bottom"><?php echo __(sfConfig::get('dw_broadcast_message', ''));?>
        <span><?php echo __(sfConfig::get('dw_broadcast_submessage', ''));?></span>
      </div>
    <?php endif;?>
    <?php if(sfConfig::get('dw_analytics_enabled', false)):?>
			<?php include_partial('global/analytics') ?>
    <?php endif;?>
  </body>
</html>
