  <tr id="<?php echo $form['user_ref']->getValue() ;?>">
    <td>
      <?php echo $form['user_ref']->renderError(); ?>
      <?php echo $form['user_ref'];?>
      <?php echo $form['user_ref']->renderLabel();?>
      <?php echo help_ico($form['user_ref']->renderHelp(),$sf_user);?>
    </td>
    <td>
      <?php echo $form['db_user_type']->renderError(); ?>
      <?php echo $form['db_user_type'];?>
    </td>
    <?php if($ref_id != "") : ?>
    <td>
      <div>
	    <a id="subcol" class='set_rights'
       href="<?php echo url_for('collection/rights?user_ref='.$form['user_ref']->getValue().'&collection_ref='.$ref_id);?>"
       name="<?php echo __('List of sub collections') ; ?>"><?php echo __('On sub collections...');?></a>
	   </div>
    </td>
    <td>
      <div <?php if($form['db_user_type']->getValue() != Users::REGISTERED_USER) echo "class='tree'" ; ?>>
	    <a id="widget_<?php echo $form['user_ref']->getValue() ;?>" class='set_rights'
        href="<?php echo url_for('collection/widgetsRight?user_ref='.$form['user_ref']->getValue().'&collection_ref='.$ref_id);?>"
        name="<?php echo __('List of Widgets') ; ?>"><?php echo __('Manage widgets');?></a>
	   </div>
    </td>
	<td>
		<div id="widget_profile_ref">
			<?php echo $form['widget_profile_ref'];?>
		</div>
	<td>
    <?php else : ?>
      <td colspan="2">&nbsp;</td>
    <?php endif ; ?>
    <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_coll'); ?>
      <script>
        $("tr#<?php echo $form['user_ref']->getValue() ;?>").find('select').change(function(){
          button_reg_user = $('#widget_<?php echo $form['user_ref']->getValue() ;?>').closest('div') ;
          if($(this).val() == 1)
          {
            button_reg_user.removeClass('tree') ;
          }
          else
          {
            button_reg_user.addClass('tree') ;
          }
        }) ;
      </script>
    </td>
  </tr>

