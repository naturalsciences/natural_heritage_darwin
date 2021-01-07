<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_bibliography','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Bibliography'));  ?>
<div class="page">
    <h1><?php echo __('View Bibliography');?></h1>
	 <div>
          <b>Edit : </b><?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'bibliography/edit?id='.$form->getObject()->getId()); ?>
    <br/>
	</div>
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
        <th><?php echo $form['reference']->renderLabel() ?></th>
        <td>
          <?php echo $bibliography->getReference(); ?>
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
        <th><?php echo $form['uri']->renderLabel() ?></th>
   
	    <td>
			  <?php print($bibliography->getUriProtocol());?>
			  <?php if(strtolower($bibliography->getUriProtocol())=="doi"):?>
			   <a href="https://dx.doi.org/<?php  print($bibliography->getUri()); ?>" target="_blank"><?php  print($bibliography->getUri()); ?></a>
			  <?php elseif(strtolower($bibliography->getUriProtocol())=="url"):?>
			   <a href="<?php  print($bibliography->getUri()); ?>" target="_blank"><?php  print($bibliography->getUri()); ?></a>
			 <?php else:?>
			 <?php>  <?php  print($bibliography->getUri()); ?></a>
			  
			 <?php endif;?>
        </td>
      </tr>
	  <tr>
        <th><?php echo __("Related specimens") ?></th>
   
	    <td>
			 <a href=<?php print(url_for("specimensearch/search")."/1?&specimen_search_filters[publication_ref]=".$bibliography->getId()."&specimen_search_filters[rec_per_page]=10&submit=Search'"); ?> target="_blank"><?php print(image_tag('link.png',array('title'=>'Linked specimen')));?></a>	
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
