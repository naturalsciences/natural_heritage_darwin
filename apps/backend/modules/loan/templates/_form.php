<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
$(document).ready(function () 
{
  //$('body').catalogue({});
});
</script>

<?php echo form_tag('loan/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition loan_form'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put"/>
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
        <th><?php echo $form['from_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['from_date']->renderError() ?>
          <?php echo $form['from_date'] ?>
        </td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <th><?php echo $form['to_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['to_date']->renderError() ?>
          <?php echo $form['to_date'] ?>
        </td>
      </tr>
      <?php if (! $form->getObject()->isNew()): ?>
      <tr>
        <td></td>
        <td></td>
        <th><?php echo $form['effective_to_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['effective_to_date']->renderError() ?>
          <?php echo $form['effective_to_date'] ?>
        </td>
      </tr>
      <?php endif;?>

      <tr>
        <th><?php echo $form['description']->renderLabel() ?></th>
        <td colspan="3">
          <?php echo $form['description']->renderError() ?>
          <?php echo $form['description'] ?>
        </td>
      </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form['id'] ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Loan'), 'loan/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Loan'), 'loan/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'loan/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>