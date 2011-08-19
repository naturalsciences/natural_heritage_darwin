<h3>Parts</h3>
<table class="results">
  <thead>
	<tr>
	  <th></th>
	  <th><?php echo __('Code');?></th>
	  <th><?php echo __('Part');?></th>
 	  <?php if ($sf_user->IsAtLEast(Users::ENCODER)) : ?>	  
	    <th><?php echo __('Room');?></th>
	    <th><?php echo __('Row');?></th>
	    <th><?php echo __('Shelf');?></th>
	    <th><?php echo __('Container');?></th>
	    <th><?php echo __('Sub container');?></th>
	  <?php endif ; ?>
	  <th></th>
	</tr>
  </thead>
  <tbody>
  <?php foreach($parts as $part):?>
	<tr>
    <td>
  	  <?php if ($sf_user->IsAtLEast(Users::ENCODER)) : ?>
  	    <?php echo image_tag('info.png',"class=extd_info_".$part->getId());?>
    		<div class="extended_info" style="display:none;">
		    </div>
        <script  type="text/javascript">
          $(".extd_info_<?php echo $part->getId();?>").qtip({
            show: { solo: true, event:'mouseover' },
            hide: { event:'mouseout' },
            style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
            content: {
              text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
              title: { text: '<?php echo __("Linked Info") ; ?>' },
              ajax: {
                url: '<?php echo url_for("parts/extendedInfo");?>',
                type: 'GET',
                data: { id: '<?php echo $part->getId() ; ?>' }
              }
            }
          });        
        </script>		    
		  <?php endif ; ?>
	  </td>
	  <td><?php if(isset($codes[$part->getId()])):?>
		  <?php foreach($codes[$part->getId()] as $code):?>
  			<p><?php echo $code->getCodeFormated();?></p>
		  <?php endforeach;?>
		<?php endif;?>
	  </td>
	  <td><?php echo $part->getSpecimenPart();?></td>
	  <?php if ($sf_user->IsAtLEast(Users::ENCODER)) : ?>	  
	  <td><?php echo $part->getRoom();?></td>
	  <td><?php echo $part->getRow();?></td>
	  <td><?php echo $part->getShelf();?></td>
	  <td><?php echo $part->getContainer();?></td>
	  <td><?php echo $part->getSubContainer();?></td>
	  <?php endif ; ?>
	  <td>
	    <?php if($user_allowed) : ?>
		    <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),'parts/edit?id='.$part->getId(), array('title'=>__('Edit this part')));?>
		    <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate this part"))),'parts/edit?indid='.$part->getSpecimenIndividualRef().
		    '&duplicate_id='.$part->getId(), array('title'=>__('Duplicate this part'), 'class' => 'duplicate_link'));?>
		  <?php endif ; ?>
		  <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'parts/view?id='.$part->getId(), array('title'=>__('View this part')));?>
	  </td>
	</tr>
  <?php endforeach;?>
  </tbody>
</table>
