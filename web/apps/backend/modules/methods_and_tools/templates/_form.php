<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
  $(document).ready(function () {
    $('body').catalogue({});
  });
</script>

<?php $notion_param = 'notion='.$notion;?>
<?php echo form_tag('methods_and_tools/'.($form->getObject()->isNew() ? 'create?'.$notion_param : 'update?id='.$form->getObject()->getId().'&'.$notion_param), array('class'=>'edition'));?>

  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <td>
          <?php echo $form[$notion]->renderError() ?>
          <?php echo $form[$notion] ?>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td>
          <?php echo $form['id'] ?>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New '.$notion), 'methods_and_tools/new?notion='.$notion) ?>
            &nbsp;<?php echo link_to(__('Duplicate '.$notion), 'methods_and_tools/new?notion'.$notion.'&duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>
          &nbsp;<a href="<?php echo url_for('methods_and_tools/'.(($notion=='method')?'methodsIndex':'toolsIndex')) ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'methods_and_tools/delete?notion='.$notion.'&id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
