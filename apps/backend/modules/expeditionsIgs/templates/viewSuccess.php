<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_expeditions','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Expeditions'));  ?>
<div class="page">
    <h1><?php echo __('View Expeditions');?></h1>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $expedition->getName(); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['expedition_from_date']->renderLabel() ?></th>
        <td>
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($expedition->getExpeditionFromDate()->getRawValue()) ?>
        </td>
      </tr>      
      <tr>
        <th><?php echo $form['expedition_to_date']->renderLabel() ?></th>
        <td>
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($expedition->getExpeditionToDate()->getRawValue()) ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="search_form">
        <fieldset>
        <legend><?php echo __('Members') ; ?></legend>
          <ul>
           <?php foreach($form['Members'] as $form_value):?>       
              <?php echo ("<li><a href='".url_for("people/view?id=".$form_value['people_ref']->getValue())."'>".$form_value['people_ref']->renderLabel()."</a></li>") ; ?>
           <?php endforeach ; ?>
          </ul>
        </fieldset>
        </td>
      </tr>     
    </tbody>
  </table>
</div>  
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidgetview',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'expeditions', 'view' => true)
	)); ?>
</div>
