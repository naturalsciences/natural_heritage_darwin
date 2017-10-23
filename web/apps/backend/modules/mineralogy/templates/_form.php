<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<?php echo form_tag('mineralogy/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition', 'enctype'=>'multipart/form-data'));?>
  <?php include_partial('catalogue/commonJs');?>
  <table class="classifications_edit">
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['code']->renderLabel() ?></th>
        <td>
          <?php echo $form['code']->renderError() ?>
          <?php echo $form['code'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['classification']->renderLabel() ?></th>
        <td>
          <?php echo $form['classification']->renderError() ?>
          <?php echo $form['classification'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['local_naming']->renderLabel() ?></th>
        <td>
          <?php echo $form['local_naming']->renderError() ?>
          <?php echo $form['local_naming'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['level_ref']->renderLabel() ?></th>
        <td>
          <?php echo $form['level_ref']->renderError() ?>
          <?php echo $form['level_ref'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo $form['status']->renderError() ?>
          <?php echo $form['status'] ?>
        </td>
      </tr>
      <tr id="parent_ref">
        <th class="top_aligned"><?php echo $form['parent_ref']->renderLabel() ?></th>
        <td>
          <?php echo $form['parent_ref']->renderError() ?>
          <?php echo $form['parent_ref'] ?>
          <div class="warn_message ref_name button hidden" id="taxonomy_parent_ref_warning"><?php echo __('The parenty does not follow the possible upper level rule');?></div>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['formule']->renderLabel() ?></th>
        <td>
          <?php echo $form['formule']->renderError() ?>
          <?php echo $form['formule'] ?>
        </td>
      </tr>
      <tr>
        <th class="ref_name"><?php echo $form['cristal_system']->renderLabel() ?></th>
        <td>
          <?php echo $form['cristal_system']->renderError() ?>
          <?php echo $form['cristal_system'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['color']->renderLabel() ?></th>
        <td>
          <?php echo $form['color']->renderError() ?>
          <?php echo $form['color'] ?>
        </td>
      </tr>       
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Unit'), 'mineralogy/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Unit'), 'mineralogy/new?duplicate_id='.$form->getObject()->getId()) ?>            
          <?php endif?>

          <?php echo $form['id']->render() ?><?php echo $form['table']->render() ?><?php echo link_to('', 'catalogue/searchPUL', array('id' => 'searchPUL', 'class' => 'hidden'));?>  &nbsp;<a href="<?php echo url_for('mineralogy/index') ?>"><?php echo __('Cancel');?></a>

          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'mineralogy/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>

          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
<script type="text/javascript">
$(document).ready(function () {
  <?php if($form->hasErrors())
      echo "$('tr#parent_ref .button').show();";
  ?>
  <?php if($form['level_ref']->getValue()) 
      echo "$('#mineralogy_level_ref').trigger('change');" ;
  ?>
  $('#mineralogy_name').autocomplete({
      minLength: 3,
      source: function( request, response ) {
        $.getJSON('<?php echo url_for('catalogue/completeName?table=mineralogy');?>', {term : request.term }, function( data) {
            response( $.map( data, function( item ) {
              return {
                label: item.label,
                value: item.label
              }
            }));

        });
      }
    });
});
</script>
