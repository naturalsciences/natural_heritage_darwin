<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <table>
    <thead>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <th><?php echo __('Between'); ?></th>
        <th><?php echo __('and'); ?></th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
          &nbsp;
        </td>
        <td>
          <?php echo $form['date_from']->renderError() ?>
          <?php echo $form['date_from'] ?>
          &nbsp;
        </td>
        <td>
          <input type="submit" value="<?php echo __('Search'); ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</form>