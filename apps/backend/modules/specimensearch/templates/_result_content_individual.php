<td class="col_individual_type">
  <?php if($individual->getTypeGroup() != 'specimen') : ?>
    <?php echo $individual->getTypeGroup();?>
  <?php endif ; ?>
</td>
<td class="col_sex"><?php echo ucfirst(($individual->getSex()=='undefined')?'':$individual->getSex());?></td> 
<td class="col_state"><?php echo ucfirst(($individual->getState()=='not applicable')?'':$individual->getState());?></td> 
<td class="col_stage"><?php echo ucfirst(($individual->getStage()=='undefined')?'':$individual->getStage());?></td> 
<td class="col_social_status"><?php echo ucfirst(($individual->getSocialStatus()=='not applicable')?'':$individual->getSocialStatus());?></td> 
<td class="col_rock_form"><?php echo ucfirst(($individual->getRockForm()=='not applicable')?'':$individual->getRockForm());?></td> 
<td class="col_individual_count">
  <?php if($individual->getSpecimenIndividualsCountMin() != $individual->getSpecimenIndividualsCountMax()):?>
    <?php echo $individual->getSpecimenIndividualsCountMin() . ' - '.$individual->getSpecimenIndividualsCountMax();?>
  <?php else:?>
    <?php echo $individual->getSpecimenIndividualsCountMin();?>
  <?php endif;?>
</td>
