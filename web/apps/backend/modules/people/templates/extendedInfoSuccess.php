<table class="extended_info">
  <tr>
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
  </tr> 
</table>
