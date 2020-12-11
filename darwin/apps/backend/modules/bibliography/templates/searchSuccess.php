<?php if($form->isValid()):?>
  <?php if(isset($bibliography) && $bibliography->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
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
		  <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=type'.( ($orderBy=='type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Type');?>
                <?php if($orderBy=='type') echo $orderSign ?>
              </a>
            </th>
		  <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=uri'.( ($orderBy=='type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('URI');?>
                <?php if($orderBy=='URI') echo $orderSign ?>
              </a>
            </th>            
			<th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=title'.( ($orderBy=='title' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Title');?>
                <?php if($orderBy=='title') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=reference'.( ($orderBy=='title' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Reference');?>
                <?php if($orderBy=='reference') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=year'.( ($orderBy=='year' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Year');?>
                <?php if($orderBy=='year') echo $orderSign ?>
              </a>
            </th>
			<th>
              
                <?php echo __('Authors');?>
               
            </th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($bibliography as $bib):?>
            <tr class="rid_<?php echo $bib->getId(); ?>">
              <td><?php echo $bib->getTypeFormatted();?></td>
			  <td class="item_name">
			  <?php print($bib->getUriProtocol());?>
			  <?php if(strtolower($bib->getUriProtocol())=="doi"):?>
			   <a href="https://dx.doi.org/<?php  print($bib->getUri()); ?>" target="_blank"><?php  print($bib->getUri()); ?></a>
			  <?php elseif(strtolower($bib->getUriProtocol())=="url"):?>
			   <a href="<?php  print($bib->getUri()); ?>" target="_blank"><?php  print($bib->getUri()); ?></a>
			 <?php else:?>
			 <?php>  <?php  print($bib->getUri()); ?></a>
			  
			 <?php endif;?></td>
              <td class="item_name  style="max-width:33%"><?php echo $bib->getTitle();?></td>
			  <td class="item_name" style="max-width:33%"><?php echo $bib->getReference();?></td>
              <td><?php echo $bib->getYear();?></td>
			  <td><?php echo $bib->getAuthors();?></td>
              <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
                  <?php if(! $is_choose):?>
                    <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                      <?php echo link_to(image_tag('edit.png',array('title'=>'Edit')),'bibliography/edit?id='.$bib->getId(), array('target'=>'_blank'));?>
                      <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate')),'bibliography/new?duplicate_id='.$bib->getId(), array('target'=>'_blank'));?>
                    <?php endif ; ?>
                    <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'bibliography/view?id='.$bib->getId(), array('target'=>'_blank'));?>               
                  <?php else:?>
                    <div class="result_choose"><?php echo __('Choose');?></div>
                  <?php endif;?>
               </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('No bibliography Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo __('Error with your criterions');?>
  </div>
<?php endif;?>
