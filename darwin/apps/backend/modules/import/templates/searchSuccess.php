<?php if($form->isValid()):?>
  <?php if(isset($imports) && $imports->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
    <div class="results_container">
      <table class="results">
        <thead>
          <tr>
		  <th><a class="sort" href="<?php echo url_for($s_url.'&orderby=id'.( ($orderBy=='id' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __("id");?>
                <?php  echo $orderSign ?>
              </a></th>
            <th></th>
            <?php if($format != 'taxon') : ?>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=name'.( ($orderBy=='name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Collection');?>
                <?php if($orderBy=='name') echo $orderSign ?>
              </a>
            </th>
          <?php endif ; ?>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=filename'.( ($orderBy=='filename' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Filename');?>
                <?php if($orderBy=='filename') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=state'.( ($orderBy=='state' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Status');?>
                <?php if($orderBy=='state') echo $orderSign ?>
              </a>
            </th>  
            <th class="datesNum">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=updated_at'.( ($orderBy=='updated_at' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Last modification');?>
                <?php if($orderBy=='updated_at') echo $orderSign ?>
              </a>
            </th>
            <th><?php echo __("Progression") ; ?></th>
            <?php if($format == 'abcd') : ?>
                <th ><?php echo __("View data") ; ?></th>
            <?php endif;?>
            <th colspan="4"><?php echo __("Actions") ; ?></th>
            
          </tr>
        </thead>
        <tbody>
          <?php foreach($imports as $import):?>
            <tr class="rid_<?php echo $import->getId(); ?>">
			<td><?php echo __($import->getId());?></td>
              <td></td>
              <?php if($format != 'taxon') : ?>
				<td><?php echo $import->Collections->getName();?></td>
			  <?php endif ; ?>
              <td><?php echo $import->getFilename();?></td>
              <td><?php echo __($import->getStateName());?></td>
              <td><?php echo $import->getLastModifiedDate(ESC_RAW);?></td>
              <td>
                <?php if(! in_array($import->getState(),array('loading','loaded','to_be_loaded','error')) ):?>
                  <?php if($format != 'taxon') : ?> 
                    <?php echo __('%rest% on %initial%',array('%rest%'=>$import->getInitialCount()-$import->getCurrentLineNum(), '%initial%'=>$import->getInitialCount() )) ;?>
                  <?php else : ?>
                    <?php echo __('%rest% on %initial%',array('%rest%'=>$import->getState()=='finished'?$import->getInitialCount():0, '%initial%'=>$import->getInitialCount() )) ;?>
                  <?php endif ; ?>
                <?php else:?>
                  <?php echo __('n/a');?>
                <?php endif;?>
              </td>
              <?php if($format == 'abcd') : ?>
                <td><a href="<?php print(url_for("specimensearch/search/1"))."?specimen_search_filters[import_ref]=".$import->getId()."&specimen_search_filters[rec_per_page]=50";?>" target="_blank">import n° <?php print($import->getId());?></a><td>
              <?php endif;?>
              <?php if ($import->getState() == 'error') : ?>
				<td colspan="2">
                  <?php echo link_to(image_tag('warning.png',array('title'=>__('View errors while importing'))),'import/viewError?id='.$import->getId());?>
				</td>
              <?php else : ?>
			      <!--2019 03 19-->
				  <?php if ($format == 'taxon'&& ($import->getState() == 'finished'||$import->isEditableState())) : ?>
                   <td><?php echo link_to(image_tag('edit.png',array('title'=>__('Edit import'))),'import/viewUnimportedTaxa?id='.$import->getId()); ?></td>
                  <?php endif;?>
				  <!--ftheeten 2017 08 29-->
                <?php if (($import->getWorking()) && !(trim($import->getStateName()) == "Finished")): ?>
                    <td>WORKING, PLEASE WAIT</td>
                <?php elseif ($import->isEditableState()) : ?>
					<td>
					 <!--ftheeten 2017 02 27 replace icon by name-->
						<?php echo link_to(__('Check import'),'staging/index?import='.$import->getId());?>
					</td>
						<!--ftheeten 2017 08 28-->
                <?php elseif($import->getState()==="to_be_loaded") : ?>
					<td>
						<?php if($format == 'files') : ?> 
							<?php echo link_to("Import files",'import/loadfiles?id='.$import->getId()); ?>
						<?php elseif($format == 'links') : ?> 
							<?php echo link_to("Import links",'import/loadlinks?id='.$import->getId()); ?>
						<?php else : ?> 
							<?php echo link_to("Load in staging",'import/loadstaging?id='.$import->getId()); ?>
						<?php endif ; ?> 
					</td>
						<!--ftheeten 2017 08 28-->
                <?php elseif($import->getState()==="loaded") : ?>
					<td>
						<?php echo link_to("Check import",'import/checkstaging?id='.$import->getId()); ?>
					</td>
					<!--ftheeten 2017 08 28 (end)-->
                <?php else : ?>
					<td/>
                <?php endif ; ?>
              
				<td>
                
                    <?php if (($import->getWorking()) && !(trim($import->getStateName()) == "Finished")): ?>
                            <td></td>
					<?php elseif ($import->isEditableState()) : ?>
					<!--ftheeten 2017 02 27 replace icon by name-->
						<?php echo link_to(__('Import "Ok" lines'),'staging/markok?import='.$import->getId());?>
					<?php endif ; ?>
				</td>
              <?php endif ; ?>
              <?php if (!in_array($import->getState(),array('apending','aprocessing','aloaded'))) : ?>
              <td>
                <?php if (!$import->getIsFinished()) : ?>
                  <?php echo link_to(image_tag('remove_2.png',array('title'=>__('Abort import'))),'import/clear?id='.$import->getId(),'class=remove_import');?>
                <?php endif;?>
              </td>
              <td>
                <?php echo link_to(image_tag('remove.png', array("title" => __("Delete"))), 'import/delete?id='.$import->getId(),'class=remove_import');?>
              </td>
             <?php else : ?>
             <td colspan="2">-</td>
             <?php endif ; ?>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>

<script language="javascript">
$(document).ready(function () {
  $('.remove_import').click(function(event)
  {
    event.preventDefault();
    if(confirm('<?php echo addslashes(__('All pending lines will be deleted, Are you sure ?'));?>'))
    {
      $.ajax({
        url: $(this).attr('href'),
        success: function(html)
        {
          if(html == "ok" )
          {
            $('#import_filter').submit();
          }
        }
      });
   }
  });
});
</script>
  <?php else:?>
    <?php echo __('No import Matching');?>
  <?php endif;?>
<?php else : ?>
<?php echo $form->renderGlobalErrors() ; ?>  
<?php endif ; ?>  
