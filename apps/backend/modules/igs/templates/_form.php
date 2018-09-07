<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('igs/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" ig_num="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo __('I.G. number:');?></th>
        <td>
          <?php echo $form['ig_num']->renderError() ?>
          <?php echo $form['ig_num'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo __('I.G. creation date:'); ?></th>
        <td>
          <?php echo $form['ig_date']->renderError() ?>
          <?php echo $form['ig_date'] ?>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New I.G.'), 'igs/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate I.G.'), 'igs/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('igs/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete'), 'igs/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
