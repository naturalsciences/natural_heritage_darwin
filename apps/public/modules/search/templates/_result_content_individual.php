<td class="col_individual_type">
  <?php if($specimen->getTypeSearch() != 'specimen') : ?>
    <?php echo ucfirst($specimen->getTypeSearch());?>
  <?php endif ; ?>
</td>
<td class="col_sex">
  <?php echo ($specimen->getSex()=="undefined")?"":ucfirst($specimen->getSex()) ; ?>
</td>
<td class="col_state">
  <?php echo ($specimen->getState()=="not applicable")?"":ucfirst($specimenSpecimenIndividuals[0]->getState());?>
</td> 
<td class="col_stage">
  <?php echo ($specimen->getStage()=="undefined")?"":ucfirst($specimen->getStage()) ; ?>
</td>
<td class="col_social_status">
  <?php echo ($specimen->getSocialStatus()=="not applicable")?"":ucfirst($specimen->getSocialStatus());?>
</td> 
<td class="col_rock_form">
  <?php echo ($specimen->getRockForm()=="not applicable")?"":ucfirst($specimen->getRockForm());?>
</td> 
<td class="col_individual_count right_aligned">
  <?php if($specimen->getSpecimenIndividualsCountMin() != $specimen->getSpecimenIndividualsCountMax()):?>
    <?php echo $specimen->getSpecimenIndividualsCountMin() . ' - '.$specimen->getSpecimenIndividualsCountMax();?>
  <?php else:?>
    <?php echo $specimen->getSpecimenIndividualsCountMin();?>
  <?php endif;?>
</td>
