<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'maintenances','eid'=> $maintenance->getId() , 'view' => true)); ?>
<?php slot('title', __('View Maintenance'));  ?>
<div class="page">
  <h1><?php echo __('View Maintenance');?></h1>
  <div class="table_view">
    <table>
      <tbody>
        <tr>
              <th><?php echo __('Type');?></th>
              <td><?php echo __($maintenance->getCategory()) ; ?></td>
        </tr>
        <tr>
              <th><?php echo __('Action / Observation');?></th>
              <td><?php echo __($maintenance->getActionObservation()); ?></td>
        </tr>
        <tr>
              <th><?php echo __('Last update date');?></th>
              <td><?php echo $maintenance->getModificationDateTimeMasked(ESC_RAW); ?>
              </td>
        </tr>
        <tr>
              <th><?php echo __('Person');?></th>
              <td>
                <?php echo $maintenance->People->getFormatedName(); ?>
              </td>
        </tr>
        <tr>
              <th><?php echo __('Description');?></th>
              <td>
                <?php echo $maintenance->getDescription(); ?>
              </td>
        </tr>
      </tbody>
    </table>
    <input type="button" class="bt_close" value="<?php echo __('Close this tab'); ?>">
    <script>
      $('input[class="bt_close"]').click(function(){
        window.close() ;
      });
    </script>     
  </div> 
  <div>
     <?php include_partial('widgets/screen', array(
	   'widgets' => $widgets,
	   'category' => 'maintenanceswidgetview',
	   'columns' => 1,
	   'options' => array('view' => true, 'eid' => $maintenance->getId())
	   )); ?>
 </div>
</div>	
