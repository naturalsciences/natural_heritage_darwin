<tr class="georeflines_top" >
  <td colspan="9"></td>
</tr>
<tr class="line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> main_line_georef">
  <td>
	<?php print($form["georef_ref"]); print($item->getId()); ?>
	
  </td>
  <td>
	<?php print($form["gtu_ref"]); ?>
  </td>
  
   <td>
    <?php echo $item->getCode();?>
  </td>
  <td>
    <?php echo $item->getName(ESC_RAW);?>
  </td>
  <td>
  <!--ftheeten 2018 12 2-->
		    <?php if(strlen(trim($item->getComments()))>0):?>
            <div>
					 <ul class='search_tags'>
                     <li>
                        <ul class='name_tags_view'>
                         <?php if(strlen(trim($item->getComments()))>0):?>
                            <?php foreach(explode("|", $item->getComments()) as $comment):?>
                            <li><?php print($comment);?></li>
                            <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                        </li>
                    </ul>
			        <br/>

                   </ul>
             </div>
             <?php endif;?>
             <?php if(strlen(trim($item->getProperties()))>0):?>
            <div>
					 <ul class='search_tags'>
                     <li>
                        <ul class='name_tags_view'>
                         <?php if(strlen(trim($item->getProperties()))>0):?>
                            <?php foreach(explode("|", $item->getProperties()) as $property):?>
                            <li><?php print($property);?></li>
                            <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                        </li>
                    </ul>
			        <br/>

                   </ul>
             </div>
             <?php endif;?>
	<td>
	<?php if($georef_ref_id!=-1):?>
	<td>
		 <a  id_rel_dw="<?php print($georef_ref_id);?>" class="row_delete_lk_georef" href="#" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?></a>
	</td>
	<?php endif; ?>
</tr>