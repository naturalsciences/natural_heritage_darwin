<?php slot('title', __('Upload file management page'));  ?>        

<div class="page">
  <h1 class="edit_mode"><?php echo __('Import ') ; 
	if($type=='abcd') 
	{
		echo __('Specimens') ; 
	}
	elseif($type=='locality')
	{
		echo __('Locality') ;
		
	}
    elseif($type=='lithostratigraphy')
	{
		echo __('Lithostratigraphy') ;
		
	}
	elseif($type=='files')
	{
		echo __('Files') ;
		
	}
	elseif($type=='links')
	{
		echo __('Links') ;
		
	}
	elseif($type=='synonymies')
	{
		echo __('Synonymies') ;
		
	}
	elseif($type=='taxon')
	{	
		echo __('Taxonomy') ;
	}?></h1>
  <?php include_partial('import/upload_file', array('form' => $form,'type' => $type)) ?>
</div>

