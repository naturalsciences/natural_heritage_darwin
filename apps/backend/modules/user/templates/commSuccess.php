<?php include_javascripts_for_form($form) ?>
<div id="comm_screen">
<?php echo form_tag('user/comm?ref_id='.$sf_request->getParameter('ref_id') . ($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form','id'=>'comm_form'));?>
<?php echo $form['person_user_ref'];?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['comm_type']->renderLabel();?></th>
      <td>
        <?php echo $form['comm_type']->renderError(); ?>
        <?php echo $form['comm_type'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['entry']->renderLabel();?></th>
      <td>
        <?php echo $form['entry']->renderError(); ?>
        <?php echo $form['entry'];?>
      </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['tag']->renderLabel();?></th>
      <td>
        <?php echo $form['tag']->renderError(); ?>
        <?php echo $form['tag'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
          <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=users_comm&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
        <?php endif;?> 
        <input id="submit" type="submit" value="<?php echo __('Save');?>" />
      </td>
    </tr>
  </tfoot>
</table>

</form>

<script  type="text/javascript">
  $(document).ready(function () {
    $('form.qtiped_form').modal_screen();

    $('#users_comm_comm_type').change(function () {
      $('form#lang_form input[type=submit]').attr('disabled','disabled');
      $.ajax({
	      type: "get",
        url: "<?php echo url_for('user/getTags');?>",
        data: { type: $('#users_comm_comm_type').val() } ,
	      success: function(html){
	        $('#users_comm_tag').val('');
	        $('#users_comm_tag_selected').html('');
	        $('#users_comm_tag_available').html(html);
	        $('#users_comm_tag_available li').bind('click',function() {	   
	          new_elem = $('<li class="'+$(this).attr('class')+'" alt="'+$(this).attr('alt')+'">'+$(this).text()+'<img src="/images/widget_help_close.png"></li>');
            $('#users_comm_tag_selected').append(new_elem);
            value = trim($(this).attr('alt').substr(2));
            $(this).addClass('hidden');
            if($('#users_comm_tag').val() =='')
              $('#users_comm_tag').val(value) ;
            else
              $('#users_comm_tag').val( $('#users_comm_tag').val() + ',' + value);
	          new_elem.find('img').click(remove_tag);
          });    	  
	      }
      });
      $('#users_comm_tag_selected li').each(function() {	   
        $(this).remove_tag ;
       });
    });
  });
  
function remove_tag() {
  avail_el = $('#users_comm_tag_available [alt$="'+$(this).parent().attr('alt')+'"]');

  $(this).parent().remove();
  avail_el.removeClass('hidden');
  value = trim(avail_el.attr('alt').substr(2));
  old_value = $('#users_comm_tag').val();
  old_value = old_value.replace(value,'');
  old_value = old_value.replace(/,,/g, ',');
  old_value = old_value.replace(/^,/,'');
  old_value = old_value.replace(/,$/,'');
  $('#users_comm_tag').val(old_value);
}
</script>
</div>
