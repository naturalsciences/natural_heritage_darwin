<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('expedition/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('expedition/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'expedition/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name_ts']->renderLabel() ?></th>
        <td>
          <?php echo $form['name_ts']->renderError() ?>
          <?php echo $form['name_ts'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name_indexed']->renderLabel() ?></th>
        <td>
          <?php echo $form['name_indexed']->renderError() ?>
          <?php echo $form['name_indexed'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name_language_full_text']->renderLabel() ?></th>
        <td>
          <?php echo $form['name_language_full_text']->renderError() ?>
          <?php echo $form['name_language_full_text'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_from_date_mask']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_from_date_mask']->renderError() ?>
          <?php echo $form['expedition_from_date_mask'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_from_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_from_date']->renderError() ?>
          <?php echo $form['expedition_from_date'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_to_date_mask']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_to_date_mask']->renderError() ?>
          <?php echo $form['expedition_to_date_mask'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_to_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_to_date']->renderError() ?>
          <?php echo $form['expedition_to_date'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
