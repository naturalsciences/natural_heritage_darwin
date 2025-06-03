<tr>
  <td>
    <?php echo image_tag('info.png',"title=info class=extd_info");?>
    <div class="extended_info" style="display:none;">
      <dl>
          <dt><?php echo __('Id :');?></dt>
          <dd><?php echo $item->getId();?></dd>
          
      </dl>
    </div>
  </td>
  <td>
    <?php echo $item->getId();?>
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
		
		<div class="add_country">
		<?php print($mode);?>
		<?php print($form->getObject()->isNew());?>
		<?php print($form->getObject()->getId());?>
           <a class="select_gtu_for_georef" href="<?php echo url_for("georef/addGtuToGeoref?".($mode=="edit"?'georef_ref='.$form->getObject()->getId()."&":"")."gtu_ref=".$item->getId() );?>/num/" id="add_gtu"><?php echo __('Select');?></a>
		<div>
  </td>  
  <td>
    <input name="mass_action[item_list][]" type="hidden" value="<?php echo $item->getId(); ?>" class="item_row">
    <a class="row_delete" href="#" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?></a>
  </td>
</tr>
