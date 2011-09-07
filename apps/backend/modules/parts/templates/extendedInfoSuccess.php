<table class="extended_info">
  <tr>
	<th><?php echo __('Count');?></th>
	<td><?php if($part->getSpecimenPartCountMin() == $part->getSpecimenPartCountMax()):?>
		  <?php echo $part->getSpecimenPartCountMin();?>
	  <?php else:?>
		<?php echo __('Between %min% and %max%',array('%min%'=>$part->getSpecimenPartCountMin(),'%max%'=>$part->getSpecimenPartCountMax()));?>
	  <?php endif;?>
	</td>
  </tr>

  <tr>
	<th><?php echo __('Codes');?></th>
	<td><?php if(isset($codes[$part->getId()])):?>
		  <ul><?php foreach($codes[$part->getId()] as $code):?>
			<li><?php echo $code->getCodeFormated();?></li>
		  <?php endforeach;?></ul>
		<?php endif;?>
	</td>
  </tr>

  <tr><td colspan="2"><hr /></td><tr>

  <tr>
	<th><?php echo __('Container');?></th>
	<td><?php echo $part->getContainer();?></td>
  </tr>
  <tr>
	<th><?php echo __('Container Type');?></th>
	<td><?php echo $part->getContainerType();?></td>
  </tr>
  <tr>
	<th><?php echo __('Container Storage');?></th>
	<td><?php echo $part->getContainerStorage();?></td>
  </tr>
  <tr>
	<th><?php echo __('Sub Container');?></th>
	<td><?php echo $part->getSubContainer();?></td>
  </tr>
  <tr>
	<th><?php echo __('Sub Container Type');?></th>
	<td><?php echo $part->getSubContainerType();?></td>
  </tr>
  <tr>
	<th><?php echo __('Sub Container Storage');?></th>
	<td><?php echo $part->getSubContainerStorage();?></td>
  </tr>

  <tr><td colspan="2"><hr /></td><tr>

  
  <tr>
	<th><?php echo __('supernumerary');?></th>
	<td><?php if($part->getSurnumerary()):?><?php echo __('Yes');?><?php else:?><?php echo __('No');?><?php endif;?></td>
  </tr>

  <tr>
	<th><?php echo __('Status');?></th>
	<td><?php echo $part->getSpecimenStatus();?></td>
  </tr>
  <tr>
	<th><?php echo __('Complete');?></th>
	<td><?php if($part->getComplete()):?><?php echo __('Yes');?><?php else:?><?php echo __('No');?><?php endif;?></td>
  </tr>
</table>
