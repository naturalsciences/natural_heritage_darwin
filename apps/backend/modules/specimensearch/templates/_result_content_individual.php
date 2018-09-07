<td class="col_individual_type">
  <?php if($specimen->getTypeGroup() != 'specimen') : ?>
    <?php echo $specimen->getTypeGroup();?>
  <?php endif ; ?>
</td>
<td class="col_sex"><?php echo ucfirst(($specimen->getSex()=='undefined')?'':$specimen->getSex());?></td> 
<td class="col_state"><?php echo ucfirst(($specimen->getState()=='not applicable')?'':$specimen->getState());?></td> 
<td class="col_stage"><?php echo ucfirst(($specimen->getStage()=='undefined')?'':$specimen->getStage());?></td> 
<td class="col_social_status"><?php echo ucfirst(($specimen->getSocialStatus()=='not applicable')?'':$specimen->getSocialStatus());?></td> 
<td class="col_rock_form"><?php echo ucfirst(($specimen->getRockForm()=='not applicable')?'':$specimen->getRockForm());?></td> 
