<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script type="text/javascript">
$(document).ready(function ()
{
  $('body').catalogue({});

  $("#collections_institution_ref").change(function() {
    $.get("<?php echo url_for('collection/completeOptions');?>/institution/"+$(this).val(), function (data) {
	    $("#collections_parent_ref").html(data);
    });
  });
  $("#collections_parent_ref").change(function() {
    if($(this).val())
    {
      $.get("<?php echo url_for('collection/setInstitution');?>/parent_ref/"+$(this).val(), function (data) {
        $("#institution_to_change").html(data);
      });
    }
  });
});
</script>
<?php echo form_tag('collection/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>
<?php echo $form->renderGlobalErrors() ?>
  <table class="collections">
    <tbody>
      <tr>
        <th>
	  <?php echo $form['is_public']->renderLabel("Public collection") ?>
          <?php echo help_ico($form['is_public']->renderHelp(),$sf_user);?>
        </th>
        <td>
          <?php echo $form['is_public']->renderError() ?>
          <?php echo $form['is_public'] ?>
        </td>
      </tr>
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
        <th><?php echo $form['institution_ref']->renderLabel() ?></th>
        <td id="institution_to_change">
          <?php echo $form['institution_ref']->renderError() ?>
          <?php echo $form['institution_ref'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['collection_type']->renderLabel() ?></th>
        <td>
          <?php echo $form['collection_type']->renderError() ?>
          <?php echo $form['collection_type'] ?>
        </td>
      </tr>
      <tr>
        <th>
          <?php echo $form['main_manager_ref']->renderLabel() ?>
	  <?php echo help_ico($form['main_manager_ref']->renderHelp(),$sf_user);?>
        </th>
        <td>
          <?php echo $form['main_manager_ref']->renderError() ?>
          <?php echo $form['main_manager_ref'] ?>
        </td>
      </tr>
      <tr>
        <th>
          <?php echo $form['staff_ref']->renderLabel() ?>
        </th>
        <td>
          <?php echo $form['staff_ref']->renderError() ?>
          <?php echo $form['staff_ref'] ?>
        </td>
      </tr>

      <tr>
        <th><?php echo $form['parent_ref']->renderLabel() ?></th>
        <td>
          <?php echo $form['parent_ref']->renderError() ?>
          <?php echo $form['parent_ref'] ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="encoding collections_rights" id="user_right">
            <thead>
              <tr>
                <th><label><?php echo __("Users") ; ?></label></th>
                <th colspan="4"><label><?php echo __("Rights") ; ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($form['CollectionsRights'] as $form_value):?>
                <?php include_partial('coll_rights', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId())));?>
              <?php endforeach;?>
              <?php foreach($form['newVal'] as $form_value):?>
                <?php include_partial('coll_rights', array('form' => $form_value, 'ref_id' => ''));?>
              <?php endforeach;?>
            </tbody>
          </table>

          <div class='add_value' id="user_right">
            <a href="<?php echo url_for('collection/addValue'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
            <a class='coll_right' href="<?php echo url_for('user/choose');?>" name="<?php echo __('Choose a User');?>"><?php echo __('Add User');?></a>
          </div>

      	</td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form['id'] ?>
          <?php if (!$form->getObject()->isNew()): ?>
      	    <a href="<?php echo url_for('collection/new') ?>"><?php echo __('New collection');?></a>
      	    &nbsp;<?php echo link_to(__('Duplicate collection'), 'collection/new?duplicate_id='.$form->getObject()->getId()) ?>
            &nbsp;<?php echo link_to(__('Delete'), 'collection/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          &nbsp;<a href="<?php echo url_for('collection/index') ?>"><?php echo __('Cancel');?></a>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
<script  type="text/javascript">
$(document).ready(function () {
    $('.clear_coll').live('click', detachCollRightValue);
    $('a.coll_right').click(collection_add_user);
    $('a.set_rights').click(collection_add_rights);
});
</script>
