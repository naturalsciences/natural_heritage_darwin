<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'maintenances','eid'=> $maintenance->getId() , 'view' => true)); ?>
<div class="page">
  <h1><?php echo __('View Maintenance');?></h1>
  <div class="table_view">
    <table>
      <tbody>
        <tr>
              <th><?php echo __('Type');?></th>
              <td><?php echo $maintenance->getCategory() ; ?></td>
        </tr>
        <tr>
              <th><?php echo __('Action observation');?></th>
              <td><?php echo $maintenance->getActionObservation() ?></td>
        </tr>
        <tr>
              <th><?php echo __('Modification date time');?></th>
              <td><?php $maintenance->getModificationDateTime() ?>
              </td>
        </tr>
        <tr>
              <th><?php echo __('People ref');?></th>
              <td>
                <?php echo $maintenance->People->getFormatedName() ?>
              </td>
        </tr>
        <tr>
              <th><?php echo __('Description');?></th>
              <td>
                <?php echo $maintenance->getDescription() ?>
              </td>
        </tr>
      </tbody>
    </table>
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

