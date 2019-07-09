<table class="property_values" id="identifications">
  <thead style="<?php echo ($form['SpecimensRelationships']->count() || $form['newSpecimensRelationships']->count())?'':'display: none;';?>">
  </thead>
    <?php $retainedKey = 0;?>
    <?php foreach($form['SpecimensRelationships'] as $form_value):?>  
      <?php include_partial('specimen/specimens_relationships', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newSpecimensRelationships'] as $form_value):?>
      <?php include_partial('specimen/specimens_relationships', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>  
  <tfoot>
    <tr>
      <td colspan='5'>
        <div class="add_code">
          <a href="<?php echo url_for('specimen/addSpecimensRelationships'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_relship"><?php echo __('Add element');?></a>
        </div>
      </td>
    </tr>
  </tfoot>
  <?php if(strpos($_SERVER['REQUEST_URI'], '/edit/')):?>
<?php if(count($spec_related_inverse)>0): ?>
<tr><td>Inverse relationship<td></tr>
<?php endif;  ?>
<?php foreach($spec_related_inverse as $val):?>
  <tr>
    <td><?php echo $val->getRelationshipType() ; ?></td>
<!--ftheeten 2018 02 13 : add getTaxonName and reorganize layout-->
      <?php if($val->getUnitType()=="specimens") : ?>
        <td>
			<a target="_blank" href="<?php echo url_for('specimen/edit?id='.$val->getSpecimenRef()) ?>"><?php echo __('Specimen'); ?> : <?php echo $val->Specimen->getName(); ?></a>
			</br>
			<?php echo $val->Specimen->getTaxonName(); ?>
		</td>
		<!--ftheeten 2015 09 10-->
		<td>
				<?php echo ucfirst($val->Specimen->getSpecimenCreationDate())?'Date created: '.$val->Specimen->getSpecimenCreationDate():'';?>
	    </td>
		
      <?php endif ; ?>
    
    <td>
    </td>
  </tr>
  <?php endforeach;?>
<?php endif;?>
</table>
<?php echo $form['SpecimensRelationships_holder'];?>
<script  type="text/javascript">
$(document).ready(function () {
    $('#add_relship').click(function()
    {
        hideForRefresh('#SpecimensRelationships');
        parent_el = $(this).closest('table.property_values');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ (0+$(parent_el).find('tbody').length),
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead:hidden').show();
            showAfterRefresh('#SpecimensRelationships');
          }
        });
        return false;
    });
});
</script>