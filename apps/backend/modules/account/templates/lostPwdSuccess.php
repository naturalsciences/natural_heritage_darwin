<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <title><?php include_slot('title') ?></title>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body id="login_page">
    <table class="all_content">
      <tr>
        <?php include_partial('global/head_login') ?>
      </tr>
      <tr>
        <td class="content">
          <h1 id="login"><?php echo __("Reset your password.");?></h1>
          <?php echo $sf_content ?>
        </td>
      </tr>
      <tr><td class="menu_bottom">&nbsp;</td></tr>
    </table>
    <script type="text/javascript">
    $(document).ready(function () {
      attachHelpQtip('body');
    });
    </script>
  </body>
</html>