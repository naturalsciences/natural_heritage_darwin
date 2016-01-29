<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_igs','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View I.G.'));  ?>
<div class="page">
    <h1><?php echo __('View I.G.');?></h1>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th><?php echo __('I.G. number:');?></th>
        <td>
          <?php echo $igs->getIgNum(); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo __('I.G. creation date:'); ?></th>
        <td>
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($igs->getIgDate()->getRawValue()) ?>
        </td>
      </tr>     
    </tbody>
  </table>
</div>  
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidgetview',
	'columns' => 1,
	'options' => array('eid' => $igs->getId(), 'table' => 'igs', 'view' => true)
	)); ?>
</div>
