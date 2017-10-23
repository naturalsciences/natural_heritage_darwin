<?php include_javascripts_for_form($form) ?>
<div id="catalogue_people_screen">
<?php echo form_tag('cataloguepeople/people?table='.$sf_params->get('table').($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form','id'=>'cataloguepeople_form'));?>
<table >
  <tr>
      <td colspan="2">
        <?php echo $form->renderGlobalErrors() ?>
      </td>
  </tr>
  <tr>
    <th><?php echo $form['people_type']->renderLabel();?>:</th>
    <td>
      <?php echo $form['people_type']->renderError(); ?>
      <?php echo $form['people_type'];?>
    </td>
  </tr>
  <tr>
    <th><?php echo $form['people_sub_type']->renderLabel();?>:</th>
    <td>
      <?php echo $form['people_sub_type']->renderError(); ?>
      <?php echo $form['people_sub_type'];?>
    </td>
  </tr>

  <tr>
    <th><?php echo $form['people_ref']->renderLabel();?>:</th>
    <td>
      <?php echo $form['people_ref']->renderError(); ?>
      <?php echo $form['people_ref'];?>
    </td>
  </tr>
  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=catalogue_people&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
          <?php endif; ?>
          <input id="save" name="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
  </tfoot>  
</table>
</form>

<script language="text/javascript">

function toggleChangingChoice()
{
  if($('#catalogue_people_people_type').val() =='author')
  {
		$('#catalogue_people_people_sub_type_parent .change_item_button:visible').click();
  	$('#catalogue_people_people_sub_type_parent .add_item_button').hide();
	
  	$('.both_search_institutions').addClass('disabled');
	
  	$('.both_search_people').click();

  }
  else
  {
	  $('.both_search_people').click();
	  $('#catalogue_people_people_sub_type_parent .add_item_button').show();
	  $('.both_search_institutions').removeClass('disabled');
  }
}

  $(document).ready(function () {
    $('form.qtiped_form').modal_screen();
    
    toggleChangingChoice();

    $('.result_choose').live('click',function () {
       el = $(this).closest('tr');
       $("#catalogue_people_people_ref").val(getIdInClasses(el));
       $("#catalogue_people_people_ref_name").val(el.find('.item_name').text()).show();
       $('.reference_clear').show();
       $('div.search_box, ul.tab_choice').slideUp();
    });

    $('#catalogue_people_people_type').change(function() {
      toggleChangingChoice();
      $.get("<?php echo url_for('cataloguepeople/getSubType');?>/type/"+$(this).val(), function (data) {
	$("#catalogue_people_people_sub_type").html(data);
      });
    });

    $('.both_search_people').click(function()
    {
      $('.both_search_institutions').removeClass('activated');

      $(".search_box").html('<img src="/images/loader.gif" />');

      people_search_url = '<?php echo url_for('people/choose?with_js=0&is_choose=1');?>';
      $('.both_search_people').addClass('activated');
      $.ajax({
	  type: "POST",
	  url: people_search_url,
	  success: function(html){
	    $('.search_box').html(html);
	  }
      });
    });
    
    $('.both_search_institutions').click(function()
    {
      if( $(this).hasClass('disabled')) return false;
      $('.both_search_people').removeClass('activated');

      $(".search_box").html('<img src="/images/loader.gif" />');

      $('.both_search_institutions').addClass('activated');

      $.ajax({
	  type: "POST",
	  url: '<?php echo url_for('institution/choose?with_js=0&is_choose=1');?>',
	  success: function(html){
	    $('.search_box').html(html);
	  }
      });
    });

    $('.both_search_people').click();

  });
</script>


<ul class="tab_choice hidden">
  <li class="both_search_people"><?php echo __('People');?></li>
  <li class="both_search_institutions"><?php echo __('Institution');?></li>
</ul>
<div class="search_box">
  <?php echo __("Choose a type");?>
</div>

</div>
