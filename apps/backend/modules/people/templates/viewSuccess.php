<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'people','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View People'));  ?>
<div class="page">
    <h1><?php echo __('View People');?></h1>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th class="top_aligned"><?php echo $form['title']->renderLabel() ?></th>
        <td>
          <?php echo $form['title']->renderError() ?>
          <?php echo $people->getTitle() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['given_name']->renderLabel() ?></th>
        <td>
           <?php echo $people->getGivenName() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['family_name']->renderLabel() ?></th>
        <td>
          <?php echo $people->getFamilyName() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['additional_names']->renderLabel() ?></th>
        <td>
           <?php echo $people->getAdditionalNames() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['gender']->renderLabel() ?></th>
        <td>
           <?php echo $people->getGender() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['birth_date']->renderLabel() ?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getBirthDate()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getBirthDate()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
      </tr>
      <tr>
        <th><?php echo $form['end_date']->renderLabel() ?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getEndDate()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getEndDate()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
      </tr>
      <tr>
        <th><?php echo $form['activity_date_from']->renderLabel() ?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateFrom()->getRawValue()) != '0001/01/01') : ?>        
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateFrom()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>          
        </td>
      </tr>
      <tr>
        <th><?php echo $form['activity_date_to']->renderLabel() ?></th>
        <td>
          <?php if (FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateTo()->getRawValue()) != '0001/01/01') : ?>
            <?php echo FuzzyDateTime::getDateTimeStringFromArray($people->getActivityDateTo()->getRawValue()) ?>
          <?php else : ?>
          -
          <?php endif ?>
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
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'people', 'view' => true)
	)); ?>
</div>
</div>
