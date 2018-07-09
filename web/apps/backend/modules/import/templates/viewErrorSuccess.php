<?php slot('title', __('Errors occured during import'));  ?>

<div class="page" id="stats" >
  <h1><?php echo __('Imports');?></h1>
   <h2><?php echo __("List of errors occured during import") ; ?></h2>

    <ul class="board_news">
   <?php if( $import->getFormat() == 'taxon'&& $import->getErrorsInImport()=='taxonomic_conflict' ) : ?>
    Taxo conflict
      <?php foreach($import->getTaxonomicConflicts() as $taxo_conflict) : ?>
      <li><ul>Taxonomic conflict for taxon <?php print($taxo_conflict["name"]);?> (<?php print($taxo_conflict["level_name"]);?>)
            <li>Hierarchy in source file : </li>
			<li>
			<?php $array_source=explode('/',$taxo_conflict["staging_catalogue_hierarchy"]);?>
			<ul>
			<?php foreach($array_source as $tmp_taxon) : ?>
				<?php if( strlen($tmp_taxon)>0 ) : ?>
					<li><?php print($tmp_taxon);?></li>
				<?php endif ?>
			<?php endforeach ;?>
			</ul>
			</li>
            <li>Existing hierarchy in Darwin : <?php print($taxo_conflict["darwin_hierarchy"]);?></li>
			<li>
			<?php $array_darwin=explode('/',$taxo_conflict["darwin_hierarchy"]);?>
			<ul>
			<?php foreach($array_darwin as $tmp_taxon) : ?>
				<?php if( strlen($tmp_taxon)>0 ) : ?>
					<li><?php print($tmp_taxon);?></li>
				<?php endif ?>
			<?php endforeach ;?>
			</ul>
			</li>
            <li>Search and modify in Darwin : <a href="../../../../index.php/taxonomy?name=<?php print($taxo_conflict["name"]);?>" target="_blank"><?php print($taxo_conflict["name"]);?></a></li>
      </ul>
      </li>
    <?php endforeach ; ?>
    </ul>
    <?php endif ;?>
    </ul>
    <div class="warn_message">
      <?php if($import->getFormat() == 'abcd') : ?>
        <?php echo __('warning_spec_msg');?>
      <?php else: ?>
        <?php echo __('warning_catalogue_msg');?>
      <?php endif ?>
    </div>
    <!--ftheeten test on taxonomy 2018 06 11-->
    
    <p>
    <!--ftheeten test on taxonomy 2018 03 22-->
      <?php if($import->getFormat() == 'abcd' || $import->getFormat() == 'taxon') : ?>
      <a href="<?php echo url_for('import/maj?id='.$id) ?>" class="bt_close"><?php echo __('Continue import');?></a>
      <?php endif ;?>
     
    </p>

    <hr />
    <?php if($import->getFormat() == 'taxon') : ?> 
      <p><?php echo link_to(__('Back'),'import/indexTaxon',array('class'=>'bt_close'));?></p>
    <?php else : ?>
      <p><?php echo link_to(__('Back'),'import/index',array('class'=>'bt_close'));?></p>
    <?php endif ; ?>

</div>

