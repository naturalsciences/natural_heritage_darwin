<?php slot('title', __('Edit User widgets'));  ?>        
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>                                                                                              
<div class="page">
  <h1 class="edit_mode"><?php echo __("List of widgets available for ".$profile->getName());?></h1>
  <form action="" method="post">
  <table class="edition" width='100%'>
    <tfoot>
      <tr>
        <td> 
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table> 
  <table class="widget_right edition" width='100%'>      
  <thead class="title_widget">
  <tr>
  	<th><?php echo __("Category/screen");?></th><th><?php echo __("Name");?></th><th colspan="6"><?php echo __("Widget");?></th>
  </tr>
  </thead>
  <?php foreach($form_pref as $category=>$record) :?>
  	<thead alt="<?php echo $category ?>" class='head_widget'>
  		<tr>
  			<td colspan="2" class='head_widget'>&nbsp;</td>
  			
			<th class='head_widget'><?php echo __("Deactivated");?><br /><input type="radio" name="<?php echo('All_'.$category) ; ?>" value="unused"></th>
  			
  			<th class='head_widget'><?php echo __("Activated");?><br /><input type="radio" name="<?php echo('All_'.$category) ; ?>" value="is_available"></th>
  			<th class='head_widget'><?php echo __("Visible");?><br /><input type="radio" name="<?php echo('All_'.$category) ; ?>" value="visible"></th>
  			<th class='head_widget'><?php echo __("Opened");?><br /><input type="radio" name="<?php echo('All_'.$category) ; ?>" value="opened"></th>
  			<th class='head_widget'><?php echo __("Custom title");?></th>
  		</tr>	
	</thead>	
	<tbody alt="<?php echo $category ?>" class='widget_selection'>
	<?php foreach($record as $i=>$widget) :?>
		<tr>
		    
			<?php if($i==0):?>
			<?php echo("<th rowspan='".count($record)."' >".__($category)."</th>") ; ?>
		    <?php endif;?>
		   
		    <th>
		      <?php echo $widget['widget_choice']->renderLabel() ?>
		      <?php echo $widget->renderHiddenFields(); ?>
		    </th>
		    <?php if ($form->getEmbeddedForm('WidgetProfilesDefinition')->getEmbeddedForm($widget->getName())->getObject()->getMandatory() ) : ?>
			    <th colspan="4" class='widget_selection'>----- <?php echo __('Mandatory') ; ?> -----</th>
		    <?php else : ?>
			      <?php echo $widget['widget_choice']->renderError() ?>
			      <?php echo $widget['widget_choice'] ?>
		    <?php endif ; ?>	
		    <td class='widget_selection'><?php echo $widget['title_perso']->renderError() ?>
			<?php echo $widget['title_perso'] ?>
		    </td>	
		</tr>
	<?php endforeach ?>
	</tbody>
  <?php endforeach ; ?>
</table>
<table class="edition" width='100%'>  
<tfoot>
  <tr>
    <td colspan="6"> 
     
      <input id="submit" type="submit" value="<?php echo __('Save');?>" />
    </td>
  </tr>
</tfoot>
 </table>
</form>
</div>
<script>
$(document).ready(function () {
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  });
  $('thead input[type=radio]').change(function()
  {
    alt_val = $(this).closest('thead').attr('alt');
    $('tbody[alt="'+alt_val+'"] tr input[value="'+$(this).val()+'"]').attr("checked","checked");
  });
});  
</script>
