 <!--ftheeten 2018 11 29-->
<div style="<?php echo $visibility;?>" class="display_temporal_information_<?php echo $rownum;?>">
<?php if($form->hasError()): ?>
	<div><?php echo $form->renderError();?></div>

  <?php else: ?>
            <div>Date cluster <?php echo $rownum+1;?></div>
			<div style="display:none"><?php echo $form['gtu_ref']; ?></div>
            <div><?php echo $form['from_date']->renderLabel(); ?></div>
			<div><?php echo $form['from_date']->renderError(); ?></div>
            <div><?php echo $form['from_date']; ?></div>
            <div><?php echo $form['to_date']->renderLabel(); ?></div>
            <div><?php echo $form['to_date']->renderError(); ?></div>
            <div><?php echo $form['to_date']; ?></div>

            <div><?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_temporal_information_'.$rownum); ?>
                
           
        
     
  <?php endif;?>



  <script type="text/javascript">
    $(document).ready(function () {
      $("#clear_temporal_information_<?php echo $rownum;?>").click( function()
      {     
             $('.display_temporal_information_<?php echo $rownum;?>').empty();
	        //$('.display_temporal_information_<?php echo $rownum;?>').hide();
            //$("#count_gtu_date").val(parseInt($("#count_gtu_date").val())-1);
	      
      });
    });
  </script>
  </div>
