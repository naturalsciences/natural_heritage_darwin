<table class="extended_info">
  <!--JM Herpers 2018 03 23-->
  <tr>
	  <td>
		<?php echo $people->getTitle().' '.$people->getGivenName().' '.$people->getFamilyName();?>
	  </td>
  </tr>
  <tr>
	  <td>
		<?php  if (count($people_address) > 0):
			echo $people_address[0]['instit'];
		endif ?>
	  </td>
  </tr>
  <tr>
	  <td>
		<?php  if (count($people_address) > 0):
			echo $people_address[0]['address'];
		endif ?>
	  </td>
  </tr>
  <tr>
	  <td>
		<?php  if (count($people_address) > 0):
			echo $people_address[0]['country'];
		endif ?>
	  </td>
  </tr>
  <!--<tr>
	  <th><?php echo __('Title');?></th>
  	<td><?php echo $people->getTitle();?></td>
  </tr>
  <tr>
	  <th><?php echo __('Family name');?></th>
  	<td><?php echo $people->getFamilyName();?></td>
  </tr>  
  <tr>
	  <th><?php echo __('Given Name');?></th>
  	<td><?php echo $people->getGivenName();?></td>
  </tr>  
  
  <tr><td colspan="2"><hr /></td><tr>

  <tr>
  	<th><?php echo __('Birth date');?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getBirthDate()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getBirthDate()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
  </tr> 
  <tr>
  	<th><?php echo __('End date');?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getEndDate()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getEndDate()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
  </tr> 
  <tr>
  	<th><?php echo __('Activity date from');?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateFrom()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateFrom()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
  </tr> 
  <tr>
  	<th><?php echo __('Activity date to');?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateTo()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateTo()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
  </tr> -->
</table>
