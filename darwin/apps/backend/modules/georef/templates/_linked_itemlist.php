<table class="list_gtu">
	<thead style="<?php echo ($form['GtuToGeoref']->count() || $form['newGtuToGeoref']->count())?'':'display: none;';?>" class="GtuToGeoref_head"></thead>
	  <tbody class="body_country_iso">
                <?php $retainedKey = 0;?>
              <?php foreach($form['GtuToGeoref'] as $form_value):?>
                <?php include_partial('linked_item_gtu', array('form' => $form_value, 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
              <?php foreach($form['newGtuToGeoref'] as $form_value):?>
                <?php include_partial('linked_item_gtu', array('form' => $form_value, 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
            </tbody>
</table>