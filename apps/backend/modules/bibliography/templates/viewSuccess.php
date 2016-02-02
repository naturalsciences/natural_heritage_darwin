<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_bibliography','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Bibliography'));  ?>
<div class="page">
    <h1><?php echo __('View Bibliography');?></h1>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th><?php echo $form['type']->renderLabel() ?></th>
        <td>
          <?php echo $bibliography->getTypeFormatted(); ?>
        </td>
      </tr>

      <tr>
        <th><?php echo $form['title']->renderLabel() ?></th>
        <td>
          <?php echo $bibliography->getTitle(); ?>
        </td>
      </tr>

      <tr>
        <th><?php echo $form['year']->renderLabel() ?></th>
        <td>
          <?php echo $bibliography->getYear(); ?>
        </td>
      </tr>

      <tr>
        <th><?php echo $form['abstract']->renderLabel() ?></th>
        <td>
          <?php echo $bibliography->getAbstract(); ?>
        </td>
      </tr>

      <tr>
        <td colspan="2" class="search_form">
        <fieldset>
        <legend><?php echo __('Authors') ; ?></legend>
          <ul>
           <?php foreach($form['Authors'] as $form_value):?>
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
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'bibliography', 'view' => true)
	)); ?>
</div>
