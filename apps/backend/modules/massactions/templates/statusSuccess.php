<?php slot('title','Successfly updated');?>
<div class="page" id="mass_action_status">
  <h1><?php echo __('Action Status :');?></h1>
  <div>
    <?php echo format_number_choice('[0] No Item modified|[1] Everything seems to go well. Your action was applied to 1 record |(1,+Inf] Everything seems to go well. Your action was applied to %1% records', array('%1%' =>  $nb_items), $nb_items);?>
  </div>
  <p>
    <?php echo link_to('Do another action','massactions/index');?>
    <?php echo link_to('Go to the board','@homepage');?>
  </p>
</div>
