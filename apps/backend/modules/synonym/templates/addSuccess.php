<?php include_javascripts_for_form($form) ?>
<div id="syn_screen">

<?php echo form_tag('synonym/add?table='.$sf_params->get('table'). '&id='.$sf_request->getParameter('id') , array('class'=>'edition qtiped_form','id'=>'synonym_form'));?>
<table >
  <tr>
      <td colspan="2">
        <?php echo $form->renderGlobalErrors() ?>
      </td>
  </tr>
  <tr>
    <th><?php echo $form['group_name']->renderLabel();?></th>
    <td>
      <?php echo $form['group_name']->renderError(); ?>
      <?php echo $form['group_name'];?>
    </td>
  </tr>
  <tr>
    <th><?php echo $form['record_id']->renderLabel('Item');?></th>
    <td>
      <?php echo $form['record_id']->renderError(); ?>
      <?php echo $form['record_id'];?>
      <span class="synonym_name"></span>
    </td>
  </tr>

  <tr class="<?php if(! $form['merge']->hasError() || $form['record_id']->hasError()) echo 'hidden';?> merge_question">
   <th><?php echo $form['merge']->renderLabel('Confirm');?></th>
   <td>
      <?php echo $form['merge']->renderError(); ?>
      <div><?php echo __('This element already has synonyms.<br />Are you sure that you want to merge them together?');?></div>
      <?php echo __('Yes ');?> <?php echo $form['merge'];?>
    </td>
  </tr>

  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <input id="save" name="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
  </tfoot>  
</table>
</form>

<script type="text/javascript">

function checkGroup()
{
  if($("#classification_synonymies_record_id").val() == '' || typeof $("#classification_synonymies_record_id").val() == 'undefined')
    return;

  $('#save').attr("disabled","disabled");
  $.ajax({
      url: '<?php echo url_for('synonym/checks?table='.$sf_request->getParameter('table'))?>/id/'+$("#classification_synonymies_record_id").val()+'/type/'+$("#classification_synonymies_group_name").val(),
      success: function(html){
        $('#save').removeAttr("disabled");
        $('.merge_question .error_list').hide();
        if(html == "0" )
        {
          $(".merge_question").hide();
          $(".merge_question input").attr('checked', 'checked');
        }
        else
        {
          $(".merge_question input").removeAttr('checked');
          $(".merge_question").show();
        }
      },
      error: function(xhr)
      {
        $('#save').removeAttr("disabled");
      }
   });
}

$(document).ready(function () 
{
  $('form.qtiped_form').modal_screen();

    $('.result_choose').live('click',function () {
      el = $(this).closest('tr');
      $("#classification_synonymies_record_id").val(getIdInClasses(el));
      $("#classification_synonymies_record_id_name").val(el.find('.item_name').text()).show();
      $('.reference_clear').show();
      $('div.search_box').slideUp();
      checkGroup();
    });

    $('#classification_synonymies_group_name').change(checkGroup);

});
</script>

<div class="search_box">
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm, 'is_choose' => true)) ?>
</div>
</div>
