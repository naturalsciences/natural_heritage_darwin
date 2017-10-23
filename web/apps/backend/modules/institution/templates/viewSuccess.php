<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'people_institution','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Institution'));  ?>
<div class="page">
    <h1><?php echo __('View Institution');?></h1>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th class="top_aligned"><?php echo $form['family_name']->renderLabel('Name') ?></th>
        <td>
          <?php echo $form['family_name']->renderError() ?>
          <?php echo $instit->getFamilyName() ?>
        </td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo $form['additional_names']->renderLabel('Abbreviation') ?></th>
        <td>
           <?php echo $instit->getAdditionalNames() ?>
        </td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo $form['sub_type']->renderLabel() ?></th>
        <td>
          <?php echo $instit->getSubType() ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>  
<div class="view_mode">
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'peoplewidgetview',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'institution', 'view' => true)
	)); ?>
</div>
</div>
