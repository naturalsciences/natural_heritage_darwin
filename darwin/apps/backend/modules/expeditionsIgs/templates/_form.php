<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('expedition/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
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
      </tr>
      <tr>
        <th><?php echo $form['expedition_from_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_from_date']->renderError() ?>
          <?php echo $form['expedition_from_date'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_to_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['expedition_to_date']->renderError() ?>
          <?php echo $form['expedition_to_date'] ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="encoding collections_rights" id="exp_member_table">
            <thead>
              <tr>
                <th>&nbsp;</th>
                <th><label><?php echo __("Members") ; ?></label></th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <?php $retainedKey = 0;?>
              <?php foreach($form['Members'] as $form_value):?>
                <?php include_partial('member_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
              <?php foreach($form['newMember'] as $form_value):?>
                <?php include_partial('member_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='add_value'>
            <a href="<?php echo url_for('expedition/addMember'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
            <a class='add_member' href="<?php echo url_for('people/choose?with_js=1');?>"><?php echo __('Add Member');?></a>
          </div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form['id'] ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Expedition'), 'expedition/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Expedition'), 'expedition/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('expedition/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'expedition/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<?php echo javascript_include_tag('catalogue_people.js') ?>

<script  type="text/javascript">
$(document).ready(function () {


function addMember(people_ref, people_name)
{ 

  info = 'ok';
  $('#exp_member_table tbody tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;
  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax({
    type: "GET",
    url: $('.add_value a.hidden').attr('href')+ (0+$('#exp_member_table tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#exp_member_table tbody tr').length),
    success: function(html)
    {
      $('#exp_member_table tbody').append(html);
      $.fn.catalogue_people.reorder($('#exp_member_table'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
    }
  });
  return true;
}


$("#exp_member_table").catalogue_people({add_button: 'a.add_member',update_row_fct: addMember,q_tip_text : '<?php echo __("Choose a Member") ;?>' });


});

</script>
