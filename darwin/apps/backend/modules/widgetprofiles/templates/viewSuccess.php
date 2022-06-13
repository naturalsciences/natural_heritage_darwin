

<div class="page">
    <h1><?php echo __('View Widget Profile data');?></h1>
     <div class="table_view">
            <table class="classifications_edit">
                <tbody>
                    <tr>
                        <th> <?php echo __('Name') ?> </th>
                        <td> <?php echo $widgetprofile->getName(); ?> </td>
                    </tr>
                    <tr>
                        <th> <?php echo __('Creator ref') ?> </th>
                        <td> <?php echo $widgetprofile->getUsers()->getFormatedName(); ?> </td>
                    </tr>
                    <tr>
                        <th> <?php echo __('Creation date') ?> </th>
                        <td> <?php $tmp= new DateTime($widgetprofile->getCreationDate()); echo $tmp->format('d/m/Y') ; ?> </td>
                    </tr>
   
                    <?php if($sf_user->isAtLeast(Users::MANAGER)):?>
                    <tr><td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this item'),'widgetprofiles/edit?id='.$widgetprofile->getId());?></td></tr>
                    <?php endif;?>
                 </tbody>
            </table>
      </div> 
  
  
  
       <div class="table_view">
            <!-- <table class="classifications_edit"> -->
            <table class="widget_right edition" width='100%'>
            <thead class="title_widget">
              <tr>
  	          <th><?php echo __("Category/screen");?></th><th><?php echo __("Name");?></th><th colspan="6"><?php echo __("Widget");?></th>
              </tr>
          		<tr>
  			         <td class='head_widget' colspan="2" >&nbsp;</td>
  			         <th class='head_widget' style="color:black"><?php echo __("Deactivated");?>  </th>
  			   			 <th class='head_widget' style="color:black"><?php echo __("Activated");?>    </th>
                 <th class='head_widget' style="color:black"><?php echo __("Visible");?>      </th>
  			         <th class='head_widget' style="color:black"><?php echo __("Opened");?>       </th>
  			         <th class='head_widget' style="color:black"><?php echo __("Custom title");?> </th>
  		        </tr>	             
            </thead>
                <tbody>
                    <?php foreach($widgetprofiledefinitions as $item):?>
                    <tr>
                        <th> <?php echo $item->getCategory(); ?> </th>
                        <th> <?php echo $item->getGroupName(); ?> </th>
                      <!--  <td> <?php echo $item->getId(); ?> </td>  <td> <?php echo $item->getVisible(); ?> </td> <td> <?php echo $item->getOpened(); ?> </td>  <td> <?php echo $item->getIsAvailable(); ?>   </td>  -->
                        <?php if ($item->getMandatory() ) : ?>
                           <th colspan="4" class='widget_selection'>----- <?php echo __('Mandatory') ; ?> -----</th>
                        <?php else : ?>
                           <?php if ($item->getWidgetField()=='unused' ) : ?>
                              <th class='widget_selection'> <?php echo __('  X  ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                           <?php endif ; ?>
                           <?php if ($item->getWidgetField()=='is_available' ) : ?>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('  X  ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                           <?php endif ; ?>
                           <?php if ($item->getWidgetField()=='visible' ) : ?>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('  X  ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                           <?php endif ; ?>
                           <?php if ($item->getWidgetField()=='opened' ) : ?>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('    ') ; ?> </th>
                              <th class='widget_selection'> <?php echo __('  X  ') ; ?> </th>
                           <?php endif ; ?>                           	
                        <?php endif ; ?>	
                        <td> <?php echo $item->getWidgetField(); ?>        </td>
 
                    </tr>
                    <?php endforeach;?>
   

                 </tbody>
            </table>
      </div> 
  
  
  
  
</div>